<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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
        if ($this->checkPermissionHelper($userAccess, $currentRoleList)) {
            return $next($request);
        }

        view()->share('user', $user);
        return response()->view('Auth.SignIn')
            ->withException(new \Exception('You do not have permission to access this page'));
    }
    private  function checkPermissionHelper($userAccess, $currentRoleList)
    {
        foreach ($currentRoleList as $key => $value) {
            if ($value == $userAccess) {
                return true;
            }
        }

        return false;
    }
}