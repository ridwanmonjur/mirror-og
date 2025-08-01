<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\FriendUpdateRequest;
use App\Http\Requests\User\OrganizerFollowRequest;
use App\Models\Friend;
use App\Models\OrganizerFollow;
use App\Models\TeamFollow;
use App\Models\ParticipantFollow;
use App\Models\Report;
use App\Models\User;
use App\Services\SocialService;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function __construct(private SocialService $socialService) {}

    public function followOrganizer(OrganizerFollowRequest $request)
    {
        try {
            $result = $this->socialService->handleOrganizerFollowsAndActivityLogs($request->attributes->get('user'), $request->organizer);

            return response()->json($result, 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to process follow/unfollow action',
                ],
                500,
            );
        }
    }

    public function updateFriend(FriendUpdateRequest $request)
    {
        try {
            $user = $request->attributes->get('user');

            $result = $this->socialService->handleFriendOperation($user, $request->toArray());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message'],
                ]);
            }

            session()->flash($result['type'], $result['message']);

            return back();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ],
                    500,
                );
            }

            return $this->showErrorParticipant($e->getMessage());
        }
    }

    public function followParticipant(Request $request)
    {
        $result = $this->socialService->handleParticipantFollow($request);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            session()->flash('successMessage', $result['message']);
        } else {
            session()->flash('errorMessage', $result['message']);
        }

        return back();
    }

    public function getConnections(Request $request, $id)
    {
        $type = $request->input('type', 'all');
        $role = $request->input('role', 'ORGANIZER');
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $perPage = 6;
        $response = [];

        if ($type === 'all') {
            if ($role == 'PARTICIPANT') {
                $response['count'] = [
                    'followers' => ParticipantFollow::getFollowerCount($id),
                    'following' => ParticipantFollow::getFollowingCount($id),
                    'friends' => Friend::getFriendCount($id),
                ];
            }
        } else {
            if ($request->has('loggedUserId')) {
                $loggedUser = User::where('id', $request->input('loggedUserId'))
                    ->select(['id', 'role'])
                    ->firstOrFail();

                $loggedUserId = $loggedUser?->id;
            } else {
                $loggedUserId = null;
            }

            $followers = null;
            if ($role === 'ORGANIZER') {
                $followers = OrganizerFollow::getFollowersPaginate($id, $loggedUserId, $perPage, $page, $search);
            } elseif ($role === 'PARTICIPANT') {
                $followers = ParticipantFollow::getFollowersPaginate($id, $loggedUserId, $perPage, $page, $search);
            } else {
                $followers = TeamFollow::getFollowersPaginate($id, $loggedUserId, $perPage, $page, $search);
            }

            $data = match ($type) {
                'followers' => $followers,
                'following' => ParticipantFollow::getFollowingPaginate($id, $loggedUserId, $perPage, $page, $search),
                'friends' => Friend::getFriendsPaginate($id, $loggedUserId, $perPage, $page, $search),
                default => throw new \InvalidArgumentException('Invalid connection type'),
            };
            $response['connections'] = [$type => $data];
        }

        return response()->json($response);
    }

    public function toggleStar(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)->select('id')->first();

        if ($authenticatedUser->hasStarred($user)) {
            $authenticatedUser->stars()->detach($user);
            $message = 'User unstarred successfully';
        } else {
            $authenticatedUser->stars()->attach($user);
            $message = 'User starred successfully';
        }

        return response()->json([
            'message' => $message,
            'is_starred' => $authenticatedUser->hasStarred($user),
        ]);
    }

    public function report(Request $request, $id): JsonResponse
    {
        $authenticatedUser = $request->attributes->get('user');
        $user = User::where('id', $id)->select('id')->first();

        if ($authenticatedUser->id == $user->id) {
            return response()->json(
                [
                    'message' => "Can't report yourself",
                    'is_blocked' => 'False',
                ],
                404,
            );
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $report = Report::create([
            'reporter_id' => $authenticatedUser->id,
            'reported_user_id' => $user->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(
            [
                'message' => 'Report submitted successfully',
                'report' => $report,
            ],
            201,
        );
    }

    public function getReports(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $reports = Report::where('reported_user_id', $user->id)
            ->with('reporter:id,name') // Optionally include reporter details
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'reports' => $reports,
        ]);
    }
}
