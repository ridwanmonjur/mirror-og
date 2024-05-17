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
            return response()->json(['error' => 'This is why we getting json error in fetch?'], 401);
        } elseif ($request->is('admin/*')) {
            return 'admin/login';
        } elseif ($request->is('participant/*')) {
            return route('participant.signin.view');
        } elseif ($request->is('organizer/*')) {
            return route('organizer.signin.view');
        } else {
            return route('participant.signin.view');
        }
    }
}
