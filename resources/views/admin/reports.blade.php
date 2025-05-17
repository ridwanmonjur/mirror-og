 {{-- @php
    dd($brackets);
@endphp   --}}
 <!DOCTYPE html>
 <html lang="en">

 <head>
     @include('googletagmanager::head')
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Event Disputes</title>
     @include('includes.HeadIcon')
     <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
     {{-- <meta name="page-component" content="adminBrackets"> --}}
     @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/adminBrackets.js'])
 </head>
<body>
    <div id="disputes" v-scope="createDisputes()" @vue:mounted="init" class="px-4 mt-4">
        <h2>Dispute Management</h2>
        
        <!-- Add Create New Dispute button -->
        <div class="mb-3">
            <button class="btn btn-success" v-on:click="createNewDispute()" 
                    data-bs-toggle="modal" data-bs-target="#disputeModal">
                Create New Dispute
            </button>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">Report ID</th>
                    <th class="text-center">Match Number</th>
                    <th class="text-center">Event ID</th>
                    <th class="text-center">Dispute Reason</th>
                    <th class="text-center">Dispute Team</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="dispute in disputes">
                    <td  class="text-center" v-text="dispute.report_id"></td>
                    <td  class="text-center" v-text="dispute.match_number+1"></td>
                    <td  class="text-center" v-text="dispute.event_id"></td>
                    <td  class="text-center" v-text="dispute.dispute_reason"></td>
                    <td  class="text-center" v-text="dispute.dispute_teamId"></td>
                    <td class="text-center" >
                        <span v-if="dispute.resolution_winner > 0" class="badge bg-success">Resolved</span>
                        <span v-else class="badge bg-warning">Pending</span>

                    </td>
                    <td class="text-center" >
                        <button class="btn btn-primary btn-sm" v-on:click="openModal(dispute)" 
                                data-bs-toggle="modal" data-bs-target="#disputeModal">
                            Resolve
                        </button>
                        
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Dispute Modal with Input Fields -->
        <div class="modal fade" id="disputeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="disputeModalTitle">Dispute Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="selectedDispute">
                        <form id="disputeForm" class="px-3">
                          
                            <input type="hidden"  name="id" :value="selectedDispute.id" readonly>
                        
                            <input type="hidden"  name="report_id" :value="selectedDispute.report_id">
                             
                            <div class="form-floating mb-3">
                                <select class="form-select" v-bind:id ="'match_number'" name="match_number" v-bind:value="selectedDispute.match_number">
                                    <option value="null">No teams</option>
                                    <option value="0">Match 1</option>
                                    <option value="1">Match 2</option>
                                    <option value="2">Match 3</option>
                                </select>
                                <label>Match Number</label>

                            </div>
                            
                            <input type="hidden" class="form-control" name="event_id" :value="selectedDispute.event_id">
                               
                            
                            <div class="mb-3 form-floating">
                                <select class="form-control" name="dispute_userId" v-model="selectedDispute.dispute_userId">
                                    <option value="" >Select User</option>
                                    <option v-for="user in users" :key="user.user_id" v-bind:value="user.user_id" > 
                                        <span v-text="user.name"></span>
                                    </option>
                                </select>
                                <label >Dispute User</label>
                             </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" name="dispute_teamId" :value="selectedDispute.dispute_teamId">
                                <label >Dispute Team ID</label>

                            </div>
                            <div class="mb-3 form-floating">
                                <select class="form-select" v-bind:id ="'dispute_teamNumber'" name="dispute_teamNumber" v-bind:value="selectedDispute.dispute_teamNumber">
                                    <option value="null">No teams</option>
                                    <option value="0">Team 1</option>
                                    <option value="1">Team 2</option>
                                </select>
                                <label >Dispute Team Number</label>
                            </div>

                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" name="dispute_reason" :value="selectedDispute.dispute_reason">
                                <label>Dispute Reason</label>

                            </div>
                            <div class="mb-3 form-floating">
                                <textarea class="form-control" name="dispute_description"  style="height: 100px" :value="selectedDispute.dispute_description"></textarea>
                                <label>Dispute Description</label>
                            </div>
                            <div class="mb-2">
                                <template v-if="selectedDispute?.dispute_image_videos && selectedDispute?.dispute_image_videos[0]">
                                    <div>
                                        <p class="my-0">Dispute Image/ Video Evidence: </p>
                                        <div class="d-flex justify-content-start">
                                            <template v-for="imgVideo in selectedDispute.dispute_image_videos"
                                                :key="imgVideo">
                                                <div class="cursor-pointer">
                                                    <template v-if="imgVideo.startsWith('media/img')">
                                                        <img v-bind:src="'/storage/' + imgVideo"
                                                            class="object-fit-cover border border-secondary me-2"
                                                            v-on:click="showImageModal(imgVideo, 'image')" height="100px" width="100px" />
                                                    </template>

                                                    <template v-else>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            class="me-3"
                                                            v-on:click="showImageModal(imgVideo, 'video')"
                                                            width="60"
                                                            height="60"
                                                        >
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" fill="#666666"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" name="response_userId" :value="selectedDispute.response_userId">
                                <label>Response User</label>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" name="response_teamId" :value="selectedDispute.response_teamId">
                                <label>Response Team</label>
                            </div>

                             <div class="mb-3 form-floating">
                                <textarea class="form-control" name="response_explanation" style="height: 100px"  rows="3" :value="selectedDispute.response_explanation"></textarea>
                                <label >Response Explanation</label>
                            </div>

                            <div class="mb-3 form-floating">
                                <select class="form-select" id ="response_teamNumber" name="response_teamNumber" v-bind:value="selectedDispute.response_teamNumber">
                                    <option value="null">No teams</option>
                                    <option value="0">Team 1</option>
                                    <option value="1">Team 2</option>
                                </select>
                                <label >Response Team Number</label>
                            </div>
                           
                            <div class="mb-3 form-floating">
                                <input type="text" class="form-control" name="resolution_resolved_by" :value="selectedDispute.resolution_resolved_by">

                                <label >Resolution Resolved by</label>
                            </div>
                            <div class="mb-3 form-floating">
                                <select class="form-select" v-bind:id ="'resolution_winner'" name="resolution_winner" v-bind:value="selectedDispute.resolution_winner">
                                    <option value="null">No teams</option>
                                    <option value="0">Team 1</option>
                                    <option value="1">Team 2</option>
                                </select>
                                        
                                <label >Resolution Winner</label>

                            </div>
                            
                           
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" v-on:click="saveChanges('dispute')">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
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
      
        <input type="hidden" id="disputes-data" value="{{ json_encode($disputes) }}">
        <input type="hidden" id="teams-data" value="{{ json_encode($teams) }}">
        <input type="hidden" id="users-data" value="{{ json_encode($users) }}">

    </div>
    
         
</body>
</html>