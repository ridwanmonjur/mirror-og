<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class AuthViewController extends Controller
{
    public function logoutAction()
    {
        Auth::logout();

        return redirect('/');
    }

    public function participantSignIn(Request $request)
    {
        if ($request->has('url')) {
            Session::put('intended', $request->input('url'));
            Session::save();
        }

        return view('Auth.ParticipantSignIn');
    }

    public function organizerSignIn(Request $request)
    {
        if ($request->has('url')) {
            Session::put('intended', $request->input('url'));
            Session::save();
        }

        return view('Auth.OrganizerSignIn');
    }

   
}
