<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        else if ($request->is('admin/*')) {
            return "admin/login";
        }
        else if ($request->is('participant/*')) {
            return route('participant.signin.view');
        }
        else if ($request->is('organizer/*')) {
            return route('organizer.signin.view');
        }
        else {
            return route('participant.signin.view');
        }
    }
}
