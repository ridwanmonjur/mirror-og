<div>
    {{-- CREATE FORM --}}
    <form method="POST" v-on:submit="submitDisputeForm(event)" id="createForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <input type="hidden" name="dispute_teamId" v-bind:value="report.teams[reportUI.matchNumber]?.id">
        <input type="hidden" name="dispute_teamNumber" v-bind:value="reportUI.teamNumber">
        <input type="hidden" name="report_id" v-bind:value="report.id">
        <input type="hidden" name="dispute_userId" value="{{ $user?->id }}">
        <input type="hidden" name="match_number" v-bind:value="reportUI.matchNumber">
        <div class="row">
            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
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
                            <div class="mb-3 form-check">
                                <input type="radio" name="reportReason" id="{{ $key }}"
                                    value="{{ $label }}" class="">
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
                <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
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
                <div class="row justify-content-start bg-light border border-3 border-dark border rounded px-2 py-2">
                    <h5 class="text-start my-3"> Image/Video Evidence <span class="text-red">*</span>
                    </h5>
                    <div class="ps-5 pe-5 text-start">
                        <div class="upload-container ps-5 pe-5" v-scope="UploadData('claim')"
                            id="claimId" @vue:mounted="init()">
                            <div class="d-flex justify-content-start">
                                <div class="upload-area me-2 d-flex justify-content-between" id="uploadArea"></div>
                                <div class="plus-button" v-on:click="clickInput()">+</div>
                            </div>
                            <input type="file" class="file-input" multiple accept="image/*,video/*"
                                v-on:change="handleFiles(event)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mx-auto d-flex justify-content-around">
            <button type="button" class="btn btn-light btn-large btn-pill border-dark px-5 py-3 rounded-pill px-5 py-2"
                data-bs-toggle="modal" data-bs-target="#reportModal" data-bs-dismiss="modal">
                Cancel
            </button>
            <button type="submit" style="background-color: #686767;"
                class="btn  btn-large  text-light border-light rounded-pill px-5 py-3">
                Submit
            </button>
        </div>
    </form>
</div>
