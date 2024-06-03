<td class="colorless-col">
    <svg onclick="redirectToProfilePage({{ $member->user_id }});" class="gear-icon-btn"
        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
        class="bi bi-eye-fill" viewBox="0 0 16 16">
        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
        <path
            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
    </svg>
<td>
<td class="coloured-cell px-3">
    <div onclick="deleteCaptain({{ $member->id }}, {{ $selectTeam->id }})"
        class="player-info cursor-pointer">
        @if ($captain && $member->id == $captain->team_member_id)
            <div class="player-image"> </div>
        @endif
        <span>{{ $member->user->name }}</span>
    </div>
</td>
<td class="flag-cell coloured-cell px-3">
    <span>{{ $member->user->email }}</span>
</td>
<td class="flag-cell coloured-cell px-3 fs-4">
    <span>{{ $member->user->participant->region_flag }} </span>

</td>
