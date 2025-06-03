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
<td class="transaction-row__cell transaction-row__cell--type " v-text="transaction.type"></td>
@if ($fullPage)
    <td class="transaction-row__cell transaction-row__cell--change text-start" 
        :class="{ 
            'text-success': transaction.changeAmount > 0, 
            'text-danger': transaction.changeAmount < 0 
        }">
        <span v-text="'RM '"></span>
        <span v-if="transaction.changeAmount > 0">+</span>
        <span v-text="transaction.changeAmount"></span>
    </td>

    <!-- Total column (running balance) -->
    <td class="transaction-row__cell transaction-row__cell--type text-start" 
        v-text="'RM ' + transaction.runningBalance"></td>

    </td>
@else
    <td class="transaction-row__cell transaction-row__cell--total " v-text="'RM ' + transaction.amount"></td>

@endif
