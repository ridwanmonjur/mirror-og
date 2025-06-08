<div class="table-responsive mb-4 " v-cloak v-if="transactions && transactions[0]">
    <table class="transaction-history__table table ">
        <thead class="transaction-table__header">
            <tr>
                <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Date
                </th>
                <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">
                    Transaction</th>
                <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Type
                </th>

                <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">
                    Change</th>
                <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Total
                </th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="transaction in transactions" :key="transaction.id" class="transaction-row">
                <x-wallet.transaction-item :fullPage="true" />
            </tr>
        </tbody>
    </table>
</div>
