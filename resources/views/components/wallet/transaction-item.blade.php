<!-- Block: transaction-row -->
<!-- Element: transaction-row__cell with Modifier: --date -->
<td class="transaction-row__cell transaction-row__cell--date">
    <span v-text="transaction.formatted_date"></span><br>
    <span v-text="transaction.formatted_time"></span>
</td>
<!-- Element: transaction-row__cell with Modifier: --transaction -->
<td class="transaction-row__cell transaction-row__cell--transaction">
    <!-- Block: transaction-details -->
    <div class="transaction-details__title" v-text="transaction.name"></div>
    <p v-if="transaction.summary" class="transaction-details__subtitle" v-text="transaction.summary"></p>
    <!-- Prize badge if transaction has special type -->
    <span v-if="transaction.type === 'Prize Winnings'" class="prize-badge prize-badge--first-place">Prize
        Winner</span>
</td>
<!-- Element: transaction-row__cell with Modifier: --type -->
<td class="transaction-row__cell transaction-row__cell--type">@{{ transaction.type }}</td>
<!-- Element: transaction-row__cell with Modifier: --total -->
<td class="transaction-row__cell transaction-row__cell--total">@{{ transaction.formatted_amount }}</td>
