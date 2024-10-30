<div class="modal fade show" data-show="true" id="disputeModal" tabindex="3" aria-labelledby="disputeModalLabel"
    aria-hidden="false">
    <div class="modal-dialog " style="min-width: 90vw;">
        <div class="modal-content " style="background-color: transparent !important; ">
            <div class="modal-body my-3 px-5 ">
                <div class="row">
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 pe-0 pe-lg-5' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div
                            class="row justify-content-start border bg-light border-3 border-dark border rounded px-2 py-2">
                            <h5 class="text-start my-3"> Event Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Name: {{ $event->eventName }} </p>
                                <p class="my-0">Organizer: {{ $event->user->name }}</p>
                                <br>
                                <p class="my-0"> Description: {{ $event->eventDescription }}</p>
                                <p class="my-0">Type: {{ $event->type->eventType }}</p>
                                <p class="my-0">Tier: {{ $event->tier->eventTier }}</p>
                                <p class="mb-3">Region: South East Asia (SEA)</p>
                            </div>
                        </div>
                    </div>
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 ps-lg-5 pe-0 ' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div
                            class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 pt-2 pb-4">
                            <h5 class="text-start my-3"> Match Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Match: <span x-text="report.position"></span></p>
                                <p class="mt-0 mb-2">Game: <span x-text="reportUI.matchNumber+1"> </span></p>
                                <div class="row px-0 w-75 mx-auto">
                                    <div class="col-12  text-center col-lg-4">
                                        <div>
                                            <img 
                                                :src="'/storage/' + report.teams[0].banner"  
                                                alt="Team Banner" width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0" x-text="report.teams[0]?.name"></p>
                                        <br>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <div
                                            class="d-flex  text-center justify-content-center align-items-center h-100">
                                            <h1>VS</h1>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4  text-center">
                                        <div>
                                            <img 
                                                :src="'/storage/' + report.teams[1].banner"  
                                                alt="Team Banner" width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0" x-text="report.teams[1]?.name"></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <template x-if="!dispute[reportUI.matchNumber]">
                    <div>
                        {{-- CREATE FORM --}}
                        <form method="POST" x-on:submit="submitDisputeForm(event)" id="create" >
                            <input type="hidden" name="action" value="create"> 
                            <input type="hidden" name="dispute_teamId" x-bind:value="report.teams[reportUI.matchNumber].id"> 
                            <input type="hidden" name="dispute_teamNumber" x-bind:value="reportUI.teamNumber"> 
                            <input type="hidden" name="report_id" x-bind:value="report.id"> 
                            <input type="hidden" name="match_number" x-bind:value="reportUI.matchNumber"> 
                            <div class="row">
                                <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                    <div
                                        class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                                        <h5 class="text-start my-3"> Reason for Dispute </h5>
                                        <div class="ps-5 pe-5 text-start">
                                            @php
                                                $reasons = [
                                                    'dishonest' =>
                                                        'The opponent team is being dishonest in their declaration of the results.',
                                                    'cheating' => 'The opponent team abused cheats/hacks during the game.',
                                                    'match_fixing' =>
                                                        'There is suspected compromises to match integrity (e.g. match-fixing).',
                                                    'other' => 'Other (Please specify your reason.)',
                                                ];
                                            @endphp
                                            @foreach ($reasons as $key => $label)
                                                <div class="mb-3">
                                                    <input type="radio" name="reportReason"
                                                        id="{{ $key }}" value="{{ $label }}">
                                                    <label class="ms-1 form-check-label" for="{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            <input type="text" class="form-control border-primary" id="otherReason"
                                                name="otherReasonText" placeholder="  Enter your reason here...">
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                    <div
                                        class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                                        <h5 class="text-start my-3"> Dispute Description (optional) </h5>
                                        <div class="ps-5 pe-5 text-start">
                                            <div class="mb-3">
                                                <label for="description" class="form-label"><strong>Provide a detailed
                                                        description:</strong></label>
                                                <textarea class="form-control" id="description" name="dispute_description" rows="5"
                                                    placeholder="Please provide more details about the issue..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                    <div
                                        class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                                        <h5 class="text-start my-3"> Image/Video Evidence <span class="text-red">*</span>
                                        </h5>
                                        <div class="ps-5 pe-5 text-start">
                                            <div class="mb-3">
                                                <label for="description" class="form-label"><strong>Provide a detailed
                                                    description:</strong>
                                                </label>
                                                {{-- <textarea class="form-control" id="description" name="description" rows="5"
                                                    placeholder="Please provide more details about the issue...">
                                                </textarea> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 mx-auto d-flex justify-content-around">
                                <button 
                                    type="button"
                                    class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    style="background-color: #686767;"
                                    class="btn  btn-large  text-light border-light rounded-pill px-5 py-3"
                                >
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </template>

                <template x-if="dispute[reportUI.matchNumber]">
                    <div>
                        <div class="row">
                            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                <div
                                    class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                                    <h5 class="text-start my-3"> Dispute Information </h5>
                                    <div class="ps-5 ps-5 text-start">
                                        <p class="my-0"> Disputing Team </p>
                                        <img :src="'/storage/images/' + report.teams[dispute[reportUI.matchNumber].dispute_teamNumber].banner"
                                            alt="Team Banner" width="50" height="50"
                                            onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                            class="mb-1 border border-2 popover-content-img rounded-circle object-fit-cover">
                                        <p class="text-primary">
                                            <span x-text="report.teams[dispute[reportUI.matchNumber].dispute_teamNumber].name"> </span>
                                            <span x-show="reportUI.teamNumber == dispute[reportUI.matchNumber].dispute_teamNumber">(Your Team)</span>
                                        </p>

                                        <p class="my-0" x-html="dispute[reportUI.matchNumber].dispute_reason">
                                        </p>
                                        <p class="text-primary" style="white-space: pre-wrap;"
                                            x-html="dispute[reportUI.matchNumber].dispute_description">
                                        </p>
                                        <p class="my-0">Image/ Video Evidence: </p>
                                        <p class="mb-3">Tier: Starfish</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                <div
                                    class="row  bg-light justify-content-start border border-3 border rounded px-2 py-2">
                                    <h5 class="text-start my-3"> Dispute Response </h5>
                                    <div class="ps-5 ps-5 text-start">
                                        <br><br><br>
                                        <p class="text-center fw-lighter">
                                            <i> Waiting for the team to respond </i>
                                        </p>
                                        <br><br><br>    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                <div
                                    class="row justify-content-start bg-light border border-3 border rounded px-2 py-2">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="text-start my-3"> Resolution </h5>
                                        <div class="text-end my-3">
                                            <p class="my-0">Time left until auto-resolve:</p>
                                            <small>0d 0h</small>
                                        </div>
                                    </div>
                                    <div class="ps-5 ps-5 text-start">
                                        <br><br><br>
                                        <p class="text-center fw-lighter">
                                            When the opponent team submits their counter-evidence, the organizer will
                                            resolve the dispute.
                                            <br>
                                            If the opponent team does not respond by the allocated time, the dispute
                                            will automatically resolve in your favor.
                                        </p>
                                        <br><br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                            <div class="text-center">
                                <button data-bs-dismiss="modal" class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2">
                                    Return to bracket
                                </button>
                            </div>
                        <br>
                            <div class="text-center">
                                <button style="background-color: #e05e5e;font-size: 1.4 rem;" class="btn  btn-large btn-danger border-danger rounded-pill px-5 py-3">
                                    Cancel Dispute
                                </button>
                            </div>
                        <br><br>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
