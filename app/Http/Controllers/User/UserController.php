<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TeamProfile;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\Request;

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
            // dd($user);
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

    public function showStats($id, Request $request)
    {
        return view('Shared.PlayerProfileStats', ['userId' => $id]);
    }
}
