<div>
    @include('___CommonPartials.__BracketModal.__Report.ExistingChoices')
    <template x-if="!report.realWinners[reportUI.matchNumber] && !dispute[reportUI.matchNumber]">
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
            @include('___CommonPartials.__BracketModal.__Report.PickWinners')         
        </div>
    </template>
    <template x-if="dispute[reportUI.matchNumber]">
        <div>
            <template x-if="dispute[reportUI.matchNumber]?.resolved">
                <div>
                    <p class="text-red mt-2">
                        The results of this match are disputed.
                    </p>
                    <div class="mt-2">
                       <div class="d-flex justify-content-center">
                            <button class="btn btn-sm border rounded-pill text-primary border-primary"
                                data-bs-toggle="modal" data-bs-target="#disputeModal"
                                onclick="document.getElementById('reportModal')?.click();"
                            > Show dispute </button>
                        </div>
                    </div>
                     <p class="text-success mt-2">
                        The dispute has been resolved in favor of 
                        <span x-text="report.teams[dispute[reportUI.matchNumber]?.resolved].name"> </span>
                    </p>
                    <template x-if="report.realWinners[reportUI.matchNumber]">
                        <div>
                            @include('___CommonPartials.__BracketModal.__Report.RealWinners')
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="!dispute[reportUI.matchNumber]?.resolved">
                <div>
                    <p class="text-red mt-2">
                        The results of this match are disputed. 
                    </p>
                    <div class="mt-2 mb-3">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-sm border rounded-pill text-primary border-primary"
                                data-bs-toggle="modal" data-bs-target="#disputeModal"
                            > Show dispute </button>
                        </div>
                    </div>
                    @include('___CommonPartials.__BracketModal.__Report.PendingWinners')
                </div>
            </template>
        </div>
    </template>
    <template x-if="!dispute[reportUI.matchNumber] && report.realWinners[reportUI.matchNumber]">
        <div>
            @include('___CommonPartials.__BracketModal.__Report.RealWinners')
            <div class="d-flex justify-content-center">
                <button class="btn btn-sm border rounded-pill text-primary border-primary me-3" x-on:click="onChangeTeamToWin"> Change Declaration </button>
            </div>
        </div>
    </template>
</div>
