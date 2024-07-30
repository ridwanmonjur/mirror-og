<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $currentRoleListString): Response
    {
        $currentRoleList = explode('|', $currentRoleListString);
        $user = auth()->user();
        $userAccess = strtolower($user->role);
        $request->attributes->add(['user' => $user]);
        view()->share('user', $user);

        if ($this->checkPermissionHelper($userAccess, $currentRoleList)) {
            return $next($request);
        }

        return response()
            ->view('Auth.ParticipantSignIn')
            ->withException(new \Exception('You do not have permission to access this page'));
    }

    private function checkPermissionHelper($userAccess, $currentRoleList)
    {
        foreach ($currentRoleList as $key => $value) {

            if ($value === $userAccess) {
                return true;
            }
        }

        return false;
    }
}
