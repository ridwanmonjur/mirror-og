<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{

    private $mappingEventState = [
        'UPCOMING' => ['buttonBackgroundColor' => '#43A4D7', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
        'ONGOING' => ['buttonBackgroundColor' => '#FFFBFB', 'buttonTextColor' => 'black', 'borderColor' => 'black'],
        'DRAFT' => ['buttonBackgroundColor' => '#8CCD39', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
        'ENDED' => ['buttonBackgroundColor' => '#A6A6A6', 'buttonTextColor' => 'white', 'borderColor' => 'transparent'],
    ];
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function home(Request $request): View
    {
        if ($request->is('organizer/*')) {
            return view('Organizer.Home');
        }
        else if ($request->is('admin/*')) {
            return view('Admin.Home');
        } else {
            return view('Participant.Home');
        }

    }

    public function index()
    {
        $eventList = EventDetail::all();

        return view(
            'Organizer.ManageEvent',
            ['eventList' => $eventList, 'mappingEventState' => $this->mappingEventState]
        );
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
    public function show($id): View
    {
        $event = Event::findOrFail($id);

        return view(
            'Organizer.ViewEvent',
            ['event' => $event, 'mappingEventState' => $this->mappingEventState]
        );
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
        $eventList = EventDetail::all();

        return view(
            'Organizer.ManageEvent',
            ['eventList' => $eventList, 'mappingEventState' => $this->mappingEventState]
        );
    }
}
