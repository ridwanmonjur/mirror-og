<div>
    @include('___CommonPartials.__BracketModal.__Report.ExistingChoices')
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        <div>
            <template
                x-if="report.teams[0].winners[reportUI.matchNumber] && 
                report.teams[1].winners[reportUI.matchNumber]"
            >
                <div>
                    <template
                        x-if="report.teams[0].winners[reportUI.matchNumber] !=
                        report.teams[1].winners[reportUI.matchNumber]">
                        <div class="mt-3">
                            <div class="d-flex justify-content-center">
                                <button class="btn border rounded-pill text-primary border-primary me-3"
                                    x-on:click="onChangeTeamToWin">
                                    Change Declaration
                                </button>
                                <button class="btn border rounded-pill text-light me-3 bg-red" 
                                data-bs-toggle="modal" data-bs-target="#disputeModal"
                                > 
                                Dispute 
                                </button>
                            </div>
                            
                            <p class="my-3">
                                If no consensus is reached by dd/mm/yy, the organizer will decide the winner for Game 3.
                            </p>
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
                                                <button
                                                    class="btn btn-sm border rounded-pill text-primary border-primary me-3"
                                                    x-on:click="onChangeTeamToWin"> Change Declaration </button>
                                        </div>
                                    </div>
                            </template>
                            <template x-if="dispute[reportUI.matchNumber]?.resolved">
                                <div>
                                    <p class="text-red mt-2">
                                        <i> The results of this match are disputed. </i>
                                    </p>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-center">
                                                <button
                                                    class="btn btn-sm border rounded-pill text-primary border-primary mx-auto"
                                                    x-on:click="onChangeTeamToWin"> Change Declaration </button>
                                        </div>
                                        <p class="text-success mt-2">
                                            The dispute has been resolved in favor of
                                            <span x-text="report.teams[dispute[reportUI.matchNumber]?.resolved].name">
                                            </span>
                                        </p>
                                    </div>
                            </template>
                            <template x-if="report.realWinners[reportUI.matchNumber]">
                                @include('___CommonPartials.__BracketModal.__Report.RealWinners')
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            <template
                x-if="!report.teams[reportUI.teamNumber].winners[reportUI.matchNumber]"
            >
                <div class="mt-2">
                    @include('___CommonPartials.__BracketModal.__Report.PickWinners')
                </div>
            </template>
            <template
                x-if="report.teams[reportUI.teamNumber].winners[reportUI.matchNumber]
                    && !report.teams[reportUI.otherTeamNumber].winners[reportUI.matchNumber] 
                "
            >
                <div class="mt-2">
                     @include('___CommonPartials.__BracketModal.__Report.PendingWinners')
                </div>
            </template>
        </div>
    </template>
    <template x-if="!dispute[reportUI.matchNumber] && report.realWinners[reportUI.matchNumber]">
        <div>
            @include('___CommonPartials.__BracketModal.__Report.RealWinners')
        </div>
    </template>
</div>
