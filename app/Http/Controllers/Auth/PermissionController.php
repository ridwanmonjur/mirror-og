<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function showAuthenticated(Request $request)
    {
        return view('DemoAuth.Authenticated');
    }
    public function showNoAuth(Request $request)
    {
        return view('DemoAuth.NoAuth');
    }
    public function showOrganizer(Request $request)
    {
        return view('DemoAuth.Organizer');
    }
    public function showParticipant(Request $request)
    {
        return view('DemoAuth.Participant');
    }
    
}
