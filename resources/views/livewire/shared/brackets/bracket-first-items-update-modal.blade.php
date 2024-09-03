<div class="modal fade" id="firstMatchModal" tabindex="-1" aria-labelledby="firstMatchModalLabel" aria-hidden="true">
    <div class="modal-dialog " style="min-width: 50vw;">
        <div class="modal-content " style="background-color: #F5F4ED !important; ">
            <div class="modal-body my-3 px-5 ">
                <h5 class="mt-2 mb-4"><u>Save and create your matches</u></h5>

                <form class="">

                    <input type="hidden" id="event_details_id" name="event_details_id" disabled placeholder=" " value="{{$event->id}}">
                    <input type="hidden" id="match_type" name="match_type" placeholder=" " required>
                    <input type="hidden" id="stage_name" name="stage_name" placeholder=" ">
                    <input type="hidden" id="inner_stage_name" name="inner_stage_name" placeholder=" ">
                    <input type="hidden" id="order" name="order" placeholder=" ">

                    <div class="row mb-3">
                        <!-- Team 1 Selection -->
                        <div class="col-12 mb-3">
                            <div class="input-group" style="max-width: 60%;">
                                <span class="input-group-text " id="team1-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                    </svg>
                                    Choose Team 1
                                </span>
                                <select class="form-select" id="team1_id" name="team1_id" required>
                                    <!-- Options go here -->
                                </select>
                            </div>
                        </div>

                        <!-- Team 1 Score -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div>
                                <span class="me-2">Score</span>
                                <input type="number" class=" form-control form-control-sm text-center d-inline"
                                    id="team1_score" name="team1_score" style="max-width: 80px;" min="0"
                                    value="0" aria-describedby="team1-score-addon">
                            </div>
                        </div>

                        <!-- Team 1 Position -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div>
                                <span class="me-2">Matchup</span>
                                <input type="text" class=" form-control form-control-sm d-inline "
                                    id="team1_position" name="team1_position" style="max-width: 100px;"
                                    aria-describedby="team1-position-addon" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Team 2 Selection -->
                        <div class="col-12 mb-3">
                            <div class="input-group" style="max-width: 60%;">
                                <span class="input-group-text " id="team2-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-person-plus me-3" viewBox="0 0 16 16">
                                        <path
                                            d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                        <path fill-rule="evenodd"
                                            d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5" />
                                    </svg>
                                    Choose Team 2
                                </span>
                                <select class="form-select" id="team2_id" name="team2_id" required>
                                    @foreach ($teamList as $team)
                                        <option value="{{ $team->id }}">
                                         <img src="/storage/$team->teamBanner" 
                                                width="30" height="30"
                                                onerror="this.src='/assets/images/404.png';"
                                                class="object-fit-cover rounded-circle me-2"
                                                alt="Team View"
                                            >
                                           <span> {{ $team->teamName }} </span>
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <!-- Team 2 Score -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div>
                                <span class="me-2">Score</span>
                                <input type="number" class=" form-control form-control-sm d-inline text-center"
                                    id="team2_score" name="team2_score" min="0" value="0"
                                    aria-describedby="team2-score-addon" style="max-width: 80px;">
                            </div>
                        </div>

                        <!-- Team 2 Position -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div>
                                <span class="me-2">Matchup</span>
                                <input type="text" class=" form-control form-control-sm d-inline" id="team2_position"
                                    name="team2_position" aria-describedby="team2-position-addon"
                                    style="max-width: 100px;" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="input-group" style="max-width: 60%;">
                                <span class="input-group-text " id="team2-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-trophy me-3" viewBox="0 0 16 16">
                                        <path
                                            d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z" />
                                    </svg>
                                    Select a winner
                                </span>
                                <select class="form-select" id="winner_id" name="winner_id"
                                    aria-describedby="winner-addon">
                                    <!-- Populate options with team data -->
                                </select>
                            </div>
                        </div>
                        <!-- Match Status -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div class="input-group">
                                <span class="input-group-text " id="status-addon">Status</span>
                                <select class="form-select" id="status" name="status"
                                    style="max-width: 160px;"
                                    aria-describedby="status-addon">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        <!-- Match Result -->
                        <div class="col-12 col-xxl-6 mb-3">
                            <div class="input-group">
                                <span class="input-group-text " id="result-addon">Result</span>
                                <select class="form-select" id="result" name="result"
                                    aria-describedby="result-addon" style="max-width: 160px;">
                                    <option value="draw">Draw</option>
                                    <option value="win">Win</option>
                                    <option value="dispute">Dispute</option>
                                </select>
                            </div>
                        </div>



                        <!-- Winner's Next Match -->
                        <div class="col-12 col-xxl-6 col- mb-3">
                            <div class="input-group">
                                <span class="input-group-text " id="winner-next-addon">Winner's next match</span>
                                <input type="text" class=" form-control form-control-sm" style="max-width: 100px;" id="winner_next_position"
                                    name="winner_next_position" aria-describedby="winner-next-addon">
                            </div>
                        </div>

                        <!-- Loser's Next Match -->
                        <div class="col-12 col-xxl-6 col- mb-3">
                            <div class="input-group">
                                <span class="input-group-text" id="loser-next-addon">Loser's
                                    next match</span>
                                <input type="text" style="max-width: 100px;" class=" form-control form-control-sm" id="loser_next_position"
                                    name="loser_next_position" aria-describedby="loser-next-addon">
                            </div>
                        </div>
                    </div>

                </form>

                <br>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary me-3 rounded-pill text-light px-4"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill text-light px-4">Save</button>
                </div>
            </div>

        </div>
    </div>
</div>
