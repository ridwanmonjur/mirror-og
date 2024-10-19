<div>
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        @include('___CommonPartials.__BracketModal.__Report.RealWinners')
    </template> 
    <template x-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('___CommonPartials.__BracketModal.__Report.RealWinners')
        </div>
    </template>
</div>
