<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'This is why we getting json error in fetch?'], 401);
        } elseif ($request->is('admin/*')) {
            session()->flash('error', 'Please log in as an admin.');

            return 'admin/login';
        } elseif ($request->is('participant/*')) {
            // dd(URL::current());
            Session::put('intended', URL::current());
            session()->flash('error', 'Please log in as a participant to access this page.');

            return route('participant.signin.view');
        } elseif ($request->is('organizer/*')) {
            Session::put('intended', URL::current());
            session()->flash('error', 'Please log in as an organizer to access this page.');

            return route('organizer.signin.view');
        } else {
            return route('participant.signin.view');
        }
    }
}
