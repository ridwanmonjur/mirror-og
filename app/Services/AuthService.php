<?php

namespace App\Services;

use App\Models\NotificationCounter;
use Illuminate\Support\Str;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService
{
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

        NotificationCounter::create(['user_id' => $user->id]);

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

    public function putRoleInSessionBasedOnRoute($url): ?string
    {
        if (strpos($url, 'organizer') !== false) {
            return 'ORGANIZER';
        } elseif (strpos($url, 'participant') !== false) {
            return 'PARTICIPANT';
        } else {
            return 'ADMIN';
        }
    }

    public function handleUserRedirection(?User $user, ?string $error, ?string $role)
    {
        $role = strtolower($role);

        if ($error) {
            return redirect()->route("$role.signin.view")->with('error', $error);
        }

        if (! $user) {
            return redirect()->route("$role.signin.view")->with('key', 'User does not exist!');
        }

        if ($user->role === 'PARTICIPANT') {
            return redirect()->route('participant.home.view');
        }

        return redirect()->route('organizer.home.view');
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
            if ($finduser->role != $role) {
                return ['finduser' => null, 'error' =>  sprintf('Only %ss can sign in with this route.', strtolower($role))];
            }

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

        DB::beginTransaction();

        try {
            $uniqueName = $user->name.'_'.date('mdHi');

            $newUser = User::create([
                'name' => $uniqueName,
                'email' => $user->email,
                'password' => Str::random(13),
                'role' => strtoupper($role),
                'created_at' => DB::raw('NOW()'),
            ]);

            NotificationCounter::create(['user_id' => $newUser->id]);

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
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return ['finduser' => $newUser, 'error' => null];
    }
}
