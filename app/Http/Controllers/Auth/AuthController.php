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

    private function handleUserRedirection(?User $user, ?string $error = null)
    {
        Session::forget('role');

        if ($error) {
            return view('Error', ['error' => $error]);
        }

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'PARTICIPANT') {
            return redirect()->route('participant.home.view');
        }

        return redirect()->route('organizer.home.view');
    }
  

    public function handleGoogleCallback(Request $request)
    {
        // @phpstan-ignore-next-line
        $user = Socialite::driver('google')->stateless()->user();
        $role = Session::get('role');
        // phpcs:enable

        ['finduser' => $finduser, 'error' => $error]
            = $this->authService->registerOrLoginUserForSocialAuth($user, 'google', $role);

        return $this->handleUserRedirection($finduser, $error);
    }

    public function handleSteamCallback()
    {
        $user = Socialite::driver('steam')->user();
        $role = Session::get('role');
        ['finduser' => $finduser, 'error' => $error]
            = $this->authService->registerOrLoginUserForSocialAuth($user, 'steam', $role);

        return $this->handleUserRedirection($finduser, $error);
    }

    private function putRoleInSessionBasedOnRoute($url): void {
        if (strpos($url, 'organizer') !== false) {
            Session::put('role', 'organizer');
        } elseif (strpos($url, 'participant') !== false) {
            Session::put('role', 'participant');
        }
    }

    // Steam login
    public function redirectToSteam(Request $request)
    {
        $this->putRoleInSessionBasedOnRoute($request->url());
        return Socialite::driver('steam')->redirect();
    }

    public function redirectToGoogle(Request $request)
    {
        $this->putRoleInSessionBasedOnRoute($request->url());
        return Socialite::driver('google')->redirect();
    }    
   


    public function storeUser(Request $request)
    {
        $validatedData = [];
        $validationRules = [
            'username' => 'baiL|required',
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6|max:24',
        ];
        
        extract($this->authService->determineUserRole($request));

        $redirectErrorRoute = $userRole.'.signup.view';
        $redirectSuccessRoute = $userRole.'.signin.view';
        
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                ...$validationRules,
                'companyDescription' => 'bail|required',
                'companyName' => 'bail|required',
            ]);

            if ($userRole === 'organizer') {
                $this->createUser($validatedData, $userRole);

                $organizer = new Organizer([
                    'user_id' => $user->id,
                    'companyDescription' => $validatedData['companyDescription'],
                    'companyName' => $validatedData['companyName'],
                ]);

                $organizer->save();
            } elseif ($userRole === 'participant') {
                $validatedData = $request->validate([
                    ...$validationRules,
                ]);

                $this->createUser($validatedData, $userRole);
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
                        'success' => $userRoleFirstCapital.' account created and verification email sent. Please verify email now!',
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

                if ($user->role !== $userRoleCapital && $user->role !== 'ADMIN') {
                    throw new ErrorException("Invalid Role for {$userRoleSentence}");
                }

                $request->session()->regenerate();

                return response()->json([
                    'message' => "Account signed in successfully as {$userRole}!",
                    'route' => route($userRole.'.home.view'),
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


   
}
