<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller {

    /**
        * Display a listing of the resource.
        *
        * @return Response
        */
    public function index()
    {
        //
    }

    /**
        * Show the form for creating a new resource.
        *
        * @return Response
        */
    public function create()
    {
        //
    }

    /**
        * Store a newly created resource in storage.
        *
        * @return Response
        */
    public function store()
    {
        //
    }

    /**
        * Display the specified resource.
        *
        * @param  int  $id
        * @return Response
        */
    public function show($id)
    {
        //
    }

    /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return Response
        */
    public function edit($id)
    {
        //
    }

    /**
        * Update the specified resource in storage.
        *
        * @param  int  $id
        * @return Response
        */
    public function update($id)
    {
        //
    }

    /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return Response
        */
    public function destroy($id)
    {
        //
    }

    public function manage()
    {
        $eventList = Event::all();  
        $mappingEventState = [
            'UPCOMING' => ['buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
            'ONGOING' => ['buttonBackgroundColor' => '#FFFBFB', 'buttonTextColor' => 'black', 'borderColor' => 'black'],
            'DRAFT' => ['buttonBackgroundColor' => '#8CCD39', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
            'ENDED' => ['buttonBackgroundColor' => '#A6A6A6', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
        ];
        $mappingTier = [
            'Turtle' => ['background'=> '/assets/images/turtle.png', 'class' => [ 'rounded-box-turtle', 'card-image-turtle' ] ],
            'Dolphin' => ['background'=> '/assets/images/dolphin.png', 'class' => [ 'rounded-box-dolphin', 'card-image-dolphin' ] ],
            'Mermaid' => ['background'=> '/assets/images/mermaid.png', 'class' => [ 'rounded-box-mermaid', 'card-image-mermaid' ] ],
            'Starfish' => ['background'=> '/assets/images/starfish.png', 'class' => [ 'rounded-box-starfish', 'card-image-starfish' ] ],

        ]; 
        return view('Organizer.ManageEvent', ['eventList' => $eventList, 'mappingTier'=> $mappingTier,  'mappingEventState'=> $mappingEventState]);

    }

}