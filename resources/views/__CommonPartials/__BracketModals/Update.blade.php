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
                                        <span id="team1_position_label"> </span>
                                    </span>
                                    <select  class="form-select" id="team1_id" name="team1_id" required  data-dynamic-select>
                                            <option value="">Choose a team</option>
                                        @foreach ($teamList as $team)
                                            <option  value="{{ $team['id'] }}"   data-img-width="40px" data-img-height="100%"
                                                data-img="{{ isset($team['teamBanner']) ? '/storage/' . $team['teamBanner'] : '/assets/images/.png' }}"
                                            >
                                            <span> {{ $team['teamName'] }} </span>
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                      
                            <div class="input-group mb-2 team2-toggle" >
                                @if(!isset($teamList[0]))
                                    <p>No teams available to select.</p>
                                @else
                                    <span class="input-group-text " id="team1-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                        </svg>
                                        <span id="team2_position_label"> </span> 
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