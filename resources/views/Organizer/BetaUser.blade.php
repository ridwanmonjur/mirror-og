// resources/views/emails/beta-users.blade.php
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Beta Users Email Management</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#previewEmailModal">
                Preview Email Template
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('beta-users.send-emails') }}" method="POST">
                @csrf
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">
                            Select All Users
                        </label>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">Select</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($betaUsers as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" 
                                                name="selected_users[]" 
                                                value="{{ $user->id }}"
                                                @if($user->welcome_email_sent) disabled @endif>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>
                                        @if($user->welcome_email_sent)
                                            <span class="badge bg-success">Email Sent</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No beta users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="sendEmailsBtn">
                        Send Welcome Emails
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Email Modal -->
<div class="modal fade" id="previewEmailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <strong>Subject:</strong> Welcome to Driftwood's Closed Beta!
                    </div>
                    <div class="card-body">
                        <p>Ahoy from Driftwood! We're excited to invite you to Driftwood as a closed beta user!</p>
                        <p>You can sign into your account using the following temporary credentials:</p>
                        <p>Email address: [User Email]<br>
                        Password: [Temporary Password]<br>
                        Username: [Username]</p>
                        <p>Sign into Driftwood here: [Sign In Link]</p>
                        <p>Note that the given username and password are only temporary. You can change them by signing into your account and clicking on your profile icon to go to your settings. From there, you can select a new username and password under "Account Details and Security".</p>
                        <p>We're still refining many of our features, so thank you for being patient. If you need any support, ping us at supportmain@driftwood.gg and we'll come to your aid.</p>
                        <p>See you on Driftwood!</p>
                        <p>Best wishes,<br>
                        Leigh<br>
                        The Driftwood Team<br>
                        driftwood.gg</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
    const sendEmailsBtn = document.getElementById('sendEmailsBtn');

    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSendButtonState();
    });

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSendButtonState);
    });

    function updateSendButtonState() {
        const anyChecked = Array.from(userCheckboxes).some(checkbox => checkbox.checked);
        sendEmailsBtn.disabled = !anyChecked;
    }

    updateSendButtonState();
});
</script>