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
    @include('includes.Navbar')
    <main>
        <br><br><br>

        <div class="container mx-auto">
            <form id="betaUsersForm" action="{{ route('admin.onboardBeta.action') }}" method="POST">
                @csrf
                @include('includes.Flash')
                <div class="table-responsive ">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="px-3">
                                    <input type="checkbox"  id="selectAll" class="form-check-input">
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

        <script src="{{ asset('/assets/js/organizer/SendBetaUser.js') }}"></script>
            
    </main>
</body>

</html>
