<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Mail;
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

    public function storeReset(Request $request){
        
    }

    public function createReset(Request $request){
        return view('Auth.ResetPassword');
    }

    public function storeForget(Request $request){

    }

    public function createForget(Request $request){
        return view('Auth.ForgetPassword');
    }

    public function verifyAccount($token)
{
    $user = User::where('email_verified_token', $token)->first();
    $role = $user->role;
    $route = strtolower($role).".signin.view";
    if (!$user) {
        return redirect()->route($route)->with('error', 'Invalid verification token.');
    }

    if ($user->email_verified_at !== null) {
        return redirect()->route($route)->with('info', 'Your account is already verified.');
    }

    $user->email_verified_at = now(); 
    $user->email_verified_token = null; 
    $user->save();

    return redirect()->route($route)->with('success', 'Your account has been successfully verified.');
}
    
    

    public function verifyResend($email){
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found with this email address.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->back()->with('info', 'Your account is already verified.');
        }
        
        $token = $this->generateToken(); 
        $user->email_verified_token = $token;
        $user->save();

        Mail::send('Email.verify', ['token' => $token], function($message) use ($email){
            $message->to($email);
            $message->subject('Reset Password');
        });

        return redirect()->back()->with('success', 'Verification email has been sent. Please check your inbox.');
    }

    public function verifySuccess(Request $request){
        return view('Auth.VerifySuccess');
    }

    private function generateToken(){
        return Str::random(64);
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

            $token = $this->generateToken(); 
            $user->email_verified_token = $token;
            $user->save();

            Mail::send('Email.verify', ['token' => $token], function($message) use ($user){
                $message->to($user->email);
                $message->subject('Reset Password');
            });
+                       
            $organizer = new Organizer(
                [
                    "user_id" => $user->id,
                    "companyDescription" => $validatedData['companyDescription'],
                    "companyName" => $validatedData['companyName'],
                ]
            );
            
            $organizer->save();
            
            return redirect()->route('organizer.signin.view')->with(
                [
                    'success' => 'Organizer Account created and verification email sent. Now verify email!',
                    'email' => $user->email
                ]
            );
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
            $token = $this->generateToken(); 
            $user->email_verified_token = $token;
            $user->save();
            
            $participant = new Participant(
                [
                    "user_id" => $user->id,
                ]
            );

            Mail::send('Email.verify', ['token' => $token], function($message) use ($user){
                $message->to($user->email);
                $message->subject('Reset Password');
            });

            $participant->save();

            return redirect()->route('participant.signin.view')->with(
                [
                    'success' => 'Participant Account Created and verification email sent. Now verify email!',
                    'email' => $user->email
                ]
            );
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
