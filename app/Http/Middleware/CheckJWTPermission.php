<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJWTPermission
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

       if ($this->checkPermissionHelper($userAccess, $currentRoleList)) {
           return $next($request);
       }

       return response()->json(['error' => 'Unauthorized'], 403);
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
