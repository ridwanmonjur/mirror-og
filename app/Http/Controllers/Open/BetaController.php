<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Mail\VerifyInterestedUserMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BetaController extends Controller
{
    public function interestedAction(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid request format',
                    ],
                    422,
                );
            }

            $email = $request->email;
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
            return view('VerifyInterestedUser')->with('error', 'Invalid verification token.');
        }

        if ($user->email_verified_at !== null) {
            return view('VerifyInterestedUser')
                ->with('success', 'verified_already')
                ->with('key', $user->email);
        }

        DB::query()
            ->from('interested_user')
            ->where('id', $user->id)
            ->update([
                'email_verified_at' => now(),
                'email_verified_token' => null,
            ]);

        return view('VerifyInterestedUser')
            ->with('success', 'verified_now')
            ->with('key', $user->email);;
    }
}
