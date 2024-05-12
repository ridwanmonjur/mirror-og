<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Client\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function showErrorOrganizer($error, $args = []) {
        return view('Organizer.EventNotFound', [
            'error' => $error,
            ...$args
        ]);
    }

    public function showErrorParticipant($error) {
        return view('Participant.EventNotFound', compact('error'));
    }

    protected function handleQueryParams(Request $request)
    {
        $queryParams = Request::query();
        
        // Check if 'success' or 'tab' is present in query params
        if (isset($queryParams['success'])) {
            $successValue = $queryParams['success'];
            unset($queryParams['success']);
            Session::flash('success', $successValue);
        }
        if (isset($queryParams['tab'])) {
            $tabValue = $queryParams['tab'];
            unset($queryParams['tab']);
            Session::flash('tab', $tabValue);
        }
        
        // Update the request query parameters
        Request::replace($queryParams);
    }

}
