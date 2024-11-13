<div class="modal fade" id="updateModal" tabindex="1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content " style="background-color: #F5F4ED !important; ">
            <div class="modal-body my-3 px-5 ">
                <h5 class="mt-2 mb-2"><u>Save and create your matches</u></h5>
                <br>
                <form method="POST" class="px-4" action="{{ route('event.matches.upsert', ['id'=> $event->id]) }}" id="matchForm">
                    @csrf
                    <input type="hidden" id="id" name="id" >
                    <input type="hidden" id="event_details_id" name="event_details_id"   value="{{$event->id}}">
                    <input type="hidden" id="match_type" name="match_type"  required>
                    <input type="hidden" id="stage_name" name="stage_name" >
                    <input type="hidden" id="inner_stage_name" name="inner_stage_name" >
                    <input type="hidden" id="order" name="order" >
                    <input type="hidden" id="team1_position" name="team1_position" >
                    <input type="hidden" id="team2_position" name="team2_position" >
                    <input type="hidden" id="team1_score" name="team1_score" value="0">
                    <input type="hidden" id="team2_score" name="team2_score" value="0">
                    <input type="hidden"  id="winner_next_position" name="winner_next_position">
                    <input type="hidden"  id="loser_next_position" name="loser_next_position">
                    
                    <div class="row mb-3">
                        <!-- Team 1 Selection -->
                            <div class="input-group mb-2" >
                                @if(!isset($teamList[0]))
                                    <p>No teams available to select.</p>
                                @else
                                    <span class="input-group-text " id="team1-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                        </svg>
                                        Team 1 (<span id="team1_position_label"> </span>) 
                                    </span>
                                    <select  class="form-select" id="team1_id" name="team1_id" required  data-dynamic-select>
                                            <option value="">Choose a team</option>
                                        @foreach ($teamList as $team)
                                            <option  value="{{ $team['id'] }}"   data-img-width="40px" data-img-height="100%"
                                                data-img="{{ '/' . 'storage/' . $team['teamBanner']}}"
                                            >
                                            <span> {{ $team['teamName'] }} </span>
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                      
                            <div class="input-group mb-2" >
                                @if(!isset($teamList[0]))
                                    <p>No teams available to select.</p>
                                @else
                                    <span class="input-group-text " id="team1-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                        </svg>
                                        Team 2 (<span id="team2_position_label"> </span>) 
                                    </span>
                                    <select  class="form-select" id="team2_id" name="team2_id" required  data-dynamic-select>
                                            <option value="">Choose a team</option>
                                        @foreach ($teamList as $team)
                                            <option class="object-fit-cover" value="{{ $team['id'] }}"   data-img-width="40px" data-img-height="100%"
                                                data-img="{{ '/' . 'storage/' . $team['teamBanner']}}"
                                            >
                                            <span> {{ $team['teamName'] }} </span>
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="input-group mb-2" >
                                @if(!isset($teamList[0]))
                                    <p>No teams available to select.</p>
                                @else
                                    <span class="input-group-text " id="team2-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-trophy me-3" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z" />
                                        </svg>
                                        Winner (<span  id="winner_next_position_label"> </span>)
                                    </span>
                                    <select class="form-select" id="winner_id" name="winner_id"  data-dynamic-select
                                        aria-describedby="winner-addon">
                                        <option value="">Choose a team</option>
                                        @foreach ($teamList as $team)
                                            <option value="{{ $team['id'] }}"  data-img-width="40px" data-img-height="100%" data-img="{{ '/' . 'storage/' . $team['teamBanner']}}">
                                            <span> {{ $team['teamName'] }} </span>
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="input-group mb-2">
                                <span class="input-group-text " id="status-addon">Status</span>
                                <select class="form-select" id="status" name="status"
                                    data-dynamic-select
                                    aria-describedby="status-addon">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                     
                            <div class="input-group mb-2">
                                <span class="input-group-text " id="result-addon">Result</span>
                                <select class="form-select" id="result" name="result" data-dynamic-select
                                    aria-describedby="result-addon" >
                                    <option value="draw">Draw</option>
                                    <option value="win">Win</option>
                                    <option value="dispute">Dispute</option>
                                </select>
                            </div>
                        
                    </div>
                    <hr>
                    <div>
                        <i>** Loser's next match: <span id="loser_next_position_label"></span></i>
                    </div>
                    <br>
                    <div class="d-flex justify-content-center">
                        <button id="closeBtn" type="button" class="btn btn-secondary me-3 rounded-pill text-light px-4"
                            data-bs-dismiss="modal">Close</button>
                        <button id="submitBtn" type="button" class="btn btn-primary rounded-pill text-light px-4">Save</button>
                    </div>
                </form>

            </div>

        </div>
    </div>

    {{-- <script src="{{ asset('/assets/js/shared/BracketUpdateModal.js') }}"></script> --}}
   
</div>