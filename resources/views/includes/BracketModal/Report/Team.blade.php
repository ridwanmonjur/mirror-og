<div>
    @include('includes.BracketModal.Report.ExistingChoicesTeam')
    <template v-if="report.realWinners">
        <div>
            <template v-if="dispute?.resolution_winner">
                <div>
                    <template
                        v-if="dispute?.resolution_resolved_by == dispute?.dispute_teamNumber">
                        <div class="mt-2">
                            <p class="text-success mt-2">
                                <span v-text="report.teams[dispute?.dispute_teamNumber]?.name">
                                </span> has conceded the dispute. Winner is to be decided by the organizer.
                            </p>
                        </div>
                    </template>
                    <template
                        v-if="dispute?.resolution_resolved_by != dispute?.dispute_teamNumber">
                        <div class="mt-2">
                            <p class="text-success mt-2">
                                The dispute has been resolved in favor of
                                <span v-text="report.teams[dispute?.resolution_winner]?.name">
                                </span>
                            </p>
                        </div>
                    </template>

                    <div class="mt-2 mb-2">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-sm border rounded-pill text-primary border-primary" data-bs-toggle="modal"
                                data-bs-target="#disputeModal" data-bs-dismiss="modal"> Show dispute </button>
                        </div>
                    </div>
                    <template v-if="!report.realWinners">
                        @include('includes.BracketModal.Report.PendingWinners')
                    </template>
                </div>
            </template>
            @include('includes.BracketModal.Report.RealWinners')
        </div>
    </template>
    <template v-else>
        <div>
            <template v-if="report.disqualified">
                @include('includes.BracketModal.Report.Disqualified')
            </template>
            <template v-else>
                <div>
                    <template
                        v-if="report.teams[0].winners && 
                            report.teams[1].winners">
                        <div>
                            <template
                                v-if="report.teams[0].winners != report.teams[1].winners
                                    && !dispute    
                                ">
                                <div class="mt-3">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn border rounded-pill text-light bg-primary me-3"
                                            v-on:click="onChangeTeamToWin">
                                            Change Declaration
                                        </button>
                                        <button class="btn border rounded-pill text-light me-3 bg-red"
                                            data-bs-toggle="modal" data-bs-target="#disputeModal"
                                            data-bs-dismiss="modal">
                                            Dispute
                                        </button>
                                    </div>
                                    <p class="my-3">
                                        If no consensus is reached by dd/mm/yy, the organizer will decide the winner for
                                        Game 3.
                                    </p>
                                </div>
                            </template>
                            <template
                                v-if="dispute && !dispute?.resolution_winner">
                                <div>
                                    <p class="text-red mt-2">
                                        The results of this match are disputed.
                                    </p>
                                    <div class="mt-2 mb-2">
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-sm border rounded-pill text-primary border-primary"
                                                data-bs-toggle="modal" data-bs-target="#disputeModal"
                                                data-bs-dismiss="modal"> Show dispute </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-sm border rounded-pill text-light bg-primary"
                                                v-on:click="onChangeTeamToWin">
                                                Change Declaration
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <template
                        v-if="report.teams[reportUI.teamNumber]?.winners">
                        <div class="mt-2">
                            @include('includes.BracketModal.Report.PendingWinners')
                        </div>
                    </template>
                    <template 
                        v-else>
                        <div class="mt-2">
                            @include('includes.BracketModal.Report.PickWinners')
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </template>
     

</div>
