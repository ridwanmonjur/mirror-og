@php 
    $counTeamMembers = count($teamMembers);
@endphp
<br>

<div id="CurrentMembers">
    <p class="text-center mx-auto mt-2">Team {{ $selectTeam->teamName }} has
        {{ $counTeamMembers }} accepted members &nbsp;&nbsp;
        @if ($selectTeam->creator_id == $user->id)
            <button class="oceans-gaming-default-button oceans-gaming-default-button-link" 
                onclick="window.location.href='{{route('participant.member.manage', ['id'=> $selectTeam->id ])}}'">
                Manage Team
            </button>
        @endif            
    </p>
    @if($counTeamMembers > 0)
        <div class="cont mt-3 pt-3">
            <div class="leftC">
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
            <div class="rightC">
                <div class="search_box">
                    <i class="fa fa-search"></i>
                    <input style="font-size: 14.5px;" class="nav__input" type="text" placeholder="Search for player name">
                </div>
                <div>
                    @if ($user->id == $selectTeam->user_id)
                        <img src="/assets/images/add.png" height="40px" width="40px">
                    @endif
                </div>
            </div>
        </div>
        <table class="member-table">
            <tbody>
                @foreach ($teamMembers as $member)
                    <tr class="st px-3">
                        <td class="coloured-cell px-3">
                            <div class="player-info">
                                <div class="player-image"> </div>
                                <span>{{ $member->user->name }}</span>
                            </div>
                        </td>
                        <td class="flag-cell coloured-cell px-3">
                            <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                alt="User's flag">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>