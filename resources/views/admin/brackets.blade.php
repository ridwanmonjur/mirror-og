<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/petite-vue" defer></script>
</head>
<body>
    <div id="app" class="container mt-4">
        <h2>Match Details</h2>
        
        <!-- Add Create New Bracket button -->
        <div class="mb-3">
            <button class="btn btn-success" v-on:click="createNewBracket()" 
                    data-bs-toggle="modal" data-bs-target="#detailsModal">
                Create New Bracket
            </button>
        </div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Team 1 Position</th>
                    <th>Team 2 Position</th>
                    <th>Scores</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="match in brackets">
                    <td v-text="match.id"></td>
                    <td v-text="match.team1Name"></td>
                    <td v-text="match.team2Name"></td>
                    <td v-text="match.team1_position"></td>
                    <td v-text="match.team2_position"></td>
                    <td>
                        <span v-if="match.score">
                            <span v-text="(match.score && match.score[0] ? match.score[0] : '0' ) + '-' + (match.score && match.score[1] ? match.score[1]: '0')"></span>
                        </span>
                        <span v-else>
                            <span>0-0 (No scores)</span>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" v-on:click="openModal(match, 'brackets')" 
                                data-bs-toggle="modal" data-bs-target="#detailsModal">
                            Show Brackets
                        </button>
                        <button class="btn btn-success btn-sm" v-on:click="openModal(match, 'scores')" 
                                data-bs-toggle="modal" data-bs-target="#scoresModal">
                            Show Scores
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Brackets Modal with Input Fields -->
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalTitle">Match Brackets Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="selectedMatch">
                        <form id="bracketsForm">
                           
                            <input type="hidden"  name="id" :value="selectedMatch.id">
                               
                            <input type="hidden" name="order" :value="selectedMatch.order">
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 1 ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="team1_id" :value="selectedMatch.team1_id">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 2 ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="team2_id" :value="selectedMatch.team2_id">
                                </div>
                            </div>
                            
                            <input type="hidden" class="form-control" name="event_details_id" :value="selectedMatch.event_details_id">
                                
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 1 Position</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="team1_position" :value="selectedMatch.team1_position">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 2 Position</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="team2_position" :value="selectedMatch.team2_position">
                                </div>
                            </div>
                           
                            <input type="hidden" name="stage_name" :value="selectedMatch.stage_name">
                        
                    
                            <input type="hidden"  name="inner_stage_name" :value="selectedMatch.inner_stage_name">
                                
                            <input type="hidden"  name="created_at" :value="selectedMatch.created_at">
                            <input type="hidden" name="updated_at" :value="selectedMatch.updated_at">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="saveChanges('brackets')">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scores Modal with Input Fields -->
        <div class="modal fade" id="scoresModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Match Scores Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="selectedMatch">
                        <div v-if="selectedMatch.completeMatchStatus">
                            <form id="scoresForm">
                                <div class="mb-3 row">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Complete Match Status</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <select class="form-select d-inline" :name="completeMatchStatus" :value="selectedMatch.completeMatchStatus">
                                                <option value="null">No status</option>
                                                <option value="UPCOMING">UPCOMING</option>
                                                <option value="ONGOING">ONGOING</option>
                                                <option value="ENDED">ENDED</option>
                                            </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row" v-if="selectedMatch.disputeResolved">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Resolved</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.disputeResolved" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'disputeResolved['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- For organizerWinners -->
                                <div class="mb-3 row" v-if="selectedMatch.organizerWinners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Organizer Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.organizerWinners" :key="index">
                                        <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                        <select class="form-select d-inline" :name="'organizerWinners['+index+']'" :value="value">
                                            <option value="null">No teams</option>
                                            <option value="0">Team 1</option>
                                            <option value="1">Team 2</option>
                                        </select>
                                        </div>
                                    </div>
                                    </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Disqualified</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <input type="text" class="form-control" name="disqualified" :value="selectedMatch.disqualified">
                                    </div>
                                </div>
                                
                                <!-- For team2Winners -->
                                <div class="mb-3 row" v-if="selectedMatch.team2Winners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 2 Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.team2Winners" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'team2Winners['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- For team1Winners -->
                                <div class="mb-3 row" v-if="selectedMatch.team1Winners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 1 Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.team1Winners" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'team1Winners['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row" v-if="selectedMatch.score">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Score</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <div class="input-group">
                                            <span class="input-group-text">Team 1</span>
                                            <input type="number" class="form-control" name="score[0]" :value="selectedMatch.score[0]">
                                            <span class="input-group-text">Team 2</span>
                                            <input type="number" class="form-control" name="score[1]" :value="selectedMatch.score[1]">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- For defaultWinners -->
                                <div class="mb-3 row" v-if="selectedMatch.defaultWinners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Default Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.defaultWinners" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'defaultWinners['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 1 ID (Reference)</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <input type="text" class="form-control" name="team1Id" :value="selectedMatch.team1Id">
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Position</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <input type="text" class="form-control" name="position" :value="selectedMatch.position">
                                    </div>
                                </div>
                                
                                <!-- For matchStatus -->
                               <div class="mb-3 row" v-if="selectedMatch.matchStatus">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Match Status</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.matchStatus" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'matchStatus['+index+']'" :value="value">
                                                <option value="null">No status</option>
                                                <option value="UPCOMING">UPCOMING</option>
                                                <option value="ONGOING">ONGOING</option>
                                                <option value="ENDED">ENDED</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3 row">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Team 2 ID (Reference)</label>
                                    <div class="col-12 col-lg-8 col-xl-9">
                                        <input type="text" class="form-control" name="team2Id" :value="selectedMatch.team2Id">
                                    </div>
                                </div>
                                
                                <!-- For realWinners -->
                                <div class="mb-3 row" v-if="selectedMatch.realWinners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Real Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.realWinners" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'realWinners['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- For randomWinners -->
                                <div class="mb-3 row" v-if="selectedMatch.randomWinners">
                                    <label class="col-12 col-lg-4 col-xl-3 col-form-label">Random Winners</label>
                                    <div class="col-12 col-lg-8 col-xl-9 d-flex justify-content-center">
                                        <div class="input-group mb-2" v-for="(value, index) in selectedMatch.randomWinners" :key="index">
                                            <span class="input-group-text d-inline">Match @{{index + 1}}</span>
                                            <select class="form-select d-inline" :name="'randomWinners['+index+']'" :value="value">
                                                <option value="null">No teams</option>
                                                <option value="0">Team 1</option>
                                                <option value="1">Team 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-else>
                            <p>No score details available for this match.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="saveChanges('scores')">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store data in hidden input -->
    <input type="hidden" id="brackets-data" value="{{ json_encode($brackets) }}">
    
    <script>
        // Retrieve the brackets data from the hidden input
        const bracketsDataInput = document.getElementById('brackets-data');
        const bracketsData = JSON.parse(bracketsDataInput.value);
        
        // Initialize Petite-Vue
        window.addEventListener('DOMContentLoaded', () => {
            PetiteVue.createApp({
                brackets: bracketsData,
                selectedMatch: null,
                
                // Method to create new bracket
                createNewBracket: function() {
                    // Create empty bracket template
                    this.selectedMatch = {
                        id: '',
                        order: '',
                        team1_id: '',
                        team2_id: '',
                        event_details_id: '',
                        team1_position: '',
                        team2_position: '',
                        stage_name: '',
                        inner_stage_name: '',
                        created_at: new Date().toISOString(),
                        updated_at: new Date().toISOString(),
                        team1Name: '',
                        team2Name: '',
                        score: [0, 0]
                    };
                    
                    // Update modal title
                    const modalTitle = document.getElementById('detailsModalTitle');
                    if (modalTitle) {
                        modalTitle.textContent = 'Create New Bracket';
                    }
                    
                    // Set form mode to create
                    const form = document.getElementById('bracketsForm');
                    if (form) {
                        form.setAttribute('data-mode', 'create');
                    }
                },
                
                // Method to open a modal and set the selected match
                openModal: function(match, type) {
                    this.selectedMatch = JSON.parse(JSON.stringify(match));
                    
                    // Update modal title if it's the brackets modal
                    if (type === 'brackets') {
                        const modalTitle = document.getElementById('detailsModalTitle');
                        if (modalTitle) {
                            modalTitle.textContent = 'Match Brackets Details';
                        }
                    }
                    
                    // Set form mode to edit
                    const form = document.getElementById(type === 'brackets' ? 'bracketsForm' : 'scoresForm');
                    if (form) {
                        form.setAttribute('data-mode', 'edit');
                    }
                },
                
                // Save changes method that collects form data
                saveChanges: function(type) {
                    if (!this.selectedMatch) return;
                    
                    // Get the form element
                    const formElement = document.getElementById(type === 'brackets' ? 'bracketsForm' : 'scoresForm');
                    if (!formElement) return;
                    
                    // Check if creating or editing based on form data attribute
                    const isCreating = formElement.getAttribute('data-mode') === 'create';
                    
                    // Create FormData from the form
                    const formData = new FormData(formElement);
                    
                    // Convert FormData to an object structure matching the original data
                    const updatedData = {};
                    
                    // Process the form data
                    for (const [key, value] of formData.entries()) {
                        // Parse array notation in field names (e.g. 'score[0]')
                        if (key.includes('[') && key.includes(']')) {
                            const mainKey = key.substring(0, key.indexOf('['));
                            const indexStr = key.substring(key.indexOf('[') + 1, key.indexOf(']'));
                            const index = parseInt(indexStr);
                            
                            // Initialize array if it doesn't exist
                            if (!updatedData[mainKey]) {
                                updatedData[mainKey] = Array.isArray(this.selectedMatch[mainKey]) ? 
                                    [...this.selectedMatch[mainKey]] : [];
                            }
                            
                            // Update the value at the specified index
                            updatedData[mainKey][index] = value;
                        } else {
                            // For non-array fields, just update directly
                            updatedData[key] = value;
                        }
                    }
                    
                    // Merge updatedData with selectedMatch to preserve any fields not in the form
                    const updatedMatch = { ...this.selectedMatch, ...updatedData };
                    
                    if (isCreating) {
                        console.log('Creating new match:', updatedMatch);
                        // Add to brackets array
                        this.brackets.push(updatedMatch);
                    } else {
                        console.log('Saving changes for match:', this.selectedMatch.id, 'Type:', type);
                        // Update the brackets array
                        const index = this.brackets.findIndex(m => m.id === this.selectedMatch.id);
                        if (index !== -1) {
                            this.brackets.splice(index, 1, updatedMatch);
                            console.log('Updated match in brackets array');
                        }
                    }
                    
                    // Here you would add API call logic to save to server
                    // For example:
                    // const endpoint = isCreating ? '/api/matches' : '/api/matches/' + this.selectedMatch.id;
                    // const method = isCreating ? 'POST' : 'PUT';
                    // fetch(endpoint, {
                    //     method: method,
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //     },
                    //     body: JSON.stringify({
                    //         type: type,
                    //         data: updatedMatch
                    //     })
                    // })
                    
                    // Close the modal after saving
                    const modalElement = document.getElementById(type === 'brackets' ? 'detailsModal' : 'scoresModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            }).mount('#app');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>