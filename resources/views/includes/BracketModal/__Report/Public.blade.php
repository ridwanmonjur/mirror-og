<div>
    <template v-if="report">
        <div class="my-0 py-0">
            <template v-if="report.realWinners[reportUI.matchNumber]">
                @include('includes.BracketModal.__Report.RealWinners')
            </template>
            <template v-else>
                <div class="my-0 py-0">
                    <template v-if="report.disqualified">
                        @include('includes.BracketModal.__Report.Disqualified')
                    </template> 
                    <template v-else> 
                        @include('includes.BracketModal.__Report.PendingWinners')
                    </template>
                </div>
            </template>
        </div>
    </template>
    <template v-else>
        <div class="my-0 py-0">
            @include('includes.BracketModal.__Report.PendingWinners')
        </div>
    </template>
</div>
