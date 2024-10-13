<div class="modal fade show" data-show="true" id="disputeModal" tabindex="3" aria-labelledby="disputeModalLabel"
    aria-hidden="false">
    <div class="modal-dialog " style="min-width: 90vw;">
        <div class="modal-content " style="background-color: transparent !important; ">
            <div class="modal-body my-3 px-5 ">
                <div class="row">
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 pe-0 pe-lg-5' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div class="row justify-content-start border bg-light border-3 border-dark border rounded px-2 py-2"
                        >
                            <h5 class="text-start my-3"> Event Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Name: {{$event->eventName}} </p>
                                <p class="my-0">Organizer: {{$event->user->name}}</p>
                                <br>
                                <p class="my-0"> Description: {{$event->eventDescription}}</p>
                                <p class="my-0">Type: {{$event->type->eventType}}</p>
                                <p class="my-0">Tier: {{$event->tier->eventTier}}</p>
                                <p class="mb-3">Region: South East Asia (SEA)</p>
                            </div>
                        </div>
                    </div>
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 ps-lg-5 pe-0 ' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 pt-2 pb-4"
                        >
                            <h5 class="text-start my-3"> Match Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Match: <span  x-text="report.position"></span></p>
                                <p class="mt-0 mb-2">Game: <span x-text="reportUI.matchNumber+1"> </span></p>
                                <div class="row px-0 w-75 mx-auto">
                                    <div class="col-12  text-center col-lg-4">
                                        <div>
                                            <img :src="'/storage/images/team/' + report.teams[0].banner"
                                                alt="Team Banner" width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover"
                                            >
                                        </div>
                                        <p class="mt-1 mb-2 py-0" x-text="report.teams[0]?.name"></p>
                                        <br>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <div class="d-flex  text-center justify-content-center align-items-center h-100">
                                            <h1>VS</h1>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4  text-center">
                                        <div>
                                            <img :src="'/storage/images/team/' + report.teams[1].banner" 
                                                alt="Team Banner" width="50"
                                                height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0"  x-text="report.teams[1]?.name"></p>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                        <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2"
                        >
                            <h5 class="text-start my-3"> Dispute Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Disputing Team </p>
                                 <img src="{{ asset('storage/images/team/teamBanner-1727678919.jpeg') }}"
                                    alt="Team Banner" width="50" height="50"
                                    onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    class="mb-1 border border-2 popover-content-img rounded-circle object-fit-cover"
                                >
                                <p class="text-primary">
                                    Farming Enjoyers 
                                    <span>(Your Team)</span>
                                </p>
                                
                                <p class="my-0"> Reason: </p>
                                <p class="text-primary">
                                   Other player on team racial slur.
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                   Other player on team racial slur
                                </p>
                                <p class="my-0">Image/ Video Evidence: </p>
                                <p class="mb-3">Tier: Starfish</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                        <div class="row  bg-light justify-content-start border border-3 border rounded px-2 py-2"
                        >
                            <h5 class="text-start my-3"> Dispute Response </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Disputing Team </p>
                                <br><br><br><br>
                                <p class="text-center fw-lighter"> 
                                    <i> Waiting for the team to respond </i>
                                </p>
                                <br><br><br><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                        <div class="row justify-content-start bg-light border border-3 border rounded px-2 py-2"
                        >
                            <div class="d-flex justify-content-between">
                                <h5 class="text-start my-3"> Resolution </h5>
                                <div class="text-end my-3">
                                    <p class="my-0">Time left until auto-resolve:</p>
                                    <small>0d 0h</small>
                                </div>
                            </div>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Resolution </p>
                                <br><br><br><br>
                                <p class="text-center fw-lighter"> 
                                    When the opponent team submits their counter-evidence, the organizer will resolve the dispute.
                                    <br>
                                    If the opponent team does not respond by the allocated time, the dispute will automatically resolve in your favor.
                                </p>
                                <br><br><br><br>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                    <div class="text-center">
                        <button style="font-size: 1.4 rem;" class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2">
                            Return to bracket
                        </button>
                    </div>
                <br>
                    <div class="text-center">
                        <button style="background-color: #e05e5e;font-size: 1.4 rem;color: #494949;" class="btn  btn-large btn-danger border-danger rounded-pill px-5 py-3">
                            Cancel Dispute
                        </button>
                    </div>
                <br><br>
            </div>
        </div>
    </div>
</div>
