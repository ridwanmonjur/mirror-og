<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailValidationRequest;
use App\Mail\SendBetaWelcomeMail;
use App\Mail\VerifyInterestedUserMail;
use App\Models\InterestedUser;
use App\Models\Participant;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BetaController extends Controller
{

    public function interestedAction(EmailValidationRequest $request): JsonResponse
    {
        try {
            $email = $request->validated()['email'];
            $isResend = $request->boolean('isResend');

            $user = DB::table(table: 'interested_user')
                ->where('email', $email)
                ->first();

            if (!$user) {
                $token = generateToken();

                DB::table('interested_user')->insert([
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'email_verified_token' => $token,
                ]);

                $this->sendVerificationEmail($email, $token);

                return response()->json([
                    'success' => true,
                    'message' => "A new verification email has been sent to your inbox",
                ]);
            } else {
                $isVerifiedUser = $user?->email_verified_at !== null;

                if ($isVerifiedUser) {
                    return response()->json(
                        [
                            'success' => false,
                            'error' => 'duplicate_verified', 
                            'message' => 'This email is already in use',
                        ],
                        422,
                    );
                }

                if ($isResend) {
                    try {
                        $token = generateToken();
                        DB::table('interested_user')
                            ->where('email', $email)
                            ->update([
                                'email_verified_token' => $token,
                                'updated_at' => now(),
                            ]);

                        $this->sendVerificationEmail($email, $token);

                        return response()->json([
                            'success' => true,
                            'message' => "A new verification email has been resent to your inbox",
                        ]);
                    } catch (Exception $e) {
                        return response()->json(
                            [
                                'success' => false,
                                'message' => 'Failed to resend verification email',
                            ],
                            500,
                        );
                    }
                } else {
                    return response()->json(
                        [
                            'success' => false,
                            'error' => 'duplicate_unverified',
                            'message' => 'Email exists but unverified',
                        ],
                        200,
                    );
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'server_error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function sendVerificationEmail($email, $token): void
    {
        Mail::to($email)->queue(new VerifyInterestedUserMail($email, $token));
    }

    public function verifyInterestedUser($token)
    {
        $user = DB::table(table: 'interested_user')->where('email_verified_token', $token)->first();
        if (!$user) {
            return view('Public.Verify')->with('error', 'Invalid verification token.');
        }

        if ($user->email_verified_at !== null) {
            return view('Public.Verify')
                ->with('success', 'verified_already')
                ->with('email', $user->email);
        }

        DB::query()
            ->from('interested_user')
            ->where('id', $user->id)
            ->update([
                'email_verified_at' => now(),
            ]);

        return view('Public.Verify')
            ->with('success', 'verified_now')
            ->with('email', $user->email);
    }

    public function viewOnboardBeta (Request $request) {
        $users = DB::table('interested_user')
            ->orderBy('id', 'desc')
            ->simplePaginate(50); 

        return view('admin.SendBetaUser', compact('users'));
    }

    public function postOnboardBeta (Request $request) {
        try {
            $interestedUsers = DB::table('interested_user')
                ->whereIn('id', $request->idList)
                ->get();
            
            $interestedUserEmail = [];

            foreach ($interestedUsers as $interestedUser) {
                $interestedUserEmail[] = $interestedUser->email;
            }

            $existingUsers = User::whereIn('email', $interestedUserEmail)
                ->get();
            
            foreach ($existingUsers as $user) {
                $password = generateToken(8);

                DB::table('interested_user')
                    ->where('email', $interestedUser->email)
                    ->update([
                        'pass_text' => $password,
                        'email_verified_at' => now()
                    ]);
                
                $user->password = Hash::make($password);
                $user->save();

                Mail::to($user)->queue(new SendBetaWelcomeMail($user, $password));
            }
            
            $existingUsersEmail = $existingUsers->pluck('email')->toArray();
            $newEmail = array_diff($interestedUserEmail, $existingUsersEmail);


            foreach ($newEmail as $email) {
                $username = explode('@', $email)[0];
                $username = strlen($username) > 5 ? substr($username, 0, 5) : $username;
                $password = generateToken(8);
                DB::table('interested_user')
                    ->where('email', $email)
                    ->update([
                            'pass_text' => $password,
                            'email_verified_at' => now()
                        ]
                    );

                $user = new User([
                    'email' => $email,
                    'password' => Hash::make($password),
                    'name' => generateToken(2) . $username . generateToken(2),
                    'role' => 'PARTICIPANT',
                    'created_at' => now(),
                    'email_verified_at' => now()
                ]);

                $user->save();
                $participant = new Participant([
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $participant->save();
                Mail::to($user)->queue(new SendBetaWelcomeMail($user, $password));
            }


            return back()->with('success', "Created / updated new users");
          
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());

        }
    }
}
