<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function showAuthenticatedPage(Request $request)
    {
        return view('DemoAuth.Authenticated');
    }
    public function showNoAuthPage(Request $request)
    {
        return view('DemoAuth.NoAuth');
    }
    public function showOrganizerPage(Request $request)
    {
        return view('DemoAuth.Organizer');
    }
    public function showParticipantPage(Request $request)
    {
        return view('DemoAuth.Participant');
    }
}
