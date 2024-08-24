<li class="tournament-bracket__joined-item  tournament-bracket__item  code{{ $team1Code }}code">
<div class="tournament-bracket__joined-match tournament-bracket__match  code{{ $team1Code }}code " tabindex="0">
    <table class="tournament-bracket__table mx-auto">
       
        <thead class="sr-only">
            <tr>
                <th>Country</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody class="tournament-bracket__content">
            <tr class="tournament-bracket__team tournament-bracket__team--winner">
                <td class="tournament-bracket__country">
                    <abbr class="tournament-bracket__code" title="{{ $team1 }}">{{ $team1Code }}</abbr>
                    <span class="tournament-bracket__flag flag-icon flag-icon-{{ strtolower($team1Code) }}"
                        aria-label="Flag"></span>
                </td>
                <td class="tournament-bracket__score">
                    <span class="tournament-bracket__number">{{ $team1Score }}</span>
                </td>
            </tr>
            <tr class="tournament-bracket__team">
                <td class="tournament-bracket__country">
                    <abbr class="tournament-bracket__code" title="{{ $team2 }}">{{ $team2Code }}</abbr>
                    <span class="tournament-bracket__flag flag-icon flag-icon-{{ strtolower($team2Code) }}"
                        aria-label="Flag"></span>
                </td>
                <td class="tournament-bracket__score">
                    <span class="tournament-bracket__number">{{ $team2Score }}</span>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="text-center mx-auto tournament-bracket__display">
        <div class="d-flex justify-content-between align-content-center tournament-bracket__team--winner"> 
            <span class="py-1 me-4"> {{ $team1Code }} </span>
            <span class="tournament-bracket__number">{{ $team1Score }}</span>
        </div>
        <div class="d-flex justify-content-between align-content-center">
            <span class="py-1 me-4"> {{ $team2Code }} </span>
            <span class="tournament-bracket__number">{{ $team2Score }}</span>
        </div>
    </div>
</div>
</li>