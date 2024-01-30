<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function logoutAction()
    {
        Auth::logout();
        return redirect('/');
    }

    protected function _registerOrLoginUser($user, $type)
    {
        if ($type == 'google') {
            $finduser = User::where('google_id', $user->id)->first();
        } elseif ($type == 'steam') {
            $finduser = User::where('steam_id', $user->id)->first();
        }

        if ($finduser) {
            Auth::login($finduser);
            return $user;
        } else {
            $finduser = User::where('email', $user->email)->first();
        
            if ($finduser) {
                
                if ($type == 'google') {
                    $finduser->google_id = $user->id;
                } elseif ($type == 'steam') {
                    $finduser->steam_id = $user->id;
                }
                
                $finduser->email_verified_at = now();
                Auth::login($finduser);
                $finduser->save();
                return $finduser;
            } else {
                
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => bcrypt(Str::random(13)),
                ]);
                $newUser->email_verified_at = now();
                
                if ($type == 'google') {
                    $newUser->google_id = $user->id;
                } elseif ($type == 'steam') {
                    $newUser->steam_id = $user->id;
                }
                
                $newUser->save();
                Auth::login($newUser);
                return $newUser;
            }
        }
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        try {
            $finduser = $this->_registerOrLoginUser($user, 'google');
            dd($finduser);

            if ($finduser->role == 'PARTICIPANT') {
                return redirect()->route('participant.home.view');
            } elseif ($finduser->role == 'ORGANIZER' || $finduser->role == 'ADMIN') {
                return redirect()->route('organizer.home.view');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Steam login
    public function redirectToSteam(Request $request)
    {
        // $routeName = $request->route()->getName();
        // if ($routeName == 'organizer.steam.login') {
            
        // } else {

        // }
        // dd($routeName);
        return Socialite::driver('steam')->redirect();
    }

    public function redirectToGoogle(Request $request)
    {
        $routeName = $request->route()->getName();
        dd($routeName);
        // return Socialite::driver('google')->redirect();
    }


    // Steam callback
    public function handleSteamCallback()
    {
        $user = Socialite::driver('steam')->stateless()->user();
        $finduser = $this->_registerOrLoginUser($user, 'steam');
        
        if ($finduser->role == 'PARTICIPANT') {
            return redirect()->route('participant.home.view');
        } elseif ($finduser->role == 'ORGANIZER' || $finduser->role == 'ADMIN') {
            return redirect()->route('organizer.home.view');
        }
    }

    public function showLandingPage(Request $request)
    {
        $count = 6;
        $currentDateTime = Carbon::now()->utc();
        
        $events = EventDetail::with('game')
            ->where('status', '<>', 'DRAFT')
            ->whereNotNull('payment_transaction_id')
            ->whereRaw('CONCAT(endDate, " ", endTime) > ?', [$currentDateTime])
            ->where('sub_action_private', '<>', 'private')
            ->where(function ($query) use ($currentDateTime) {
                $query
                    ->whereRaw('CONCAT(sub_action_public_time, " ", sub_action_public_date) < ?', [$currentDateTime])
                    ->orWhereNull('sub_action_public_time')
                    ->orWhereNull('sub_action_public_date');
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                if (empty($search)) {
                    return $query;
                }
                return $query->where('eventName', 'LIKE', "%{$search}%")->orWhere('eventDefinitions', 'LIKE', "%{$search}%");
            })
            ->with('tier', 'type', 'game')
            ->paginate($count);
        
            $mappingEventState = EventDetail::mappingEventStateResolve();
        $output = compact('events', 'mappingEventState');
        
        if ($request->ajax()) {
            // dd($events);
            $view = view('LandingPageScroll', $output)->render();
            return response()->json(['html' => $view]);
        }
        
        return view('LandingPage', $output);
    }

    public function signIn(Request $request)
    {
        return view('Auth.SignIn');
    }

    public function organizerSignIn(Request $request)
    {
        return view('Auth.OrganizerSignIn');
    }

    public function storeReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($request->password != $request->password_confirmation) {
            return back()->with(['error' => 'Password confirmation does not match.']);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return back()->with(['error' => 'Invalid token or email address.']);
        }

        if (now() > $tokenData->expires_at) {
            return back()->with(['error' => 'Token has expired. Please request a new password reset.']);
        }

        $user = User::where('email', $tokenData->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            // dd($user);
            DB::table('password_reset_tokens')
                ->where('email', $tokenData->email)
                ->delete();

            return view('Auth.ResetSuccess');
        }

        return back()->with(['error' => 'User not found.']);
    }

    public function createReset(Request $request)
    {
        return view('Auth.ResetPassword', ['token' => $request->token]);
    }

    public function storeForget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator);
        }

        $token = $this->generateToken();

        $email = $request->email;

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with(['error' => 'User not found with this email.']);
        }

        DB::table('password_reset_tokens')->updateOrInsert(['email' => $request->email], ['token' => $token, 'expires_at' => Carbon::now()->addDay()]);

        Mail::send('Email.reset', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Password');
        });

        return back()->with('success', 'Password reset link sent. Please check your email.');
    }

    public function createForget(Request $request)
    {
        return view('Auth.ForgetPassword');
    }

    public function verifyAccount($token)
    {
        $user = User::where('email_verified_token', $token)->first();
        $role = $user->role;
        $route = strtolower($role) . '.signin.view';
        if (!$user) {
            return redirect()
                ->route($route)
                ->with('error', 'Invalid verification token.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()
                ->route($route)
                ->with('info', 'Your account is already verified.');
        }

        $user->email_verified_at = now();
        $user->email_verified_token = null;
        $user->save();

        return redirect()
            ->route($route)
            ->with('success', 'Your account has been successfully verified.');
    }

    public function verifyResend($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()
                ->back()
                ->with('error', 'User not found with this email address.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()
                ->back()
                ->with('info', 'Your account is already verified.');
        }

        $token = $this->generateToken();
        $user->email_verified_token = $token;
        $user->save();

        Mail::send('Email.verify', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Verify Password');
        });

        return redirect()
            ->back()
            ->with('success', 'Verification email has been sent. Please check your inbox.');
    }

    public function verifySuccess(Request $request)
    {
        return view('Auth.VerifySuccess');
    }

    private function generateToken()
    {
        return Str::random(64);
    }

    //SignUp Auth View

    public function signUp(Request $request)
    {
        return view('Auth.SignUp');
    }

    public function organizerSignUp(Request $request)
    {
        return view('Auth.OrganizerSignUp');
    }

    public function storeUser(Request $request)
    {
        $userRole = '';
        $userRoleCapital = '';
        $validatedData = [];
        
        if ($request->is('organizer/signup')) {          
            $userRole = 'organizer';
            $userRoleCapital = 'ORGANIZER';
            
            $validatedData = $request->validate([
                'username' => 'baiL|required',
                'email' => 'bail|required|email',
                'password' => 'bail|required|min:6|max:24',
                'companyDescription' => 'bail|required',
                'companyName' => 'bail|required',
            ]);
        } elseif ($request->is('participant/signup')) {
            $userRole = 'participant';
            $userRoleCapital = 'PARTICIPANT';
            
            $validatedData = $request->validate([
                'username' => 'baiL|required',
                'email' => 'bail|required|email',
                'password' => 'bail|required|min:6|max:24',
            ]);
        } else {
            return redirect()->route('landing.view');
        }

        $redirectErrorRoute = $userRole . '.signup.view';
        $redirectSuccessRoute = $userRole . '.signin.view';
        
        try {
            $user = new User([
                'name' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'role' => $userRoleCapital,
            ]);

            $token = $this->generateToken();
            $user->email_verified_token = $token;
            $user->save();

            if ($userRole == 'organizer') {
                
                $organizer = new Organizer([
                    'user_id' => $user->id,
                    'companyDescription' => $validatedData['companyDescription'],
                    'companyName' => $validatedData['companyName'],
                ]);

                $organizer->save();
            } elseif ($userRole == 'participant') {
                
                $participant = new Participant([
                    'user_id' => $user->id,
                ]);
                
                $participant->save();
            }

            Mail::send('Email.verify', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Email verification');
            });

            return redirect()
                ->route($redirectSuccessRoute)
                ->with(
                    [
                        'success' => 'Organizer Account created and verification email sent. Now verify email!',
                        'email' => $user->email,
                    ],
                    // [
                    //     'success' => $userRoleCapital . ' Account created successfully!',
                    //     // 'email' => $user->email
                    // ]
                );
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            if ($e->getCode() == '23000' || 1062 == $e->getCode()) {
                return redirect()
                    ->route($redirectErrorRoute)
                    ->with('error', 'The email already exists. Add another email!');
            } else {
                return redirect()
                    ->route($redirectErrorRoute)
                    ->with('error', 'An error occurred while processing your request.');
            }

        } catch (\Throwable $th) {
            return redirect()
                ->route($redirectErrorRoute)
                ->with('error', $th->getMessage());
        }
    }

    public function accessUser(Request $request)
    {
        $userRole = '';
        $userRoleCapital = '';
        $validatedData = [];
        
        if ($request->is('organizer/signin')) {
            $userRole = 'organizer';
            $userRoleCapital = 'ORGANIZER';
            $userRoleSentence = 'Organizer';
        } elseif ($request->is('participant/signin')) {
            $userRole = 'participant';
            $userRoleCapital = 'PARTICIPANT';
            $userRoleSentence = 'Participant';
        } else {
            return redirect()->route('landing.view');
        }

        $redirectRoute = $userRole . '.signin.view';
        
        try {
            $validatedData = $request->validate([
                'email' => 'bail|required|email',
                'password' => 'bail|required|min:6|max:24',
            ]);

            if (Auth::attempt($validatedData)) {
                $user = Auth::getProvider()->retrieveByCredentials($validatedData);

                if (!$user->email_verified_at) {
                    return redirect()
                        ->back()
                        ->with([
                            'errorEmail' => $request->email,
                            'error' => 'Email not verified. Please verify email first!',
                        ]);
                }

                if ($user->role != $userRoleCapital && $user->role != 'ADMIN') :
                    throw new \ErrorException("Invalid Role for $userRoleSentence");
                endif;

                $request->session()->regenerate();
                $route = $userRole . '.home.view';
                $message = 'Account signed in successfully as $userRole!';
                
                return redirect()
                    ->route($route)
                    ->with('success', $message);
            } else {
                throw new \ErrorException('The email or password you entered is incorrect!');
            }
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()
                ->route($redirectRoute)
                ->with('error', 'An error occurred while processing your request.');
        } catch (\Throwable $th) {
            return redirect()
                ->route($redirectRoute)
                ->with('error', $th->getMessage());
        }
    }
}
