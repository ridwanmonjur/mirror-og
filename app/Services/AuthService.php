<?php
namespace App\Services;

use Illuminate\Support\Str;
use App\Mail\VerifyUserMail;
use App\Mail\ResetPasswordMail;
use App\Models\EventDetail;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthService {
    
    public function createUser(array $validatedData, string $roleCapital): User
    {
        $user = new User([
            'name' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
            'role' => $roleCapital,
            'created_at' => now(),
        ]);

        $token = generateToken();
        $user->email_verified_token = $token;
        $user->save();

        return $user;
    }

    public function determineUserRole(Request $request): array|object
    {
        if ($request->is('organizer/*')) {
            return [
                'role' => 'organizer',
                'roleCapital' => 'ORGANIZER',
                'roleFirstCapital' => 'Organizer',
            ];
        }

        if ($request->is('participant/*')) {
            return [
                'role' => 'participant',
                'roleCapital' => 'PARTICIPANT',
                'roleFirstCapital' => 'Participant',
            ];
        }



        throw new \InvalidArgumentException('Invalid registration path');
    }

    public function registerOrLoginUserForSocialAuth($user, $type, $role)
    {
        $finduser = null;
        if ($type === 'google') {
            $finduser = User::where('google_id', $user->id)->first();
        } elseif ($type === 'steam') {
            $finduser = User::where('steam_id', $user->id)->first();
        }

        if ($finduser) {
            // if (!$user->user['email_verified']) {
            //     return ['finduser' => null, 'error' => 'Your Gmail is not verified'];
            // }

            Auth::login($finduser);

            return ['finduser' => $finduser, 'error' => null];
        }

        $finduser = User::where('email', $user->email)->first();

        if ($finduser) {
            if ($type === 'google') {
                $finduser->google_id = $user->id;
            } elseif ($type === 'steam') {
                $finduser->steam_id = $user->id;
            }

            $finduser->email_verified_at = now();
            Auth::login($finduser);
            $finduser->save();

            return ['finduser' => $finduser, 'error' => null];
        }

        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => bcrypt(Str::random(13)),
            'role' => strtoupper($role),
            'created_at' => now(),
        ]);

        if ($newUser->role === 'ORGANIZER') {
            $organizer = new Organizer([
                'user_id' => $newUser->id,
            ]);

            $organizer->save();
        } elseif ($newUser->role === 'PARTICIPANT') {
            $participant = new Participant([
                'user_id' => $newUser->id,
            ]);

            $participant->save();
        }

        $newUser->email_verified_at = now();

        if ($type === 'google') {
            $newUser->google_id = $user->id;
        } elseif ($type === 'steam') {
            $newUser->steam_id = $user->id;
        }

        $newUser->save();
        Auth::login($newUser);

        return ['finduser' => $newUser, 'error' => null];
    }
}