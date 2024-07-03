@php 
    use Carbon\Carbon;
    $counTeamMembers = count($teamMembers);
@endphp
<br>

<div id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $counTeamMembers }} accepted member{{bladePluralPrefix($counTeamMembers)}} &nbsp;&nbsp;
        @if (isset($user) && $selectTeam->creator_id == $user->id)
            <button class="oceans-gaming-default-button oceans-gaming-default-button-link" 
                onclick="window.location.href='{{route('participant.member.manage', ['id'=> $selectTeam->id ])}}'">
                Manage Team
            </button>
        @endif            
    </p>
    @if($counTeamMembers > 0)
        <div class="tab-size d-flex justify-content-around flex-wrap tab-size mt-3 pt-3">
            
            <div class="mb-2">
                <span class="icon2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                        </polygon>
                    </svg>
                    <span> Filter </span>
                </span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <span class="icon2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                        <path d="M15 7h6v6" />
                    </svg>
                    <span>
                        Sort
                    </span>
                </span>
            </div>
            <div class="mb-2">
                <div class="search_box">
                    <i class="fa fa-search"></i>
                    <input style="width: min(90vw, 350px); font-size: 15px;" class="nav__input" type="text" placeholder="Search for player name">
                </div>
                <div>
                    @if (isset($user) && $user->id == $selectTeam->user_id)
                        <img src="/assets/images/add.png" height="40px" width="40px">
                    @endif
                </div>
            </div>
        </div>
        <table class="member-table">
            <tbody>
                @foreach ($teamMembers as $member)
                    <tr class="st px-3">
                        <td class="colorless-col">
                            <svg 
                                onclick="redirectToProfilePage({{$member->user_id}});"
                                class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path
                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>
                        </td>
                        <td class="coloured-cell px-3">
                            <div class="player-info">
                                @if ($captain && $member->id == $captain->team_member_id)
                                    <div class="player-image"> </div>
                                @endif
                                 <img 
                                    width="45" height="45" 
                                    src="{{ bladeImageNull($member->user->userBanner) }}"
                                    class="mx-2 random-color-circle object-fit-cover rounded-circle"
                                >
                                <span>{{ $member->user->name }}</span>
                            </div>
                        </td>
                        <td class="coloured-cell px-3">
                            <span>{{ $member->user->email }}</span>
                        </td>
                        <td class="coloured-cell px-3">
                            <span>{{ $member->status }} {{ is_null($member->updated_at) ? '' : Carbon::parse($member->updated_at)->diffForHumans() }} </span>
                        </td>
                        <td class="flag-cell coloured-cell px-3 fs-4">
                            <span>{{ $member->user->participant->region_flag }} </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>