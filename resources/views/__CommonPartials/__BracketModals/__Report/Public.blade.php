<div>
    <template v-if="!report.realWinners[reportUI.matchNumber]">
        @include('__CommonPartials.__BracketModals.__Report.PendingWinners')
    </template> 
    <template v-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('__CommonPartials.__BracketModals.__Report.RealWinners')
        </div>
    </template>
</div>
