 {{-- @php
    dd($brackets);
@endphp   --}}
 <!DOCTYPE html>
 <html lang="en">

 <head>
     @include('googletagmanager::head')
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Event Brackets</title>
     @include('includes.HeadIcon')
     <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
     {{-- <meta name="page-component" content="adminBrackets"> --}}
     @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/adminBrackets.js'])
 </head>

 <body>
     <div id="brackets" v-scope="createBrackets()" @vue:mounted="init" class="px-5 mt-4">
         <h2>Match Details</h2>

        <div  class="text-start">
            <div  class=" d-inline-block  text-start  py-3">
                
                    <img onerror="this.onerror=null;this.src='{{asset('assets/images/404q.png')}}';"
                        src="{{ '/storage' . '/'.  $event['eventBanner'] }}"
                        class="object-fit-cover float-left border border-primary rounded-circle me-1" width="30" height="30"
                    >
                   
                    <p class="py-0 my-0 ms-2 mb-2 d-inline"> {{ $event['eventName'] }} </p>
                
            </div>
        </div>
        

         <table class="table table-striped">
             <thead>
                 <tr>
                     <th class="text-center">ID</th>
                     <th class="text-center">Team 1</th>
                     <th class="text-center">Team 2</th>
                     <th class="text-center">Team 1 Position</th>
                     <th class="text-center">Team 2 Position</th>
                     <th class="text-center">Scores</th>
                     <th class="text-center">Actions</th>
                 </tr>
             </thead>
             <tbody>
                 <tr v-for="match in brackets">
                     <td  class="text-center" v-text="match.id"></td>
                     <td class="text-center" v-text="match.team1Name"></td>
                     <td class="text-center" v-text="match.team2Name"></td>
                     <td class="text-center" v-text="match.team1_position"></td>
                     <td class="text-center" v-text="match.team2_position"></td>
                     <td class="text-center">
                         <span v-if="match.id">
                             <span>
                                 <span v-if="match.score">
                                     <span
                                         v-text="(match.score && match.score[0] ? match.score[0] : '0' ) + '-' + (match.score && match.score[1] ? match.score[1]: '0')"></span>
                                 </span>
                                 <span v-else>
                                     <span>0-0 (No scores)</span>
                                 </span>
                             </span>
                         </span>
                         <span v-else>
                             <span>0-0 (No teams)</span>
                         </span>

                     </td>
                     <td class="text-center">
                         <button class="btn rounded-pill btn-primary text-light btn-sm" v-on:click="openModal(match, 'brackets')"
                             data-bs-toggle="modal" data-bs-target="#detailsModal">
                             Show Brackets
                         </button>
                         <button class="btn rounded-pill btn-success btn-sm" v-on:click="openModal(match, 'scores')"
                             v-if="match.team1_position != 'F'"
                             data-bs-toggle="modal" data-bs-target="#scoresModal">
                             Show Scores
                         </button>
                     </td>
                 </tr>
             </tbody>
         </table>

         <!-- Brackets Modal with Input Fields -->
         <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
             <div class="modal-dialog ">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title  " id="detailsModalTitle">Match Brackets Details</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body" v-if="selectedMatch">
                         <form id="bracketsForm" method="POST"
                             action="{{ route('event.matches.upsert', ['id' => $event['id']]) }}">

                             <input type="hidden" name="id" v-bind:value="selectedMatch.id">

                             <input type="hidden" name="order" v-bind:value="selectedMatch.order">
                             <div class="mb-3 form-floating">
                                <select class="form-control" name="team1_id" v-model="selectedMatch.team1_id">
                                    <option value="null">Select Team 1</option>
                                    <option v-for="team in teams" :key="team.id" v-bind:value="team.id"
                                        v-text="team.teamName">
                                    </option>
                                </select>
                                <label >Team 1</label>
                             </div>
                             <div class="mb-3 form-floating" v-show="selectedMatch.team1_position != 'F'">
                                <select class="form-control" name="team2_id" v-model="selectedMatch.team2_id">
                                    <option value="null">Select Team 2</option>
                                    <option v-for="team in teams" :key="team.id" v-bind:value="team.id"
                                        v-text="team.teamName">
                                    </option>
                                </select>
                                 <label >Team 2</label>
                                
                             </div>

                             <input type="hidden" name="event_details_id"
                                 v-bind:value="selectedMatch.event_details_id">

                             <div class="mb-3 form-floating">
                                <input readonly disabled type="text" class="form-control" name="team1_position"
                                    v-bind:value="selectedMatch.team1_position">
                                 <label >Team 1 Position</label>

                             </div>
                             <div  class="mb-3 form-floating">
                                <input readonly disabled type="text" class="form-control" name="team2_position"
                                    v-bind:value="selectedMatch.team2_position">
                                <label>Team 2 Position</label>

                             </div>

                             <input type="hidden" name="stage_name" v-bind:value="selectedMatch.stage_name">


                             <input type="hidden" name="inner_stage_name"
                                 v-bind:value="selectedMatch.inner_stage_name">

                             <input type="hidden" name="created_at" v-bind:value="selectedMatch.created_at">
                             <input type="hidden" name="updated_at" v-bind:value="selectedMatch.updated_at">
                         </form>
                     </div>
                     <div class="modal-footer mx-auto">
                         <button type="button" class="btn rounded-pill btn-primary text-light " v-on:click="saveChanges('brackets')">Save
                             Changes</button>
                         <button type="button" class="btn rounded-pill btn-secondary text-light " data-bs-dismiss="modal">Close</button>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Scores Modal with Input Fields -->
         <div class="modal fade" id="scoresModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
             <div class="modal-dialog modal-xl">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title  ">Match Scores Details</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal"
                             aria-label="Close"></button>
                     </div>
                     <div class="modal-body" v-if="selectedMatch">
                         <form id="scoresForm">
                             <div class="mb-3 row" >
                                 <label  class="col-12 col-lg-4 col-xl-3 col-form-label">Complete Match Status</label>
                                 <div class="col-12 col-lg-6 col-xl-4 " >
                                     <select class="form-select d-inline" name="completeMatchStatus"
                                         v-bind:value="selectedMatch.completeMatchStatus">
                                         <option value="null">No status</option>
                                         <option value="UPCOMING">UPCOMING</option>
                                         <option value="ONGOING">ONGOING</option>
                                         <option value="ENDED">ENDED</option>
                                     </select>
                                 </div>
                             </div>
                             

                             <!-- For matchStatus -->
                             <div class="mb-3 row" v-if="selectedMatch.matchStatus">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Match Status</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.matchStatus" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'matchStatus'+index"
                                                 v-bind:name="'matchStatus['+index+']'" v-bind:value="value">
                                                 <option value="null">No status</option>
                                                 <option value="UPCOMING">UPCOMING</option>
                                                 <option value="ONGOING">ONGOING</option>
                                                 <option value="ENDED">ENDED</option>
                                             </select>
                                             <label v-bind:for="'matchStatus'+index">Match <span
                                                     v-text="index + 1"></span>
                                             </label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For disputeResolved -->
                             <div class="mb-3 row" v-if="selectedMatch.disputeResolved">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Resolved</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.disputeResolved"
                                         :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'disputeResolved'+index"
                                                 v-bind:name="'disputeResolved['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'disputeResolved'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For organizerWinners -->
                             <div class="mb-3 row" v-if="selectedMatch.organizerWinners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Organizer Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.organizerWinners"
                                         :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'organizerWinners'+index"
                                                 v-bind:name="'organizerWinners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'organizerWinners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>



                             <!-- For team1Winners -->
                             <div class="mb-3 row" v-if="selectedMatch.team1Winners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 1 Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.team1Winners" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'team1Winners'+index"
                                                 v-bind:name="'team1Winners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'team1Winners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For team2Winners -->
                             <div class="mb-3 row" v-if="selectedMatch.team2Winners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 2 Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.team2Winners" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'team2Winners'+index"
                                                 v-bind:name="'team2Winners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'team2Winners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <div class="mb-3 row" v-if="selectedMatch.score">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Score</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-3">
                                         <div class="form-floating mb-3">
                                             <input type="number" class="form-control" name="score[0]"
                                                 v-bind:value="selectedMatch.score[0]">
                                             <label>Team 1</label>
                                         </div>
                                     </div>
                                     <div class="col-12 col-lg-6 col-xl-4">
                                         <div class="form-floating mb-3">
                                             <input type="number" class="form-control" name="score[1]"
                                                 v-bind:value="selectedMatch.score[1]">
                                             <label>Team 2</label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <input type="hidden" name="team1Id" v-bind:value="selectedMatch.team1Id">

                             <input type="hidden" name="position" v-bind:value="selectedMatch.position">

                             <input type="hidden" name="team2Id" v-bind:value="selectedMatch.team2Id">

                             <!-- For defaultWinners -->
                             <div class="mb-3 row" v-if="selectedMatch.defaultWinners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Default Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.defaultWinners" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'defaultWinners'+index"
                                                 v-bind:name="'defaultWinners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'defaultWinners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For realWinners -->
                             <div class="mb-3 row" v-if="selectedMatch.realWinners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Real Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.realWinners" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'realWinners'+index"
                                                 v-bind:name="'realWinners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'realWinners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For randomWinners -->
                             <div class="mb-3 row" v-if="selectedMatch.randomWinners">
                                 <label class="col-12 col-lg-4 col-xl-3 col-form-label">Random Winners</label>
                                 <div class="col-12 col-lg-8 col-xl-9 row">
                                     <div class="col-12 col-lg-6 col-xl-4 mb-2"
                                         v-for="(value, index) in selectedMatch.randomWinners" :key="index">
                                         <div class="form-floating">
                                             <select class="form-select" v-bind:id="'randomWinners'+index"
                                                 v-bind:name="'randomWinners['+index+']'" v-bind:value="value">
                                                 <option value="null">No teams</option>
                                                 <option value="0">Team 1</option>
                                                 <option value="1">Team 2</option>
                                             </select>
                                             <label v-bind:for="'randomWinners'+index">Match <span
                                                     v-text="index + 1"></span></label>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <!-- For randomWinners -->

                         </form>

                     </div>
                     <div class="modal-body" v-else>
                         <div>
                             <p>No teams available for this match. First select teams.</p>
                         </div>
                     </div>
                     <div class="modal-footer mx-auto text-center">
                         <button type="button" class="btn rounded-pill btn-primary text-light " v-on:click="saveChanges('scores')">Save
                             Changes</button>
                         <button type="button" class="btn rounded-pill btn-secondary text-light " data-bs-dismiss="modal">Close</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Store data in hidden input -->
     <input type="hidden" id="brackets-data" value="{{ json_encode($brackets) }}">
     <input type="hidden" id="teams-data" value="{{ json_encode($teams) }}">
     {{-- <input type="hidden" id="event-data" value="{{ json_encode($event) }}"> --}}



 </body>

 </html>
