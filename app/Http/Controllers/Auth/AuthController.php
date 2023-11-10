<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventDetail;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    public function logoutAction()
    {
        Auth::logout();
        return redirect('/');
    }
    public function redirectToGoogle(Request $request)
    {
        // dd($request->all());
        // dd('redirected');
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // try {
        // dd($request->all());

        $user = Socialite::driver('google')->user();

        $finduser = User::where('google_id', $user->id)->first();

        if ($finduser) {

            Auth::login($finduser);

            return redirect()->route('participant.home.view');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt('123456dummy'),

            ]);
            $newUser->email_verified_at = now();
            $newUser->google_id = $user->id;
            $newUser->save();
            Auth::login($newUser);

            return redirect()->route('participant.home.view');
        }
        // } catch (Exception $e) {
        //     dd($e->getMessage());
        // }
    }

    // Steam login
    public function redirectToSteam()
    {
        return Socialite::driver('steam')->redirect();
    }

    // Steam callback
    public function handleSteamCallback()
    {
        $user = Socialite::driver('steam')->user();

        $this->_registerOrLoginUser($user);

        // Return home after login
        return redirect()->route('participant.home.view');
    }


    public function showLandingPage(Request $request)
    {

        $count = 4;
        $events = EventDetail::paginate($count);
        $output = compact("events");
        if ($request->ajax()) {
            $view = view(
                'LandingPageScroll',
                $output
            )->render();

            return response()->json(['html' => $view]);
        }
        return view(
            'LandingPage',
            $output
        );

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

        // dd($request->all());
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($request->password != $request->password_confirmation) {
            return back()
                ->with(['error' => 'Password confirmation does not match.']);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();


        if (!$tokenData) {
            return back()
                ->with(['error' => 'Invalid token or email address.']);
        }

        if (now() > $tokenData->expires_at) {
            return back()
                ->with(['error' => 'Token has expired. Please request a new password reset.']);
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

        return back()
            ->with(['error' => 'User not found.']);
    }

    public function createReset(Request $request)
    {
        return view('Auth.ResetPassword', ['token' => $request->token]);
    }

    public function storeForget(Request $request)
    { {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return back()
                    ->with('error', $validator);
            }

            $token = $this->generateToken();

            $email = $request->email;

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->with(['error' => 'User not found with this email.']);
            }

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => $token, 'expires_at' => Carbon::now()->addDay()]
            );

            Mail::send('Email.reset', ['token' => $token], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password');
            });

            // Redirect back with a success message
            return back()->with('success', 'Password reset link sent. Please check your email.');
        }
    }

    public function createForget(Request $request)
    {
        return view('Auth.ForgetPassword');
    }

    public function verifyAccount($token)
    {
        $user = User::where('email_verified_token', $token)->first();
        $role = $user->role;
        $route = strtolower($role) . ".signin.view";
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



    public function verifyResend($email)
    {
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

        Mail::send('Email.verify', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Password');
        });

        return redirect()->back()->with('success', 'Verification email has been sent. Please check your inbox.');
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
        $userRole = "";
        $userRoleCapital = "";
        $validatedData = [];
        if ($request->is("organizer/signup")) {
            $userRole = "organizer";
            $userRoleCapital = "ORGANIZER";
            $validatedData = $request->validate([
                'username' => 'baiL|required',
                'email' => "bail|required|email",
                'password' => "bail|required|min:6|max:24",
                "companyDescription" => 'bail|required',
                "companyName" => 'bail|required',
            ]);
        } else if ($request->is("participant/signup")) {
            $userRole = "participant";
            $userRoleCapital = "PARTICIPANT";
            $validatedData = $request->validate([
                'username' => 'baiL|required',
                'email' => "bail|required|email",
                'password' => "bail|required|min:6|max:24",
            ]);
        } else {
            return redirect()->route("landing.view");
        }

        $redirectErrorRoute = $userRole . ".signup.view";
        $redirectSuccessRoute = $userRole . ".signin.view";
        try {
            $user = new User(
                [
                    'name' => $validatedData['username'],
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                    "role" => $userRoleCapital,
                ]
            );

            $token = $this->generateToken();
            $user->email_verified_token = $token;
            $user->save();

            if ($userRole == "organizer") {
                $organizer = new Organizer(
                    [
                        "user_id" => $user->id,
                        "companyDescription" => $validatedData['companyDescription'],
                        "companyName" => $validatedData['companyName'],
                    ]
                );
                $organizer->save();
            } else if ($userRole == "participant") {
                $participant = new Participant(
                    [
                        "user_id" => $user->id,
                    ]
                );
                $participant->save();
            }

            Mail::send('Email.verify', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Reset Password');
            });

            return redirect()->route($redirectSuccessRoute)->with(
                [
                    'success' => 'Organizer Account created and verification email sent. Now verify email!',
                    'email' => $user->email
                ]
                // [
                //     'success' => $userRoleCapital . ' Account created successfully!',
                //     // 'email' => $user->email
                // ]
            );
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            if ($e->getCode() == '23000' || 1062 == $e->getCode()) {
                return redirect()->route($redirectErrorRoute)->with('error', 'The email already exists. Add another email!');
            } else {
                return redirect()->route($redirectErrorRoute)->with('error', 'An error occurred while processing your request.');
            }
        } catch (\Throwable $th) {
            return redirect()->route($redirectErrorRoute)->with('error', $th->getMessage());
        }
    }

    public function accessUser(Request $request)
    {
        $userRole = "";
        $userRoleCapital = "";
        $validatedData = [];
        if ($request->is("organizer/signin")) {
            $userRole = "organizer";
            $userRoleCapital = "ORGANIZER";
            $userRoleSentence = "Organizer";
        } else if ($request->is("participant/signin")) {
            $userRole = "participant";
            $userRoleCapital = "PARTICIPANT";
            $userRoleSentence = "Participant";
        } else {
            return redirect()->route("landing.view");
        }
        $redirectRoute = $userRole . ".signin.view";
        try {
            $validatedData = $request->validate([
                'email' => "bail|required|email",
                'password' => "bail|required|min:6|max:24",
            ]);

            if (Auth::attempt($validatedData)) {
                $user = Auth::getProvider()->retrieveByCredentials($validatedData);

                if (!$user->email_verified_at) {
                    return redirect()->back()->with([
                        'errorEmail' => $request->email,
                        'error' => 'Email not verified. Please verify email first!',
                    ]);
                }

                if ($user->role != $userRoleCapital && $user->role != "ADMIN") :
                    throw new \ErrorException("Invalid Role for $userRoleSentence");
                endif;

                $request->session()->regenerate();
                $route = $userRole . ".home.view";
                $message = 'Account signed in successfully as $userRole!';
                return redirect()->route($route)->with('success', $message);
            } else {
                throw new \ErrorException("The email or password you entered is incorrect!");
            }
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return redirect()->route($redirectRoute)->with('error', 'An error occurred while processing your request.');
        } catch (\Throwable $th) {
            return redirect()->route($redirectRoute)->with('error', $th->getMessage());
        }
    }


    private $mappingEventState = [
        'UPCOMING' => [
            'buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'ONGOING' => [
            'buttonBackgroundColor' => '#FFFBFB', 'buttonTextColor' => 'black', 'borderColor' => 'black'
        ],
        'DRAFT' => [
            'buttonBackgroundColor' => '#8CCD39', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
        'ENDED' => [
            'buttonBackgroundColor' => '#A6A6A6', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'
        ],
    ];



}
