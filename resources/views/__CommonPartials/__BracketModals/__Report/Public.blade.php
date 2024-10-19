<div>
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        @include('__CommonPartials.__BracketModals.__Report.RealWinners')
    </template> 
    <template x-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('__CommonPartials.__BracketModals.__Report.RealWinners')
        </div>
    </template>
</div>
