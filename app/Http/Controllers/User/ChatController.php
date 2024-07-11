<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request) {
        $userProfile = null;
        if ($request->has('id')) {
            $userProfile = User::findOrFail($request->id);
        }

        $user = User::select(['id', 'name', 'role', 'userBanner'])->findOrFail(
            $request->attributes->get('user')
                ->id
        );

        return view('Shared.Message', ['userProfile' => $userProfile, 'user' => $user]);
    }

    public function getFirebaseUsers(Request $request) {
        $userIdList = $request->userIdList;
        $users = User::whereIn('id', $userIdList)
            ->select(['id', 'name', 'role', 'userBanner'])
            ->get();
        return response()->json(['data'=> $users, 'success' => true], 200);
    }
}
