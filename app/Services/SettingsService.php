<?php

namespace App\Services;

use App\Exceptions\SettingsException;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Mail\VerifyUserMail;
use App\Mail\VerifyUserMailChange;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingsService
{
    public function changeSettings(UpdateSettingsRequest $request): array
    {
        try {
            $matchingAction = $request->getMatchingAction();
            $user = $request->attributes->get('user');

            $function = $matchingAction['action'];
            
            if (!method_exists($this, $function)) {
                throw new SettingsException("Action method '{$function}' not found");
            }

            $validatedData = $request->validated();
            $validatedData['user'] = $user;
            $result = $this->{$function}($validatedData);
            
            return [
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $result
            ];

        } catch (Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw new SettingsException($e->getMessage(), 0, $e);
        }
    }

    public function changeMailAction($token, $newEmail) {
        $route = null;
        try{
            $user = User::where('email_verified_token', $token)->first();
            if (!$user) return [
                'success' => false, 
                'message' => 'Your token is expired or missing.',
                'route' => null
            ];

            $existingUser = User::where('email', $newEmail)
                ->where('id', '!=', $user->id)
                ->first();
                
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'This email is already in use by another account.',
                    'route' => null
                ];
            }

            $user->update([
                'email' => $newEmail,
                'email_verified_at' => now(),
                'email_verified_token' => null
            ]);

            $route = strtolower($user->role).'.signin.view';

            return ['success' => true, 'message' => 'Your email has been successfully changed. Please login!', 'route' => $route];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to change your mail', 'route' => $route];
        }
    }

    public function changeEmail(array $request) {
        $user = $request['user'];
        $token = generateToken();

        $existingUser = User::where('email', $request['newEmail'])
                ->first();
                
            if ($existingUser) {
                throw new Exception("An existing user has this email!");
            }

        $user->update([
            'email_verified_at' => null,
            'email_verified_token' => $token
        ]);
        Mail::to($user->email)->queue(new VerifyUserMailChange($user, $token, $request['newEmail']));
        return [
            'message' => 'Please verify your new email address'
        ];
    }

    public function comparePassword(array $request) {

        $user = $request['user'];
        if (Auth::attempt([
            'email' => $user->email,
            'password' =>  $request['currentPassword']
        ])) {
            return [
                'message' => 'Password compared and matched!'
            ];
        } else {
            throw new SettingsException("Password is not correct!");
        };
       
    }

    public function changePassword(array $request) {
        $user = $request['user'];
        $user->password = $request['newPassword'];
        $user->save();
        return [
            'message' => 'New Password is saved!'
        ];
    }

    public function changeRecoveryEmail(array $request) {
        $user = $request['user'];
        $user->recovery_email = $request['newRecoveryEmail'];
        $user->save();
        return [
            'message' => 'Your recovery email is saved!'
        ];
    }


    

}