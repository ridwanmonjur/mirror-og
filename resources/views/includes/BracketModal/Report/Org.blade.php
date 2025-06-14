<div>
    @include('includes.BracketModal.Report.ExistingChoices')
    <template v-if="report.disqualified">
        <div>
            @include('includes.BracketModal.Report.Disqualified')
        </div>
    </template>
    <template v-else>
        <div class="d-block">
            <template
                v-if="!report.realWinners[reportUI.matchNumber] && !report.disqualified && !dispute[reportUI.matchNumber]">
                <div>
                    <div>
                        <template
                            v-if="report.teams[0].winners[reportUI.matchNumber] &&
                        report.teams[1].winners[reportUI.matchNumber]">
                            <div>
                                <template
                                    v-if="(report.teams[0].winners[reportUI.matchNumber] !=
                                    report.teams[1].winners[reportUI.matchNumber])
                                    && !dispute[reportUI.matchNumber]?.resolution_winner
                                ">
                                    <div>
                                        <p class="text-red">
                                            <i> The results of this match are being disagreed. </i>
                                        </p>
                                    </div>
                                </template>

                            </div>
                        </template>
                    </div>
                    @include('includes.BracketModal.Report.PickWinners')
                </div>
            </template>
            <template v-if="dispute[reportUI.matchNumber]">
                <div>
                    <template v-if="dispute[reportUI.matchNumber]?.resolution_winner">
                        <div>


                            <p class="text-success mt-2">
                                The dispute has been resolved in favor of
                                <span class="text-primary"
                                    v-text="report.teams[dispute[reportUI.matchNumber]?.resolution_winner]?.name"> </span>
                            </p>
                            <div class="mt-2">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-sm border rounded-pill text-primary border-primary"
                                        data-bs-toggle="modal" data-bs-target="#disputeModal" data-bs-dismiss="modal"
                                        onclick="document.getElementById('reportModal')?.click();"> Show dispute </button>
                                </div>
                            </div>
                            <template v-if="report.realWinners[reportUI.matchNumber]">
                                <div>
                                    @include('includes.BracketModal.Report.RealWinners')
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-if="!dispute[reportUI.matchNumber]?.resolution_winner">
                        <div>
                            <p class="text-red mt-2">
                                The results of this match are disputed.
                            </p>
                            <div class="mt-2 mb-3">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-sm border rounded-pill text-primary border-primary"
                                        data-bs-toggle="modal" data-bs-target="#disputeModal" data-bs-dismiss="modal"> Show
                                        dispute </button>
                                </div>
                            </div>
                            @include('includes.BracketModal.Report.PendingWinners')
                        </div>
                    </template>
                </div>
            </template>
            <template v-if="!dispute[reportUI.matchNumber] && report.realWinners[reportUI.matchNumber]">
                <div>
                    @include('includes.BracketModal.Report.RealWinners')
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-sm border rounded-pill text-primary border-primary "
                            v-on:click="onChangeTeamToWin"> Change Declaration </button>
                        <button class="btn btn-sm border border-danger rounded-pill text-danger ms-2 "
                            v-on:click="onRemoveTeamToWin"> Remove </button>
                    </div>
                </div>
            </template>
        </div>
    </template>
</div>
