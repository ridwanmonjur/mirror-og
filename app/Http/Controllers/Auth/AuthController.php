<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //SignIn Auth View

    public function signIn()
    {

        return view('Auth.SignIn');
    }

    public function organizerSignIn()
    {
        return view('Auth.OrganizerSignIn');
    }


    //SignUp Auth View

    public function signUp()
    {

        return view('Auth.SignUp');
    }

    public function organizerSignUp()
    {

        return view('Auth.OrganizerSignUp');
    }

    public function storeOrganizer(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'baiL|required',
            'email' => "bail|required|email",
            'password' => "bail|required|min:6|max:24",
            "companyDescription" => 'bail|required',
            "companyName" => 'bail|required',
        ]);
        $user = new User(
            [
                'name' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],

                "role" => "ORGANIZER",
            ]
        );
        $user->save();
        $organizer = new Organizer(
            [
                "user_id" => $user->id,
                "companyDescription" => $validatedData['companyDescription'],
                "companyName" => $validatedData['companyName'],
            ]
        );
        $organizer->save();
        // return redirect('/signup')->with('status', 'Blog Post Form Data Has Been inserted');
        return redirect('/organizerSignup')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    public function storeParticipant(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'baiL|required',
            'email' => "bail|required|email",
            'password' => "bail|required|min:6|max:24",
        ]);
        $user = new User(
            [
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                "role" => "PARTICIPANT",
            ]
        );
        $user->save();
        $participant = new Participant(
            [
                "user_id" => $user->id,
            ]
        );
        $participant->save();
        return redirect('/signup')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    public function accessOrganizer(Request $request)
    {
        $validatedData = $request->validate([
            'email' => "bail|required|email",
            'password' => "bail|required|min:6|max:24",
        ]);
        // dd($validatedData);
        if(Auth::attempt($validatedData))
        {
            $request->session()->regenerate();
            $user = Auth::getProvider()->retrieveByCredentials($validatedData);
            if ($user->role != "ORGANIZER"):
                throw new \ErrorException("Invalid Role for Organizer");
            endif;
            dd($user);
            $request->session()->regenerate();
            dd($request->session()->all());
        }
        dd("not logged in");
        return redirect('/organizerSignin');
    }

    public function accessParticipant(Request $request)
    {
        $validatedData = $request->validate([
            'email' => "bail|required|email",
            'password' => "bail|required|min:6|max:24",
        ]);
        if(Auth::attempt($validatedData))
        {
            $request->session()->regenerate();
            $user = Auth::getProvider()->retrieveByCredentials($validatedData);
            if ($user->role != "PARTICIPANT"):
                throw new \ErrorException("Invalid Role for Participant");
            endif;
            dd($user);
            $request->session()->regenerate();
            dd($request->session()->all());
        }
        dd("not logged in");
        return redirect('/');
    }
}
