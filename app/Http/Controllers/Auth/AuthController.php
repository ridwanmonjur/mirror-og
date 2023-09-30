<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    //SignIn Auth 
    private function showAlert($session){
        if ($message = $session->get('success')){
            Alert::success('Success', $message);
        }
        if ($message = $session->get('error')){
            Alert::error('Error', 'Organizer Account Signed In Successfully!');
        }
    }

    public function showLandingPage(Request $request)
    {
        return view('LandingPage');
    }

    public function signIn(Request $request)
    {
        $this->showAlert($request->session());
        return view('Auth.SignIn');
    }

    public function organizerSignIn(Request $request)
    {
        $this->showAlert($request->session());
        return view('Auth.OrganizerSignIn');
    }


    //SignUp Auth View

    public function signUp(Request $request)
    {
        $this->showAlert($request->session());

        return view('Auth.SignUp');
    }

    public function organizerSignUp(Request $request)
    {

        return view('Auth.OrganizerSignUp');
    }

    public function storeOrganizer(Request $request)
    {
        try{
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
            return redirect()->route('organizer.signin.view')->with('success', 'Organizer Account Created Successfully. Now verify email!');
        } 
        catch (\Throwable $th) {
            return redirect()->route('organizer.signup.view')->with('error', $th->getMessage());
        }
        
    }

    public function storeParticipant(Request $request)
    {
        try{
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
            return redirect()->route('participant.signin.view')->with('success', 'Participant Account Created Successfully. Now verify email!');
        }
        catch (\Throwable $th) {
            return redirect()->route('participant.signup.view')->with('error', $th->getMessage());
        }
    }

    public function accessOrganizer(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'email' => "bail|required|email",
                'password' => "bail|required|min:6|max:24",
            ]);
            // dd($validatedData);
            if(Auth::attempt($validatedData))
            {
                $user = Auth::getProvider()->retrieveByCredentials($validatedData);
                if ($user->role != "ORGANIZER"):
                    throw new \ErrorException("Invalid Role for Organizer");
                endif;
                $request->session()->regenerate();
                return redirect()->route("organizer.signin.view")->with('success', 'Organizer Account Signed In Successfully!');
            }
            else{
                throw new \ErrorException("Can't find user!");
            }
        }
        catch (\Throwable $th) {
            return redirect()->route("organizer.signin.view")->with('error', $th->getMessage());
        }
    }

    public function accessParticipant(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'email' => "bail|required|email",
                'password' => "bail|required|min:6|max:24",
            ]);
            if(Auth::attempt($validatedData))
            {
                $user = Auth::getProvider()->retrieveByCredentials($validatedData);
                if ($user->role != "PARTICIPANT"):
                    throw new \ErrorException("Invalid Role for Participant");
                endif;
                $request->session()->regenerate();
            }
            else{
                throw new \ErrorException("Can't find user!");
            }
        }
        catch (\Throwable $th) {
            return redirect()->route("organizerSignin")->with('error', $th->getMessage());
        }
        return redirect()->route("organizerSignin")->with('success', 'Participant Account Signed In Successfully!');
    }
}
