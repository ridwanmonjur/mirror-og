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
                                            <img :src="'/storage/' + report.teams[0]?.banner" alt="Team Banner"
                                                width="50" height="50"
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
                                            <img :src="'/storage/' + report.teams[1]?.banner" alt="Team Banner"
                                                width="50" height="50"
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
                        <form method="POST" x-on:submit="submitDisputeForm(event)" id="create">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="event_id" value="{{$event->id}}">
                            <input type="hidden" name="dispute_teamId" x-bind:value="report.teams[reportUI.matchNumber]?.idid">
                            <input type="hidden" name="dispute_teamNumber" x-bind:value="reportUI.teamNumber">
                            <input type="hidden" name="report_id" x-bind:value="report.id">
                            <input type="hidden" name="dispute_userId" value="{{$user?->id}}">
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
                                                    'cheating' =>
                                                        'The opponent team abused cheats/hacks during the game.',
                                                    'match_fixing' =>
                                                        'There is suspected compromises to match integrity (e.g. match-fixing).',
                                                    'other' => 'Other (Please specify your reason.)',
                                                ];
                                            @endphp
                                            @foreach ($reasons as $key => $label)
                                                <div class="mb-3">
                                                    <input type="radio" name="reportReason" id="{{ $key }}"
                                                        value="{{ $label }}">
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
                                        <h5 class="text-start my-3"> Image/Video Evidence <span
                                            class="text-red">*</span>
                                        </h5>
                                        <div class="ps-5 pe-5 text-start">
                                            <div class="upload-container">
                                                <div class="upload-area" x-ref="uploadArea1"
                                                    @dragover.prevent="$refs.uploadArea1.classList.add('drag-over')"
                                                    @dragleave.prevent="$refs.uploadArea1.classList.remove('drag-over')"
                                                    @drop.prevent="handleDrop($event, '1')">
                                                    <div class="plus-button" @click="$refs.fileInput1.click()">+</div>
                                                </div>
                                                <input type="file" x-ref="fileInput1" class="file-input" multiple
                                                    accept="image/*" @change="handleFiles($event, '1')">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="w-100 mx-auto d-flex justify-content-around">
                                <button type="button"
                                    class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2">
                                    Cancel
                                </button>
                                <button type="submit" style="background-color: #686767;"
                                    class="btn  btn-large  text-light border-light rounded-pill px-5 py-3">
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
                                        <img :src="'/storage/images/' + report.teams[dispute[reportUI.matchNumber]
                                            .dispute_teamNumber]?.banner"
                                            alt="Team Banner" width="50" height="50"
                                            onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                            class="mb-1 border border-2 popover-content-img rounded-circle object-fit-cover">
                                        <p class="text-primary">
                                            <span
                                                x-text="report.teams[dispute[reportUI.matchNumber].dispute_teamNumber].name">
                                            </span>
                                            <span
                                                x-show="reportUI.teamNumber == dispute[reportUI.matchNumber].dispute_teamNumber">(Your
                                                Team)</span>
                                        </p>

                                        <p class="my-0" x-html="dispute[reportUI.matchNumber].dispute_reason">
                                        </p>
                                        <p class="text-primary" style="white-space: pre-wrap;"
                                            x-html="dispute[reportUI.matchNumber].dispute_description">
                                        </p>
                                        <p class="my-0">Image/ Video Evidence: <span class="text-red">*<span></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- reportUI.teamNumber == dispute[reportUI.matchNumber].dispute_teamNumber --}}
                        <template x-if="dispute[reportUI.matchNumber].response_teamId">
                            <div class="row">
                                <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                    <div
                                        class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                                        <div class="ps-5 ps-5 text-start">
                                            <h5 class="text-start my-3"> Counter Explanation (Optional) </h5>
                                            <p class="my-0"> Responding Team </p>
                                            <img :src="'/storage/images/' + report.teams[dispute[reportUI.matchNumber]
                                                .response_teamNumber]?.banner"
                                                alt="Team Banner" width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="mb-1 border border-2 popover-content-img rounded-circle object-fit-cover"
                                            >
                                            <p class="text-primary">
                                                <span
                                                    x-text="report.teams[dispute[reportUI.matchNumber].response_teamNumber].name">
                                                </span>
                                                <span
                                                    x-show="reportUI.teamNumber == dispute[reportUI.matchNumber].response_teamNumber">(Your
                                                    Team)
                                                </span>
                                            </p>

                                            <p class="my-0" x-html="dispute[reportUI.matchNumber].response_explanation">
                                            </p>
                                            <p class="text-primary" style="white-space: pre-wrap;"
                                                x-html="dispute[reportUI.matchNumber].dispute_description">
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="!dispute[reportUI.matchNumber].response_teamId && userLevelEnums['IS_ORGANIZER'] != report.userLevel">
                            <div class="row">
                                <template
                                    x-if="reportUI.teamNumber == dispute[reportUI.matchNumber].dispute_teamNumber">
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
                                </template>
                                <template
                                    x-if="reportUI.teamNumber != dispute[reportUI.matchNumber].dispute_teamNumber">
                                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                        <div class="response_decision row  bg-light justify-content-start border border-3 border rounded px-2 py-2">
                                            <div class="mt-3 d-flex align-items-center py-3 pb-4 justify-content-around flex-column">
                                                <button style="width: 250px;" class="btn btn-danger text-light bg-red px-2 mb-2 py-2 rounded-pill border border-dark" x-on:click="toggleResponseForm('response_decision', 'response_form');">
                                                    Submit Counter Evidence
                                                </button>
                                                <form method="POST" x-on:submit="resolveDisputeForm(event)" id="create">
                                                    <input type="hidden" name="dispute_matchNumber" x-bind:value="dispute[reportUI.matchNumber].matchNumber">
                                                    <input type="hidden" name="action" value="resolve">
                                                    <input type="hidden" name="id" x-bind:value="dispute[reportUI.matchNumber].id">
                                                    <input type="hidden" name="resolution_winner" x-bind:value="reportUI.otherTeamNumber">
                                                    <input type="hidden" name="resolution_resolved_by" x-bind:value="disputeLevelEnums['RESPONDER']">
                                                    <button type="submit" style="width: 250px;" class="btn d-inline-block btn-light border px-2 mb-2 py-2 border-dark rounded-pill text-dark">
                                                        Concede
                                                    </button>
                                                </form> 
                                                <small style="width: 350px;" class="text-center">
                                                    NOTE: Conceding will automatically resolve the dispute in favor of the opponent team.
                                                </small>
                                            </div>
                                        </div>

                                        <div
                                            class="response_form d-none row  bg-light justify-content-start border border-3 border rounded px-2 py-2">
                                            <form method="POST" x-on:submit="respondDisputeForm(event)" id="respond">
                                                <input type="hidden" name="action" value="respond">
                                                <input type="hidden" name="dispute_teamId" x-bind:value="report.teams[reportUI.matchNumber]?.idid">
                                                <input type="hidden" name="response_teamNumber" x-bind:value="reportUI.teamNumber">
                                                <input type="hidden" name="dispute_matchNumber" x-bind:value="dispute[reportUI.matchNumber].match_number">
                                                <input type="hidden" name="id" x-bind:value="dispute[reportUI.matchNumber].id">
                                                <input type="hidden" name="response_userId" value="{{$user?->id}}">

                                                <h5 class="text-start my-3"> Dispute Description (optional) </h5>
                                                <div class="ps-5 pe-5 text-start">
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label"><strong>Provide a detailed
                                                                description:</strong></label>
                                                        <textarea class="form-control" id="description" name="dispute_description" rows="5"
                                                            placeholder="Please provide more details about the issue..."></textarea>
                                                    </div>
                                                </div>
                                                <p class="my-0 text-start ps-5 pe-5 ">Image/ Video Evidence: <span class="text-red">*<span> </p>
                                                <div class="upload-container ps-5 pe-5 ">
                                                    <div class="upload-area" x-ref="uploadArea2"
                                                        @dragover.prevent="$refs.uploadArea2.classList.add('drag-over')"
                                                        @dragleave.prevent="$refs.uploadArea2.classList.remove('drag-over')"
                                                        @drop.prevent="handleDrop($event, '2')">
                                                        <div class="plus-button" @click="$refs.fileInput2.click()">+</div>
                                                    </div>
                                                    <input type="file" x-ref="fileInput2" class="file-input" multiple
                                                        accept="image/*" @change="handleFiles($event, '2')">
                                                </div>
                                                <div class="my-3 px-3 d-flex justify-content-between">
                                                    <button class="btn btn-light rounded-pill border border-dark" x-on:click="toggleResponseForm('response_form', 'response_decision');">
                                                        Cancel
                                                    </button>
                                                    <button class="btn btn-secondary rounded-pill text-light" type="submit">
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                         </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!dispute[reportUI.matchNumber].resolution_winner">
                            <div>
                                <template x-if="userLevelEnums['IS_ORGANIZER'] != report.userLevel">
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
                                                    <br><br>
                                                    <p class="text-center fw-lighter">
                                                        When the opponent team submits their counter-evidence, the organizer will
                                                        resolve the dispute.
                                                        <br>
                                                        If the opponent team does not respond by the allocated time, the dispute
                                                        will automatically resolve in your favor.
                                                    </p>
                                                    <br><br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="userLevelEnums['IS_ORGANIZER'] == report.userLevel">
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
                                                    <form method="POST" x-on:submit="resolveDisputeForm(event)" id="resolve">
                                                        <input type="hidden" name="action" value="resolve">
                                                        <input type="hidden" name="id" x-bind:value="dispute[reportUI.matchNumber].id">
                                                        <input type="hidden" name="dispute_matchNumber" x-bind:value="dispute[reportUI.matchNumber].match_number">

                                                        <input type="hidden" name="resolution_winner" id="resolution_winner_input">
                                                        <input type="hidden" name="resolution_resolved_by" value="disputeLevelEnums['ORGANIZER']">
                                                        <p class="text-primary text-center"> The dispute will be resolved in favor of (Choose): </p>
                                                        <div class="d-flex justify-content-center flex-column mt-2">
                                                            <button type="button" x-on:click="decideResolution(event, 0)" :disabled="getDisabled()"
                                                                class="selectedButton selectedDisputeResolveButton ps-0 btn mb-2 mt-2 rounded-pill mx-auto py-0 border border-dark text-start">
                                                                <img :src="'/storage/' + report.teams[0]?.banner" alt="Team Banner" width="35" height="35"
                                                                    onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                                    class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                                                                <small class="ms-2 py-0" x-text="report.teams[0]?.name"></small>
                                                            </button>
                                                            <button type="button" x-on:click="decideResolution(event, 1)" :disabled="getDisabled()"
                                                                class="selectedButton selectedDisputeResolveButton ps-0 btn  rounded-pill mx-auto py-0 mt-2 border border-dark text-start">
                                                                <img :src="'/storage/' + report.teams[1]?.banner" alt="Team Banner" width="35" height="35"
                                                                    onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                                    class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                                                                <small class="ms-2 py-0" x-text="report.teams[1]?.name"></small>
                                                            </button>
                                                            <button type="submit"  class="selectTeamSubmitButton btn border mx-auto border border-primary text-primary py-2 btn-sm rounded-pill mt-4 px-4"> Submit </button>
                                                        </div>
                                                        <br>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="dispute[reportUI.matchNumber].resolution_winner">
                            <div class="row">
                                <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                    <div
                                        class="row justify-content-start bg-light border border-3 border rounded px-2 py-2">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="text-start my-3"> Resolution </h5>
                                        </div>
                                        <div class="ps-5 ps-5 text-start">
                                            <div class="mt-2">
                                                <div>
                                                    <img :src="'/storage/' + report.teams[report.realWinners[reportUI.matchNumber]]?.banner" alt="Team Banner"
                                                        width="60" height="60" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                                                </div>
                                                <div class="mt-2 d-block">
                                                    <p>
                                                        <span x-text="report.teams[report.realWinners[reportUI.matchNumber]]?.name"> </span>
                                                        has been resolved as the winner.
                                                    </p>
                                                    <template x-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['DISPUTEE']">
                                                        <div class="mt-2">
                                                            <p class="text-success mt-2">
                                                                <span x-text="report.teams[dispute[reportUI.matchNumber]?.dispute_teamNumber].name">
                                                                </span> has conceded the dispute. Winner is to be decided by the organizer.
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template x-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['RESPONDER']">
                                                        <div class="mt-2">
                                                            <p class="text-success mt-2">
                                                                The responder has conceded the dispute. The disputee is declared as the winner.
                                                            </p>
                                                        </div>
                                                    </template>
                                                    <template x-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['ORGANIZER']">
                                                        <div class="mt-2">
                                                            <p class="text-success mt-2">
                                                            Winner has been decided by the organizer.
                                                            </p>
                                                        </div>
                                                    </template>

                                                    <template x-if="userLevelEnums['IS_ORGANIZER'] == report.userLevel">
                                                        <div class="d-inline">
                                                            <form method="POST" class="d-inline" x-on:submit="resolveDisputeForm(event)" id="resolve">
                                                                <input type="hidden" name="action" value="resolve">
                                                                <input type="hidden" name="id" x-bind:value="dispute[reportUI.matchNumber].id">
                                                                <input type="hidden" name="dispute_matchNumber" x-bind:value="dispute[reportUI.matchNumber].match_number">
                                                                <input type="hidden" name="already_winner" x-bind:value="dispute[reportUI.matchNumber].resolution_winner">
                                                                <input type="hidden" name="resolution_resolved_by" x-bind:value="disputeLevelEnums['ORGANIZER']">
                                                                <button type="submit" class="btn py-0 d-inline rounded-pill btn-link text-primary">
                                                                    Change Declaration
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <br>
                        <div class="text-center">
                            <button data-bs-dismiss="modal"
                                class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2">
                                Return to bracket
                            </button>
                        </div>
                        <br>
                            <template x-if="!dispute[reportUI.matchNumber]?.resolution_winner">
                                <div class="text-center">
                                    <form method="POST" x-on:submit="resolveDisputeForm(event)" id="resolve">
                                        <input type="hidden" name="action" value="resolve">
                                        <input type="hidden" name="id" x-bind:value="dispute[reportUI.matchNumber].id">
                                        <input type="hidden" name="dispute_matchNumber" x-bind:value="dispute[reportUI.matchNumber].match_number">
                                        <input type="hidden" name="already_winner" x-bind:value="reportUI.otherTeamNumber">
                                        <input type="hidden" name="resolution_resolved_by" x-bind:value="disputeLevelEnums['DISPUTEE']">
                                        <button type="submit"
                                            class="btn  btn-large btn-danger bg-red border-danger rounded-pill px-5 py-3">
                                            Cancel Dispute
                                        </button>
                                    </form> 
                                </div>
                            </template>
                        <br><br>
                    </div>
                </template>

            </div>
        </div>
    </div>
</div>
