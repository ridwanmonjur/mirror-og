<?php
// app/Traits/HandlesFilamentExceptions.php

namespace App\Filament\Traits;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

trait HandlesFilamentExceptions
{
    public function exception(Throwable $e, $stopPropagation)
    {
        // Log the detailed error for debugging
        Log::error('Filament operation failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'component' => static::class,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Get user-friendly error message
        $userMessage = $this->getUserFriendlyMessage($e);

        // Create notification that looks more like a popup
        $notification = Notification::make()
            ->title('Operation Failed')
            ->body($userMessage)
            ->danger()
            ->persistent()
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger');

        // Add technical details if in debug mode
        // if (config('app.debug')) {
        $notification->body($userMessage.'<br><br><b>Technical Details:</b><br> '.$e->getMessage());
        // }

        $notification->send();

        $stopPropagation();
    }

    /**
     * Get user-friendly error message based on exception type
     */
    private function getUserFriendlyMessage(Throwable $e): string
    {
        // Database related errors
        if ($e instanceof \Illuminate\Database\QueryException) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23000]')) {
                return 'This action cannot be completed because the record is being used elsewhere in the system.';
            }
            if (str_contains($e->getMessage(), 'SQLSTATE[23505]')) {
                return 'A record with this information already exists. Please use different values.';
            }

            return 'A database error occurred. Please try again or contact support if the problem persists.';
        }

        // Validation errors
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 'The provided data is invalid. Please check your input and try again.';
        }

        // Authorization errors
        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 'You do not have permission to perform this action.';
        }

        // File/Storage errors
        if ($e instanceof \Illuminate\Contracts\Filesystem\FileNotFoundException) {
            return 'The requested file could not be found.';
        }

        // Model not found errors
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 'The requested record could not be found. It may have been deleted or moved.';
        }

        // Network/HTTP errors
        if ($e instanceof \GuzzleHttp\Exception\ConnectException) {
            return 'Unable to connect to external service. Please check your internet connection and try again.';
        }

        // Timeout errors
        if ($e instanceof \Illuminate\Http\Client\RequestException) {
            return 'The request took too long to process. Please try again.';
        }

        // Default fallback message
        return 'An unexpected error occurred. Please try again or contact support if the problem persists.';
    }
}
