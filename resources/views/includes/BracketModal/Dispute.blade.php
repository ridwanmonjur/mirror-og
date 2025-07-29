<div class="modal fade" style="z-index: 999900 !important;" id="imageModal" tabindex="5" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
              <div class="modal-body text-center p-0">
                <!-- Image container (hidden for videos) -->
                <img id="imagePreview" class="img-fluid object-fit-cover mx-auto" alt="Full size image" style="max-height: 70vh; display: none;">
                
                <!-- Video container (hidden for images) -->
                <video id="videoPreview" controls class="img-fluid" style="max-height: 70vh; display: none;">
                    <source id="videoSource" src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <br>
        </div>
    </div>
</div>
<div class="modal fade show" data-show="true" id="disputeModal" tabindex="3" aria-labelledby="disputeModalLabel">
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
                                <p class="my-0">Organizer: {{ $event->user?->name }}</p>
                                <br>
                                <p class="my-0"> Description: {{ $event->eventDescription }}</p>
                                <p class="my-0">Type: {{ $event->type?->eventType }}</p>
                                <p class="my-0">Tier: {{ $event->tier?->eventTier }}</p>
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
                                <p class="my-0"> Match: <span v-text="report.position"></span></p>
                                <p class="mt-0 mb-2">Game: <span v-text="reportUI.matchNumber+1"> </span></p>
                                <div class="row px-0 w-75 mx-auto">
                                    <div class="col-12  text-center col-lg-4">
                                        <div>
                                            <img v-bind:src="report.teams[0] && report.teams[0].banner? '/storage/' + report.teams[0].banner : '/assets/images/.png'" alt="Team Banner"
                                                width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0" v-text="report.teams[0]?.name"></p>
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
                                            <img v-bind:src="report.teams[1] && report.teams[1].banner? '/storage/' + report.teams[1].banner :'/assets/images/.png'" alt="Team Banner"
                                                width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                                                class="border border-2 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0" v-text="report.teams[1]?.name"></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               

                <template v-if="dispute">
                    <div>
                         @include('includes.BracketModal.Dispute.Claim')
                        <template v-if="dispute?.response_teamId">
                            @include('includes.BracketModal.Dispute.Response')
                        </template>
                        <template v-if="!dispute?.response_teamId && userLevelEnums['IS_ORGANIZER'] != report.userLevel">
                            <div class="row">
                                <template
                                    v-if="reportUI.teamNumber == dispute?.dispute_teamNumber">
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
                                    v-if="reportUI.teamNumber != dispute?.dispute_teamNumber && !dispute?.resolution_winner">
                                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                                        <div class="response_decision row  bg-light justify-content-start border border-3 border rounded px-2 py-2">
                                            <div class="mt-3 d-flex align-items-center py-3 pb-4 justify-content-around flex-column">
                                                <button style="width: 250px;" class="btn btn-danger text-light bg-red px-2 mb-2 py-2 rounded-pill border border-dark" v-on:click="toggleResponseForm('response_decision', 'response_form');">
                                                    Submit Counter Evidence
                                                </button>
                                                <form method="POST" v-on:submit="resolveDisputeForm(event)" id="resolveForm">
                                                    <input type="hidden" name="match_number" v-bind:value="dispute?.match_number">
                                                    <input type="hidden" name="action" value="resolve">
                                                    <input type="hidden" name="id" v-bind:value="dispute?.id">
                                                    <input type="hidden" name="resolution_winner" v-bind:value="reportUI.otherTeamNumber">
                                                    <input type="hidden" name="resolution_resolved_by" v-bind:value="disputeLevelEnums['RESPONDER']">
                                                    <button type="submit" style="width: 250px;" class="btn d-inline-block btn-light border px-2 mb-2 py-2 border-dark rounded-pill text-dark"
                                                        data-bs-toggle="modal" data-bs-target="#reportModal" data-bs-dismiss="modal" 
                                                    >
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
                                            <form method="POST" v-on:submit="respondDisputeForm(event)" id="respondForm">
                                                <input type="hidden" name="action" value="respond">
                                                <input type="hidden" name="dispute_teamId" v-bind:value="report.teams[reportUI.teamNumber]?.id">
                                                <input type="hidden" name="response_teamNumber" v-bind:value="reportUI.teamNumber">
                                                <input type="hidden" name="match_number" v-bind:value="dispute?.match_number">
                                                <input type="hidden" name="id" v-bind:value="dispute?.id">
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
                                                <div class="ps-5 pe-5 text-start">
                                                <div class="upload-container ps-5 pe-5" v-scope="UploadData('response')" id="responseId"
                                                    @vue:mounted="init()"
                                                >
                                                    <div class="d-flex justify-content-start">
                                                        <div class="upload-area me-2 d-flex justify-content-between" id="uploadArea"></div>
                                                        <div class="plus-button" v-on:click="clickInput()">+</div>
                                                    </div>
                                                    <input type="file" 
                                                        class="file-input" 
                                                        multiple
                                                        accept="image/*,video/*" 
                                                        v-on:change="handleFiles(event)">
                                                    </div>
                                                </div>
                                                <div class="my-3 px-3 d-flex justify-content-between">
                                                    <button class="btn btn-light rounded-pill border border-dark" data-bs-toggle="modal" data-bs-target="#reportModal" data-bs-dismiss="modal"  v-on:click="toggleResponseForm('response_form', 'response_decision');">
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
                        
                        <template v-if="dispute?.resolution_winner">
                            @include('includes.BracketModal.Dispute.Winner')
                        </template> 
                         <template v-else> 
                            @include('includes.BracketModal.Dispute.MissingWinner')
                        </template>
                        <br>
                        <div class="text-center">
                            <button data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#reportModal" 
                                class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2">
                                Return to bracket
                            </button>
                        </div>
                        <br>
                        <template v-if="!dispute?.resolution_winner && reportUI.teamNumber == dispute?.dispute_teamNumber">
                            <div class="text-center">
                                <form method="POST" v-on:submit="resolveDisputeForm(event)">
                                    <input type="hidden" name="action" value="resolve">
                                    <input type="hidden" name="id" v-bind:value="dispute?.id">
                                    <input type="hidden" name="match_number" v-bind:value="dispute?.match_number">
                                    <input type="hidden" name="already_winner" v-bind:value="reportUI.otherTeamNumber">
                                    <input type="hidden" name="resolution_resolved_by" v-bind:value="disputeLevelEnums['DISPUTEE']">
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
                 <template v-else>
                    @include('includes.BracketModal.Dispute.Create')
                </template>
            </div> 
        </div>
    </div>
</div>

