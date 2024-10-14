@props(['bracket'])
 <table class="tournament-bracket__table mx-auto">
     <thead class="sr-only">
         <tr>
             <th>Country</th>
             <th>Score</th>
         </tr>
     </thead>
     <tbody class="tournament-bracket__content">
         <tr class="tournament-bracket__team tournament-bracket__team--winner">
             <td class="tournament-bracket__country {{ $bracket['team2_position'] }}">
                 <abbr class="tournament-bracket__code me-2"
                     title="{{ $bracket['team1_position'] }}">{{ $bracket['team1_position'] }}
                 </abbr>
                 @if ($bracket['team1_id'])
                     <img src="/storage/{{ $bracket['team1_teamBanner'] }}" width="40" height= "40"
                         onerror="this.src='/assets/images/404.png';" class="object-fit-cover border border-primary rounded-circle" alt="Team View">
                 @endif
             </td>
             <td class="tournament-bracket__score mb-2">
                 <span class="tournament-bracket__number">0</span>
             </td>
         </tr>
         <tr class="tournament-bracket__team">
             <td class="tournament-bracket__country  {{ $bracket['team2_position'] }}">
                 <abbr class="tournament-bracket__code"
                     title="{{ $bracket['team2_position'] }}">{{ $bracket['team2_position'] }}</abbr>
                 @if ($bracket['team2_id'])
                     <img src="/storage/{{ $bracket['team2_teamBanner'] }}" width="40" height= "40"
                         onerror="this.src='/assets/images/404.png';" class="object-fit-cover border border-primary rounded-circle me-2" alt="Team View">
                 @endif

             </td>
             <td class="tournament-bracket__score mb-2">
                 <span class="tournament-bracket__number">0</span>
             </td>
         </tr>
     </tbody>
 </table>
