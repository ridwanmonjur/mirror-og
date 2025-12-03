<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyUserMail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthResetAndVerifyController extends Controller
{
    public function createReset(Request $request)
    {
        return view('Auth.ResetPassword', ['token' => $request->token]);
    }

    public function storeReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|min:6',
        ]);

        if ($request->password !== $request->confirmPassword) {
            return back()->with(['error' => 'Password confirmation does not match.']);
        }

        $tokenData = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (! $tokenData) {
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
            DB::table('password_reset_tokens')->where('email', $tokenData->email)->delete();

            return view('Auth.ResetSuccess');
        }

        return back()->with(['error' => 'User not found.']);
    }

    public function storeForget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator);
        }

        $token = generateToken();
        $email = $request->email;
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with(['error' => 'User not found with this email.']);
        }

        DB::table('password_reset_tokens')->updateOrInsert(['email' => $request->email], ['token' => $token, 'expires_at' => Carbon::now()->addDay()]);

        Mail::to($email)->queue(new ResetPasswordMail(public_path('assets/images/DW_LOGO.png'), $token, $user->name));

        return back()->with('success', 'Password reset link sent. Please check your email.');
    }

    public function verifyAccount($token)
    {
        $user = User::where('email_verified_token', $token)->first();
        $role = $user->role;
        $route = strtolower($role).'.signin.view';
        if (! $user) {
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

        if (! $user) {
            return redirect()->back()->with('error', 'User not found with this email address.');
        }

        if ($user->email_verified_at !== null) {
            return redirect()->back()->with('info', 'Your account is already verified.');
        }

        $token = generateToken();
        $user->email_verified_token = $token;
        $user->save();

        Mail::to($user->email)->queue(new VerifyUserMail($user, $token));

        return redirect()->back()->with('success', 'Verification email has been sent. Please check your inbox.')->with('successEmail', $user->email);
    }
}
