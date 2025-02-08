<div>
    <template v-if="!report.realWinners[reportUI.matchNumber]">
        @include('Shared.__BracketModalPartials.__Report.PendingWinners')
    </template> 
    <template v-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('Shared.__BracketModalPartials.__Report.RealWinners')
        </div>
    </template>
</div>
