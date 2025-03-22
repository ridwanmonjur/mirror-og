<div>
    <template v-if="!report.realWinners[reportUI.matchNumber]">
        @include('includes.__BracketModal.__Report.PendingWinners')
    </template> 
    <template v-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('includes.__BracketModal.__Report.RealWinners')
        </div>
    </template>
</div>
