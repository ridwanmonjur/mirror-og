<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandlePostTooLarge
{
    /**
     * Maximum allowed post size in bytes (20MB)
     */
    protected const MAX_POST_SIZE = 20 * 1024 * 1024; // 20MB

    /**
     * Handle an incoming request and catch PostTooLargeException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check post size before processing request
        if ($this->exceedsPostSizeLimit($request)) {
            Log::error('PostTooLargeException caught in middleware', [
                'url' => $request->url(),
                'method' => $request->method(),
                'content_length' => $request->header('Content-Length'),
                'max_post_size' => '20MB',
                'user_agent' => $request->header('User-Agent')
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'File too large. Please reduce file size and try again.',
                    'error' => 'The uploaded file exceeds the maximum allowed size of 20MB.',
                    'max_size' => '20MB'
                ], 413);
            }

            return back()->withErrors([
                'file' => 'The uploaded file is too large. Maximum size allowed is 20MB.'
            ])->withInput();
        }

        try {
            return $next($request);
        } catch (PostTooLargeException $e) {
            Log::error('PostTooLargeException caught in middleware', [
                'url' => $request->url(),
                'method' => $request->method(),
                'content_length' => $request->header('Content-Length'),
                'max_post_size' => '20MB',
                'user_agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'File too large. Please reduce file size and try again.',
                    'error' => 'The uploaded file exceeds the maximum allowed size of 20MB.',
                    'max_size' => '20MB'
                ], 413);
            }

            return back()->withErrors([
                'file' => 'The uploaded file is too large. Maximum size allowed is 20MB.'
            ])->withInput();
        }
    }

    /**
     * Check if the request exceeds the post size limit
     */
    protected function exceedsPostSizeLimit(Request $request): bool
    {
        $contentLength = $request->header('Content-Length');
        
        return $contentLength && $contentLength > self::MAX_POST_SIZE;
    }

}