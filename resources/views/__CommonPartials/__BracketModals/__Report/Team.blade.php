<div>

    @include('__CommonPartials.__BracketModals.__Report.ExistingChoices')
    <template x-if="!report.realWinners[reportUI.matchNumber]">
        <div>
            <template
                x-if="report.teams[0].winners[reportUI.matchNumber] && 
                report.teams[1].winners[reportUI.matchNumber]"
            >
                <div>
                    <template
                        x-if="report.teams[0].winners[reportUI.matchNumber] != report.teams[1].winners[reportUI.matchNumber]
                        && !dispute[reportUI.matchNumber]    
                    ">
                        <div class="mt-3">
                            <div class="d-flex justify-content-center">
                                <button class="btn border rounded-pill text-light bg-primary me-3"
                                    x-on:click="onChangeTeamToWin">
                                    Change Declaration
                                </button>
                                <button class="btn border rounded-pill text-light me-3 bg-red" 
                                    data-bs-toggle="modal" data-bs-target="#disputeModal" data-bs-dismiss="modal"
                                > 
                                Dispute 
                                </button>
                            </div>
                            <p class="my-3">
                                If no consensus is reached by dd/mm/yy, the organizer will decide the winner for Game 3.
                            </p>
                        </div>
                    </template>
                    <template x-if="dispute[reportUI.matchNumber] && !dispute[reportUI.matchNumber]?.resolution_winner">
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
                            <div class="mt-2">
                                <div class="d-flex justify-content-center">
                                    <button
                                        class="btn btn-sm border rounded-pill text-light bg-primary"
                                        x-on:click="onChangeTeamToWin"
                                    > 
                                    Change Declaration 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <template
                x-if="!report.teams[reportUI.teamNumber].winners[reportUI.matchNumber]"
            >
                <div class="mt-2">
                    @include('__CommonPartials.__BracketModals.__Report.PickWinners')
                </div>
            </template>
            <template
                x-if="report.teams[reportUI.teamNumber].winners[reportUI.matchNumber] &&
                    !report.teams[reportUI.otherTeamNumber].winners[reportUI.matchNumber] 
                "
            >
                <div class="mt-2">
                     @include('__CommonPartials.__BracketModals.__Report.PendingWinners')
                </div>
            </template>
        </div>
    </template>
     <template x-if="dispute[reportUI.matchNumber]?.resolution_winner">
        <div>
            <template x-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == dispute[reportUI.matchNumber]?.dispute_teamNumber">
                <div class="mt-2">
                <p class="text-success mt-2">
                    <span x-text="report.teams[dispute[reportUI.matchNumber]?.dispute_teamNumber].name">
                    </span> has conceded the dispute. Winner is to be decided by the organizer.
                </p>
            </div>
            </template>
            <template x-if="dispute[reportUI.matchNumber]?.resolution_resolved_by != dispute[reportUI.matchNumber]?.dispute_teamNumber">
                <div class="mt-2">
                    <p class="text-success mt-2">
                        The dispute has been resolved in favor of
                        <span x-text="report.teams[dispute[reportUI.matchNumber]?.resolution_winner].name">
                        </span>
                    </p>
                </div>
            </template>
            
                <div class="mt-2 mb-3">
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm border rounded-pill text-primary border-primary"
                        data-bs-toggle="modal" data-bs-target="#disputeModal"
                    > Show dispute </button>
                </div>
            </div>
            <template x-if="!report.realWinners[reportUI.matchNumber]">
                @include('__CommonPartials.__BracketModals.__Report.PendingWinners')
            </template>
    </template>
    <template x-if="report.realWinners[reportUI.matchNumber]">
        <div>
            
            @include('__CommonPartials.__BracketModals.__Report.RealWinners')
        </div>
    </template>
</div>
