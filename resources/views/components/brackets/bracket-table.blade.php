@props(['bracket', 'isLeague'])
<div class="position-relative">
 <table class="tournament-bracket__table  mx-auto {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }}"
    style="width: 300px;"
 >
     <thead class="sr-only">
         <tr>
             <th>Team</th>
             <th>Score</th>
         </tr>
     </thead>
     <tbody class="tournament-bracket__content">
         <tr class="tournament-bracketTeam  px-2 py-3 bg-translucent  tournament-bracketTeam--winner ">
             <td class="tournament-bracket__pos ">
                 <abbr class="tournament-bracket__code me-2"
                     title="{{ $bracket['team1_position'] }}">{{ $bracket['team1_position'] }} 
                 </abbr>
                
                 @if ($bracket['team1_id'])
                    <img 
                        data-position="{{$bracket['team1_position']}}"
                        src="{{ asset('storage/'.$bracket['team1_teamBanner'])  }}" width="40" height= "40"
                        onerror="this.src='/assets/images/404.svg';" class="object-fit-cover mobile-img team border border-primary rounded-circle" alt="Team View">
                @else
                    <img  
                        data-position="{{$bracket['team1_position']}}"
                        width="40" height= "40"
                        src="{{asset('assets/images/404.svg')}}" 
                        class="object-fit-cover mobile-img border border-secondary team rounded-circle" alt="Team View"
                    >
                 @endif
                    <svg 
                        data-position="{{$bracket['team1_position']}}" 
                        data-position1="{{$bracket['team1_position']}}"
                        data-position2="{{$bracket['team2_position']}}"
                        onclick="currentMatchReportShow(event);" 
                        class="ms-2"
                        width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#878787 " stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    
             </td>
             <td class="tournament-bracket__score  ">
                 <span class="tournament-bracket__number dotted-score-box">0</span>
             </td>
         </tr>
         <tr class="tournament-bracketTeam  bg-translucent px-2 py-3 ">
          
             <td class="tournament-bracket__pos ">
                    <svg 
                        data-position="{{$bracket['team2_position']}}" 
                        data-position1="{{$bracket['team1_position']}}"
                        data-position2="{{$bracket['team2_position']}}"
                        onclick="currentMatchReportShow(event);" 
                        class="me-2"
                        width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 14C3 9.02944 7.02944 5 12 5C16.9706 5 21 9.02944 21 14M17 14C17 16.7614 14.7614 19 12 19C9.23858 19 7 16.7614 7 14C7 11.2386 9.23858 9 12 9C14.7614 9 17 11.2386 17 14Z" stroke="#878787 " stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                   
                
                 @if ($bracket['team2_id'])
                     <img
                         data-position="{{$bracket['team2_position']}}"
                         src="{{ asset('storage/'.$bracket['team2_teamBanner']) }}" width="40" height= "40"
                         onerror="this.src='/assets/images/404q.svg';" 
                         class="object-fit-cover mobile-img border border-primary team rounded-circle" alt="Team View">
                 @else
                    <img  
                        data-position="{{$bracket['team2_position']}}"
                        width="40" height= "40"
                        src="{{asset('assets/images/404.svg')}}" 
                        class="object-fit-cover mobile-img border border-secondary team rounded-circle" alt="Team View"
                    >
                @endif

                 <abbr class="tournament-bracket__code"
                     title="{{ $bracket['team2_position'] }}">{{ $bracket['team2_position'] }}</abbr>
               
             </td>
          
             <td class="tournament-bracket__score ">
                 <span class="tournament-bracket__number dotted-score-box">0</span>
                  
             </td>
            
         </tr>
         
     </tbody>
     <div @class(["d-block position-absolute top-0 left-0 py-2 px-3", "d-lg-none" => !$isLeague])>
    @if($bracket['user_level'] === $USER_ACCESS['IS_ORGANIZER'])
        <span class="h-100">
            <svg 
                data-team1_id="{{$bracket['team1_position']}}" data-team2_id="{{$bracket['team2_position']}}"
                onclick="currentMatchUpdateShow(event); " style="z-index: 999;"
                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-pencil-square  cursor-pointer ms-3" viewBox="0 0 16 16">
                <path
                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                <path fill-rule="evenodd"
                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
            </svg>
        </span>
    @endif
 </div>
 </table>
</div>

  
