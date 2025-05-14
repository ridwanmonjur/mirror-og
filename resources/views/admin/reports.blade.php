<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispute Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/petite-vue" defer></script>
</head>
<body>
    <div id="app" class="container mt-4">
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
                    <th>Report ID</th>
                    <th>Match Number</th>
                    <th>Event ID</th>
                    <th>Dispute Reason</th>
                    <th>Dispute Team</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="dispute in disputes">
                    <td v-text="dispute.report_id"></td>
                    <td v-text="dispute.match_number"></td>
                    <td v-text="dispute.event_id"></td>
                    <td v-text="dispute.dispute_reason"></td>
                    <td v-text="dispute.dispute_teamId"></td>
                    <td>
                        <span v-if="dispute.resolution_winner == '0'" class="badge bg-warning">Pending</span>
                        <span v-else-if="dispute.resolution_winner > 0" class="badge bg-success">Resolved</span>
                    </td>
                    <td>
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
                        <form id="disputeForm">
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="id" :value="selectedDispute.id" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Report ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="report_id" :value="selectedDispute.report_id">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Match Number</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="match_number" :value="selectedDispute.match_number">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Event ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="event_id" :value="selectedDispute.event_id">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute User ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="dispute_userId" :value="selectedDispute.dispute_userId">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Team ID</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="dispute_teamId" :value="selectedDispute.dispute_teamId">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Team Number</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="dispute_teamNumber" :value="selectedDispute.dispute_teamNumber">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Reason</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="dispute_reason" :value="selectedDispute.dispute_reason">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Description</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <textarea class="form-control" name="dispute_description" rows="3" :value="selectedDispute.dispute_description"></textarea>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Dispute Media</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <div v-if="selectedDispute.dispute_image_videos && selectedDispute.dispute_image_videos.length > 0">
                                        <p>Media attached: @{{ selectedDispute.dispute_image_videos.length }} item(s)</p>
                                        <button type="button" class="btn btn-sm btn-info">View Media</button>
                                    </div>
                                    <div v-else>
                                        <p>No media attached</p>
                                        <input type="file" class="form-control" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Response Status</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <p v-if="selectedDispute.response_userId" class="form-control-plaintext">
                                        Response received from Team ID: @{{ selectedDispute.response_teamId }}
                                    </p>
                                    <p v-else class="form-control-plaintext">
                                        No response received yet
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Resolution Status</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <p v-if="selectedDispute.resolution_resolved_by" class="form-control-plaintext">
                                        Resolved by Admin ID: @{{ selectedDispute.resolution_resolved_by }}
                                    </p>
                                    <p v-else class="form-control-plaintext">
                                        Not resolved yet
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Resolution Winner:</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <select class="form-select" name="resolution_winner" :value="resolutionData.winner">
                                        <option value="0">Team 1 Wins Dispute</option>
                                        <option value="1">Team 1 Wins Dispute</option>
                                        <option :value="null">No Change to Result</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Resolved By (Admin ID):</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <input type="text" class="form-control" name="resolution_resolved_by" :value="resolutionData.resolved_by">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-12 col-lg-4 col-xl-3 col-form-label">Resolution Notes:</label>
                                <div class="col-12 col-lg-8 col-xl-9">
                                    <textarea class="form-control" name="resolution_notes" rows="3" :value="resolutionData.notes"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="saveChanges('dispute')">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
      
    <input type="hidden" id="disputes-data" value="{{ json_encode($disputes) }}">

    </div>
    
    <!-- Sample data (would normally come from backend) -->
    <script>
         const disputesDataInput = document.getElementById('disputes-data');
        const disputesData = JSON.parse(disputesDataInput.value);
        // Initialize Petite-Vue
        window.addEventListener('DOMContentLoaded', () => {
            PetiteVue.createApp({
                // Sample dispute data
                disputes: disputesData,
                
                selectedDispute: null,
                resolutionData: {
                    winner: "0",
                    resolved_by: "",
                    notes: ""
                },
                
                // Method to create new dispute
                createNewDispute: function() {
                    // Create empty dispute template with default values
                    this.selectedDispute = {
                        id: '',
                        dispute_userId: '',
                        resolution_resolved_by: null,
                        created_at: new Date().toISOString(),
                        response_explanation: null,
                        dispute_description: '',
                        response_teamNumber: null,
                        report_id: '',
                        match_number: '',
                        response_userId: null,
                        dispute_reason: '',
                        dispute_image_videos: [],
                        resolution_winner: "0",
                        updated_at: new Date().toISOString(),
                        response_teamId: null,
                        dispute_teamNumber: '',
                        event_id: '',
                        dispute_teamId: ''
                    };
                    
                    // Update modal title
                    const modalTitle = document.getElementById('disputeModalTitle');
                    if (modalTitle) {
                        modalTitle.textContent = 'Create New Dispute';
                    }
                    
                    // Set form mode to create
                    const form = document.getElementById('disputeForm');
                    if (form) {
                        form.setAttribute('data-mode', 'create');
                    }
                },
                
                // Method to open dispute modal
                openModal: function(dispute) {
                    this.selectedDispute = JSON.parse(JSON.stringify(dispute));
                    
                    // Update modal title
                    const modalTitle = document.getElementById('disputeModalTitle');
                    if (modalTitle) {
                        modalTitle.textContent = 'Dispute Details';
                    }
                    
                    // Set form mode to edit
                    const form = document.getElementById('disputeForm');
                    if (form) {
                        form.setAttribute('data-mode', 'edit');
                    }
                },
                
                // Method to open resolution modal
                openResolutionModal: function(dispute) {
                    this.selectedDispute = JSON.parse(JSON.stringify(dispute));
                    this.resolutionData = {
                        winner: dispute.resolution_winner || "0",
                        resolved_by: dispute.resolution_resolved_by || "",
                        notes: ""
                    };
                },
                
                // Save dispute changes
                saveChanges: function(type) {
                    if (!this.selectedDispute) return;
                    
                    // Get the form element
                    const formElement = document.getElementById('disputeForm');
                    if (!formElement) return;
                    
                    // Collect all form values
                    const formData = new FormData(formElement);
                    const formValues = {};
                    for (const [key, value] of formData.entries()) {
                        formValues[key] = value;
                    }
                    
                    // Update selectedDispute with form values only on save
                    for (const key in formValues) {
                        if (key in this.selectedDispute) {
                            this.selectedDispute[key] = formValues[key];
                        }
                    }
                    
                    // Manually handle resolution data
                    if (formValues.resolution_winner) {
                        this.selectedDispute.resolution_winner = formValues.resolution_winner;
                    }
                    if (formValues.resolution_resolved_by) {
                        this.selectedDispute.resolution_resolved_by = formValues.resolution_resolved_by;
                    }
                    
                    // Check if creating or editing based on form data attribute
                    const isCreating = formElement.getAttribute('data-mode') === 'create';
                    
                    // For creating, generate a new ID if not provided
                    if (isCreating && !this.selectedDispute.id) {
                        // Generate ID based on report_id and match_number
                        if (this.selectedDispute.report_id && this.selectedDispute.match_number) {
                            this.selectedDispute.id = this.selectedDispute.report_id + '.' + this.selectedDispute.match_number;
                        } else {
                            // Fallback to timestamp-based ID
                            this.selectedDispute.id = 'D' + Date.now();
                        }
                    }
                    
                    // Update timestamps
                    this.selectedDispute.updated_at = new Date().toISOString();
                    if (isCreating) {
                        this.selectedDispute.created_at = new Date().toISOString();
                    }
                    
                    if (isCreating) {
                        console.log('Creating new dispute:', this.selectedDispute);
                        // Add to disputes array
                        this.disputes.push(JSON.parse(JSON.stringify(this.selectedDispute)));
                    } else {
                        console.log('Saving changes for dispute:', this.selectedDispute.id);
                        // Update the disputes array
                        const index = this.disputes.findIndex(d => d.id === this.selectedDispute.id);
                        if (index !== -1) {
                            this.disputes.splice(index, 1, JSON.parse(JSON.stringify(this.selectedDispute)));
                            console.log('Updated dispute in array');
                        }
                    }
                    
                    // Here you would add API call logic to save to server
                    // For example:
                    // const endpoint = isCreating ? '/api/disputes' : '/api/disputes/' + this.selectedDispute.id;
                    // const method = isCreating ? 'POST' : 'PUT';
                    // fetch(endpoint, {
                    //     method: method,
                    //     headers: { 'Content-Type': 'application/json' },
                    //     body: JSON.stringify(this.selectedDispute)
                    // })
                    
                    // Close the modal after saving
                    const modalElement = document.getElementById('disputeModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                },
                
            }).mount('#app');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>