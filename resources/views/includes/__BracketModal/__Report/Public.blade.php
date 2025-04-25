<div>
    <template v-if="report.realWinners[reportUI.matchNumber]">
        @include('includes.__BracketModal.__Report.RealWinners')
    </template>
    <template v-else>
        <div>
            <template v-if="report.disqualified">
                @include('includes.__BracketModal.__Report.Disqualified')
            </template>
            <template v-else>
                @include('includes.__BracketModal.__Report.PendingWinners')
            </template>
        </div>
    </template>
</div>
