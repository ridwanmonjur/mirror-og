<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //SignIn Auth View

    public function signIn(){

        return view('Auth.SignIn');
    }

    public function organizerSignIn(){
        return view('Auth.OrganizerSignIn');
    }


    //SignUp Auth View

    public function signUp(){

        return view('Auth.SignUp');
    }

    public function organizerSignUp(){

        return view('Auth.OrganizerSignUp');
    }

    public function storeOrganizer(Request $request)
    {
        // $post = new Post;
        // $post->title = $request->title;
        // $post->description = $request->description;
        // $post->save();
        return redirect('/organizerSignup')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    public function storeParticipant(Request $request)
    {
        // $post = new Post;
        // $post->title = $request->title;
        // $post->description = $request->description;
        // $post->save();
        return redirect('/signup')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    public function siginpOrganizer(Request $request)
    {
        // $post = new Post;
        // $post->title = $request->title;
        // $post->description = $request->description;
        // $post->save();
        return redirect('/organizerSignin')->with('status', 'Blog Post Form Data Has Been inserted');
    }

    public function signinParticipant(Request $request)
    {
        // $post = new Post;
        // $post->title = $request->title;
        // $post->description = $request->description;
        // $post->save();
        return redirect('/')->with('status', 'Blog Post Form Data Has Been inserted');
    }
}
