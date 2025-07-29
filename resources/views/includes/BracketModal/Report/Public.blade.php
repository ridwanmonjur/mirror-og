<div>
    <template v-if="report">
        <div class="my-0 py-0">
            <template v-if="report.realWinners">
                @include('includes.BracketModal.Report.RealWinners')
            </template>
            <template v-else>
                <div class="my-0 py-0">
                    <template v-if="report.disqualified">
                        @include('includes.BracketModal.Report.Disqualified')
                    </template> 
                    <template v-else> 
                        @include('includes.BracketModal.Report.PendingWinners')
                    </template>
                </div>
            </template>
        </div>
    </template>
    <template v-else>
        <div class="my-0 py-0">
            @include('includes.BracketModal.Report.PendingWinners')
        </div>
    </template>
</div>
