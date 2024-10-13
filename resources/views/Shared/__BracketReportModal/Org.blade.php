<div>
    @include('Shared.__BracketReportModal.ExistingChoices')
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        <div>
            <div>
                <template x-if="report.teams[0].winners[reportUI.matchNumber] &&
                    report.teams[1].winners[reportUI.matchNumber]"
                >
                    <div>
                        <template x-if="(report.teams[0].winners[reportUI.matchNumber] !=
                                report.teams[1].winners[reportUI.matchNumber])
                                && !dispute[reportUI.matchNumber]?.resolved
                            "
                        >
                            <div>
                                <p class="text-red">
                                    <i> The results of this match are being disagreed. </i>
                                </p>
                            </div>
                        </template>
                        
                    </div>   
                </template>
            </div>
            @include('Shared.__BracketReportModal.PickWinners')         
        </div>
    </template>
    <template x-if="dispute[reportUI.matchNumber]">
        <div>
            <template x-if="dispute[reportUI.matchNumber]?.resolved">
                <div>
                    <p class="text-red mt-2">
                        <i> The results of this match are disputed. </i>
                    </p>
                    <div class="mt-2">
                        <div class="d-flex justify-content-center"><div class="d-flex justify-content-center">
                            <button class="btn btn-sm border rounded-pill text-primary border-primary me-3" x-on:click="onChangeTeamToWin"> Change Declaration </button>
                        </div>
                    </div>
                    <template x-if="report.realWinners[reportUI.matchNumber]">
                        <div>
                            @include('Shared.__BracketReportModal.RealWinners')
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="!dispute[reportUI.matchNumber]?.resolved">
                <div>
                    <p class="text-red mt-2">
                        <i> The results of this match are disputed. </i>
                    </p>
                    <div class="mt-2">
                        <div class="d-flex justify-content-center"><div class="d-flex justify-content-center">
                            <button class="btn btn-sm border rounded-pill text-primary border-primary me-3" x-on:click="onChangeTeamToWin"> Change Declaration </button>
                        </div>
                    </div>
                    <p class="text-success mt-2">
                        The dispute has been resolved in favor of 
                        <span x-text="report.teams[dispute[reportUI.matchNumber]?.resolved].name"> </span>
                    </p>
                </div>
                @include('Shared.__BracketReportModal.RealWinners')
            </template>
        </div>
    </template>
    <template x-if="!dispute[reportUI.matchNumber] && report.realWinners[reportUI.matchNumber]">
        <div>
            @include('Shared.__BracketReportModal.RealWinners')
            <div class="d-flex justify-content-center">
                <button class="btn btn-sm border rounded-pill text-primary border-primary me-3" x-on:click="onChangeTeamToWin"> Change Declaration </button>
            </div>
        </div>
    </template>
</div>
