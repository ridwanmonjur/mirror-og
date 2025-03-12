<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        $userProfile = null;
        $loggedUser = $request->attributes->get('user');

        // dd($loggedUser ->id, $request->userId);
        if ($request->has('userId')) {
            if ($loggedUser->id == $request->userId) {
                return $this->showErrorParticipant("Please don't chat with yourself! Use a todo app instead.");
            }

            $userProfile = User::find($request->userId);
            if (! $userProfile) {
                return $this->showErrorParticipant('We have no user by this id!');
            }
        }

        $user = User::select(['id', 'name', 'role', 'userBanner'])->findOrFail(
            $loggedUser->id
        );

        DB::table('firebase_user_active_at')->updateOrInsert(
            ['user_id' => $loggedUser->id],
            ['updated_at' => now()]
        );

        return view('Shared.Message', ['userProfile' => $userProfile,
            'user' => $user,
        ]);
    }

    public function getFirebaseUsers(Request $request)
    {
        $users = null;
        $loggedUserId = $request->attributes->get('user')->id;

        $select = [
            'users.id', 'users.name', 'users.role', 'users.userBanner', 
        ];

        if ($request->has('userIdList')) {
            $userIdList = $request->userIdList;

            $users = $users = DB::table('users')
                ->leftJoin('firebase_user_active_at', 'users.id', '=', 'firebase_user_active_at.user_id')
                ->leftJoin('blocks', function($join) use ($loggedUserId) {
                    $join->on('users.id', '=', 'blocks.blocked_user_id')
                         ->where('blocks.user_id', $loggedUserId)
                    ->orOn('users.id', '=', 'blocks.user_id')
                         ->where('blocks.blocked_user_id', $loggedUserId);
                })
                ->whereIn('users.id', $userIdList)
                ->select($select)
                ->addSelect([
                    'firebase_user_active_at.updated_at',
                   
                ])
                ->get();
        } elseif ($request->has('searchQ')) {
            $searchQ = $request->searchQ;
            $usersQ = User::select($select);
            if ($searchQ) {
                $usersQ->where('name', 'LIKE', "%{$searchQ}%");
            }

            $users = $usersQ->paginate(5);
        }

        return response()->json(['data' => $users, 'success' => true], 200);
    }
}
