<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function message(Request $request) {
        $userProfile = $error = null;
        $loggedUser =  $request->attributes->get('user');
        if ($request->has('userId')) {
            if ($loggedUser->id == $request->userId) {
                $error = "Please don't chat with yourself! Use a todo app instead.";
            }

            $userProfile = User::find($request->userId);
            if (!$userProfile) {
                $error = "No user exists by that name";
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
            'user' => $user, 'error' => $error
        ]);
    }

    public function getFirebaseUsers(Request $request) {
        
        $users = null;

        if ($request->has('userIdList'))  {
            $userIdList = $request->userIdList;

            $users = $users = DB::table('users')
                ->leftJoin('firebase_user_active_at', 'users.id', '=', 'firebase_user_active_at.user_id')
                ->whereIn('users.id', $userIdList)
                ->select('users.id', 'users.name', 'users.role', 'users.userBanner', 'firebase_user_active_at.updated_at')
                ->get();
        }   else if ($request->has('searchQ')) {
            $searchQ = $request->searchQ;
            $usersQ = User::select(['id', 'name', 'role', 'userBanner']);
            if ($searchQ) {
                $usersQ->where('name', 'LIKE', "%$searchQ%");
            }

            $users = $usersQ->paginate(5);
        }

        return response()->json(['data'=> $users, 'success' => true], 200);
    }
}
