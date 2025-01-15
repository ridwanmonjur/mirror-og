<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyUserMail;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use ErrorException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Services\AuthService;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function handleGoogleCallback(Request $request)
    {
        $state = decrypt(request('state'));
        $role = $state['role'];
        $user = Socialite::driver('google')->stateless()->user();

        ['finduser' => $finduser, 'error' => $error]
            = $this->authService->registerOrLoginUserForSocialAuth($user, 'google', $role);
        Session::forget('role');
        
        return $this->authService->handleUserRedirection($finduser, $error, $role);
    }

    public function handleSteamCallback()
    {
        $user = Socialite::driver('steam')->user();
        $role = Session::get('role');
        ['finduser' => $finduser, 'error' => $error]
            = $this->authService->registerOrLoginUserForSocialAuth($user, 'steam', $role); 
        Session::forget('role');

        return $this->authService->handleUserRedirection($finduser, $error, $role);
    }

    

    // Steam login
    public function redirectToSteam(Request $request)
    {

        Session::put('role', $this->authService->putRoleInSessionBasedOnRoute($request->url()));
        Session::save();
        return Socialite::driver('steam')->redirect();
    }

    public function redirectToGoogle(Request $request)
    {
        $role = $this->authService->putRoleInSessionBasedOnRoute($request->url());
        Session::put('role', $role);
        Session::save();
        return Socialite::driver('google')
            ->with(['state' => encrypt(['role' => $role]) ])
            ->redirect();
    }    
   
    public function storeUser(Request $request)
    {
        $validatedData = [];
        $validationRules = [
            'username' => 'bail|required',
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6|max:24',
        ];
        
        extract($this->authService->determineUserRole($request));

        $redirectErrorRoute = $role.'.signup.view';
        $redirectSuccessRoute = $role.'.signin.view';
        
        DB::beginTransaction();
        $validatedData = $request->validate($validationRules);
        try {

            if ($role === 'organizer') {
                $this->authService->createUser($validatedData, $role);

                $organizer = new Organizer([
                    'user_id' => $user->id,
                    'companyDescription' => $validatedData['companyDescription'],
                    'companyName' => $validatedData['companyName'],
                ]);

                $organizer->save();
            } elseif ($role === 'participant') {

                $this->authService->createUser($validatedData, $role);
                $participant = new Participant([
                    'user_id' => $user->id,
                ]);

                $participant->save();
            }

            Mail::to($user->email)->queue(new VerifyUserMail($user, $token));

            DB::commit();

            return redirect()
                ->route($redirectSuccessRoute)
                ->with(
                    [
                        'success' => $roleFirstCapital.' account created and verification email sent. Please verify email now!',
                        'email' => $user->email,
                    ]
                );
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            if ($e->getCode() === '23000' || $e->getCode() === 1062) {
                return redirect()
                    ->route($redirectErrorRoute)
                    ->with('error', 'The email already exists. Add another email!');
            }

            return redirect()
                ->route($redirectErrorRoute)
                ->with('error', 'An error occurred while processing your request.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
                ->route($redirectErrorRoute)
                ->with('error', $th->getMessage());
        }
    }

    public function accessUser(Request $request)
    {
        $validatedData = [];
        extract($this->authService->determineUserRole($request));

        try {
            $validatedData = $request->validate([
                'email' => 'bail|required|email',
                'password' => 'bail|required|min:6|max:24',
            ]);

            if (Auth::attempt($validatedData)) {
                $user = User::where('email', $request->email)->first();
                if (! $user->email_verified_at) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Email not verified. Please verify email first!',
                        'errorEmail' => $request->email,
                    ]);
                }

                if ($user->role !== $roleCapital && $user->role !== 'ADMIN') {
                    throw new ErrorException("Invalid Role for {$roleFirstCapital}");
                }

                $request->session()->regenerate();

                return response()->json([
                    'message' => "Account signed in successfully as {$role}!",
                    'route' => route($role.'.home.view'),
                    'token' => null,
                    'success' => true,
                ], 201);
            }

            throw new ErrorException('The email or password you entered is incorrect!');
        } catch (QueryException $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => 'An error occurred while processing your request.'], 422);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }
    }

    public function logoutAction()
    {
        Auth::logout();

        return redirect('/home');
    }

    public function participantSignIn(Request $request)
    {
        if ($request->has('url')) {
            Session::put('intended', $request->input('url'));
            Session::save();
        }

        return view('Auth.ParticipantSignIn');
    }

    public function organizerSignIn(Request $request)
    {
        if ($request->has('url')) {
            Session::put('intended', $request->input('url'));
            Session::save();
        }

        return view('Auth.OrganizerSignIn');
    }

   
}
