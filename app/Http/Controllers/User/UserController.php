<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TeamProfile;
use App\Models\User;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function replaceBanner(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|array',
                'file.filename' => 'required|string',
                'file.type' => 'required|string',
                'file.size' => 'required|numeric',
                'file.content' => 'required|string',
            ]);

            $user = $request->attributes->get('user');
            $oldBanner = $user->userBanner;
            $fileName = $user->uploadUserBanner($request);
            $user->destroyUserBanner($oldBanner);

            return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => compact('fileName')], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function replaceBackground(Request $request)
    {
        try {
            $validated = $request->validate([
                'backgroundBanner' => 'nullable',
                'teamId' => 'nullable|exists:teams,id',
                'backgroundGradient' => 'nullable|string',
                'backgroundColor' => 'nullable|string',
                'fontColor' => 'nullable|string',
                'frameColor' => 'nullable|string',
            ]);

            $user = $request->attributes->get('user');
            if ($request->teamId) {
                $profile = TeamProfile::where('team_id', $request->teamId)->firstOrNew();
                $profile->team_id = $request->teamId;
                $oldBanner = $profile->backgroundBanner;
                if ($request->backgroundBanner) {
                    $user->uploadBackgroundBanner($request, $profile);
                } else {
                    $profile->fill($validated);
                    if ($profile->backgroundColor || $profile->backgroundGradient) {
                        $profile->backgroundBanner = null;
                    }

                    $profile->save();
                }

                $user->destroyUserBanner($oldBanner);
            } else {
                $profile = UserProfile::where('user_id', $user->id)->firstOrNew();
                $profile->user_id = $user->id;
                $oldBanner = $profile->backgroundBanner;
                if ($request->backgroundBanner) {
                    $user->uploadBackgroundBanner($request, $profile);
                } else {
                    $profile->fill($validated);
                    if ($profile->backgroundColor || $profile->backgroundGradient) {
                        $profile->backgroundBanner = null;
                    }

                    $profile->save();
                }

                $user->destroyUserBanner($oldBanner);
            }
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Succeeded', 'data' => $profile], 201);
            }

            return back();
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            session()->flash('errorMessage', $e->getMessage());

            return back();
        }
    }

    public function viewOnboardBeta (Request $request) {
        $users = DB::table('interested_user')
            ->orderBy('created_at', 'desc')
            ->paginate(50); 

        return view('Organizer.BetaUser', compact('users'));

    }

    public function postOnboardBeta (Request $request) {
        $users = DB::table('interested_user')
            ->where('id', $request->idList)
            ->get()
            ->keyBy('email'); 
        
        // $userList = new User([
        //     'name' => 'user' . '122222222222',
        //     'email' => $validatedData['email'],
        //     'password' => $validatedData['password'],
        //     'role' => $userRoleCapital,
        //     'created_at' => now(),
        // ]);
    }
}
