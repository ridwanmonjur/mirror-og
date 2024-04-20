<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function show404Organizer($error, $args = []) {
        return view('Organizer.EventNotFound', [
            'error' => $error,
            ...$args
        ]);
    }

    public function showErrorParticipant($error) {
        return view('Participant.EventNotFound', compact('error'));
    }
}
