<div>
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        @include('Shared.__BracketReportModal.RealWinners')
    </template> 
    <template x-if="report.realWinners[reportUI.matchNumber]">
        <div>
            @include('Shared.__BracketReportModal.RealWinners')
        </div>
    </template>
</div>
