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


    //SignUp Auth View

    public function signUp(){

        return view('Auth.SignUp');
    }
}
