<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showErrorOrganizer($error, $args = [])
    {
        return view('Organizer.EventNotFound', [
            'error' => $error,
            ...$args,
        ]);
    }

    public function showErrorParticipant($error)
    {
        return view('Participant.EventNotFound', compact('error'));
    }

    public function handleErrorJson(\Exception $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Operation failed',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}
