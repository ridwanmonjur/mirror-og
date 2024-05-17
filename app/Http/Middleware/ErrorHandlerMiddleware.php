<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Exception $e) {
            Log::error($e);
            $statusCode = null;
            $errorMessage = $e->getMessage();
            if (method_exists($e, 'getCode')) {
                $statusCode = $e->getCode();
            }

            if (is_null($statusCode)) {
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], $statusCode);
            } else {
                if ($request->is('admin/*')) {
                    $view = 'Participant.EventNotFound';
                } elseif ($request->is('participant/*')) {
                    $view = 'Participant.EventNotFound';
                } elseif ($request->is('organizer/*')) {
                    $view = 'Organizer.EventNotFound';
                } else {
                    $view = 'Participant.EventNotFound';
                }

                return response()->view($view, ['statusCode' => $statusCode, 'errorMessage' => $errorMessage], $statusCode);
            }
        }
    }
}
