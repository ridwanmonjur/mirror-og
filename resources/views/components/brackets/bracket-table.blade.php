@props(['bracket', 'isFirst'])
 <table class="tournament-bracket__table  mx-auto {{ $bracket['team1_position'] }} {{ $bracket['team2_position'] }}"
    style="width: min(300px, 90vw);"
 >
     <thead class="sr-only">
         <tr>
             <th>Country</th>
             <th>Score</th>
         </tr>
     </thead>
     <tbody class="tournament-bracket__content">
         <tr class="tournament-bracket__team  px-2 py-3 bg-translucent  tournament-bracket__team--winner">
             <td class="tournament-bracket__pos ">
                 <abbr class="tournament-bracket__code me-2"
                     title="{{ $bracket['team1_position'] }}">{{ $bracket['team1_position'] }}
                 </abbr>
                 @if ($bracket['team1_id'])
                    <img src="{{ bladeImageNullq($bracket['team1_teamBanner']) }}" width="40" height= "40"
                         onerror="this.src='/assets/images/404q.png';" class="object-fit-cover team border border-primary rounded-circle" alt="Team View">
                @else
                    <small class="rounded-circle border filler border-secondary" ></small>
                 @endif
                 @if (!$isFirst)
                    <svg 
                        data-position="{{$bracket['team1_position']}}" 
                        onclick="reportModalShow(event);" 
                        class="mx-2"
                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                @endif
             </td>
             <td class="tournament-bracket__score ">
                 <span class="tournament-bracket__number">0</span>
             </td>
         </tr>
         <tr class="tournament-bracket__team  bg-translucent px-2 py-3 ">
             <td class="tournament-bracket__pos ">
                 <abbr class="tournament-bracket__code"
                     title="{{ $bracket['team2_position'] }}">{{ $bracket['team2_position'] }}</abbr>
                 @if ($bracket['team2_id'])
                     <img src="{{ bladeImageNullq($bracket['team2_teamBanner']) }}" width="40" height= "40"
                         onerror="this.src='/assets/images/404q.png';" 
                         class="object-fit-cover border border-primary team rounded-circle me-2" alt="Team View">
                 @else
                    <small class="rounded-circle filler border border-secondary " ></small>
                @endif
                @if (!$isFirst)
                    <svg 
                        data-position="{{$bracket['team2_position']}}" 
                        onclick="reportModalShow(event);" 
                        class="mx-2"
                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                @endif
             </td>
          
             <td class="tournament-bracket__score ">
                 <span class="tournament-bracket__number dotted-score-box">0</span>
                  @if($bracket['user_level'] === $USER_ACCESS['IS_ORGANIZER'])
                        <span >
                            <svg 
                                data-team1_id="{{$bracket['team1_position']}}" data-team2_id="{{$bracket['team2_position']}}"
                                onclick="updateModalShow(event); " style="z-index: 999;"
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-pencil-square  cursor-pointer ms-2" viewBox="0 0 16 16">
                                <path
                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                <path fill-rule="evenodd"
                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                            </svg>
                        </span>
                    @endif
             </td>
             
         </tr>
         
     </tbody>
 </table>
