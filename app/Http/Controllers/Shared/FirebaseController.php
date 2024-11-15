<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Firebase;
use Kreait\Firebase\Contract\Auth;


class FirebaseController extends Controller
{
    private $auth;

    public function __construct(Auth $auth) 
    {
        $this->auth = $auth;
    }

    public function createToken()
    {
        if (session()->has('firebase_token')) {
            $tokenData = session('firebase_token');
            
            if (time() - $tokenData['created_at'] < 3600) {
                return [
                    'token' => $tokenData['token'],
                    'claims' => $tokenData['claims'],
                    'from_session' => true
                ];
            }
        }

        try {
            $user = auth()->user();
            $claims = [
                'uid' => $user->id,
                'email' => $user->email, 
                'role' => $user->role
            ];
              
            $token = $this->auth->createCustomToken($user->id, $claims)->toString();
            session([
                'firebase_token' => [
                    'token' => $token,
                    'claims' => $claims,
                    'created_at' => time()
                ]
            ]);

            return [
                'token' => $token,
                'claims' => $claims,
                'from_session' => false
            ]; 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}