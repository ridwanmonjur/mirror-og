<div>
    <!-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama -->
</div><div class="tournament-bracket__match" tabindex="0">
    <table class="tournament-bracket__table">
        <caption class="tournament-bracket__caption">
            <time datetime="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</time>
        </caption>
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
</div>
