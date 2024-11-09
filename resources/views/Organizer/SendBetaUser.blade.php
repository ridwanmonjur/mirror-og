<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beta User</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
     @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 
    ])
</head>
@php
    use Carbon\Carbon;
@endphp
<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <br><br><br>

        <div class="container mx-auto">
            <form id="betaUsersForm" action="{{ route('admin.onboardBeta.action') }}" method="POST">
                @csrf
                @include('__CommonPartials.Flash')
                <div class="table-responsive ">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="px-3">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th class="px-3">Email</th>
                                <th class="px-3">Email verified</th>
                                <th class="px-3">Created At</th>
                                <th class="px-3">Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    @php
                                        $created_at = Carbon::parse($user->created_at)->diffForHumans();
                                        $updated_at = Carbon::parse($user->updated_at)->diffForHumans();
                                        $email_verified_at = 'Not verified';
                                        if ($user->email_verified_at) {
                                            $email_verified_at = Carbon::parse($user->email_verified_at)->diffForHumans();
                                        }
                                    @endphp
                                    <td class="px-3">
                                        <input type="checkbox" name="idList[]" value="{{ $user->id }}"
                                            class="form-check-input user-checkbox"
                                        >
                                    </td>
                                    <td class="px-3">{{ $user->email }}</td>
                                    <td class="px-3">{{ $email_verified_at }}</td>
                                    <td class="px-3">{{ $created_at }}</td>
                                    <td class="px-3">{{ $updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="submit" class="btn btn-primary text-white" id="sendButton" disabled>
                        Send Invites
                    </button>
                    {{ $users->links() }}
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const userCheckboxes = document.getElementsByClassName('user-checkbox');
                const sendButton = document.getElementById('sendButton');

                selectAllCheckbox.addEventListener('change', function() {
                    Array.from(userCheckboxes).forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    updateSendButton();
                });

                Array.from(userCheckboxes).forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                        updateSendButton();
                    });
                });

                function updateSendButton() {
                    const hasChecked = Array.from(userCheckboxes).some(cb => cb.checked);
                    sendButton.disabled = !hasChecked;
                }

                document.getElementById('betaUsersForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const checkedIds = Array.from(userCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);

                    if (checkedIds.length === 0) {
                        alert('Please select at least one user.');
                        return;
                    }

                    this.submit();
                });
            });
        </script>
    </main>
</body>

</html>
