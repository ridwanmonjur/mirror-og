<?php

namespace App\Services;

use App\Exceptions\SettingsException;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Mail\VerifyUserMail;
use App\Mail\VerifyUserMailChange;
use App\Models\PaymentTransaction;
use App\Models\StripePayment;
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

        } catch (SettingsException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            throw new SettingsException('Failed to update settings', 0, $e);
        }
    }

    public function changeEmail(array $request) {

        $user = $request['user'];
        $token = generateToken();

        $user->update([
            'email' => $request['newEmail'],
            'email_verified_at' => null,
            'email_verified_token' => $token
        ]);

        Mail::to($user->email)->queue(new VerifyUserMailChange($user, $token));

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