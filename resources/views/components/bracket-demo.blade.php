<li class="tournament-bracket__item code{{ $team1Code }}code">
><div class="tournament-bracket__match  code{{ $team1Code }}code " tabindex="0">
    <table class="tournament-bracket__table">
        <thead class="sr-only">
            <tr>
                <th>Country</th>
            </tr>
        </thead>
        <tbody class="tournament-bracket__content">
            <tr class="tournament-bracket__team tournament-bracket__team--winner">
                <td class="tournament-bracket__country">
                    <abbr class="tournament-bracket__code" title="{{ $team1 }}">{{ $team1Code }}</abbr>
                    <span class="tournament-bracket__flag flag-icon flag-icon-{{ strtolower($team1Code) }}"
                        aria-label="Flag"></span>
                </td>
            </tr>
            <tr class="tournament-bracket__team">
                <td class="tournament-bracket__country">
                    <abbr class="tournament-bracket__code" title="{{ $team2 }}">{{ $team2Code }}</abbr>
                    <span class="tournament-bracket__flag flag-icon flag-icon-{{ strtolower($team2Code) }}"
                        aria-label="Flag"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</li>