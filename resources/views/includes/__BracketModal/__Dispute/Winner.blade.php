<div class="my-0 py-0" >
    <template v-if="dispute[reportUI.matchNumber] && dispute[reportUI.matchNumber].dispute_teamNumber">
        <div class="row">
            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                <div class="row justify-content-start bg-light border border-3 border rounded px-2 py-2">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-start my-3"> Resolution </h5>
                    </div>
                    <div class="ps-5 ps-5 text-start">
                        <div class="mt-2">
                            <div>
                                <img v-bind:src="'/storage/' + report.teams[report.realWinners[reportUI.matchNumber]]?.banner"
                                    alt="Team Banner" width="60" height="60"
                                    onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                                    class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                            </div>
                            <div class="mt-2 mb-2 d-block">
                                <p>
                                    <span v-text="report.teams[report.realWinners[reportUI.matchNumber]]?.name"> </span>
                                    has been resolved as the winner.
                                </p>
                                <template
                                    v-if="dispute[reportUI.matchNumber] && dispute[reportUI.matchNumber]?.resolution_resolved_by">
                                    <div class="my-0 py-0">
                                    <template
                                        v-if="
                                        dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['DISPUTEE']
                                        && dispute[reportUI.matchNumber]?.dispute_teamNumber
                                        && report.teams[dispute[reportUI.matchNumber].dispute_teamNumber]?.name
                                    ">
                                        <div class="mt-2">
                                            <p class="text-success mt-2">
                                                <span
                                                    v-text="report.teams[dispute[reportUI.matchNumber]?.dispute_teamNumber]?.name">
                                                </span> has conceded the dispute. Winner is to be decided by the
                                                organizer.
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['RESPONDER']">
                                        <div class="mt-2">
                                            <p class="text-success mt-2">
                                                The responder has conceded the dispute. The disputee is declared as the
                                                winner.
                                            </p>
                                        </div>
                                    </template>
                                    <template
                                        v-else-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['ORGANIZER']">
                                        <div class="mt-2">
                                            <p class="text-success mt-2">
                                                Winner has been decided by the organizer.
                                            </p>
                                        </div>
                                    </template>
                                    <template v-else-if="dispute[reportUI.matchNumber]?.resolution_resolved_by == disputeLevelEnums['TIME'] ">
                                        <div class="mt-2">
                                            <p class="text-success mt-2">
                                                Winner has been decided by timeout.
                                            </p>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="mt-2">
                                            <p class="text-success mt-2">
                                                Winner has been decided randomly.
                                            </p>
                                        </div>
                                    </template>
                                    </div>
                                </template>

                                <template
                                    v-if="userLevelEnums['IS_ORGANIZER'] == report.userLevel && dispute[reportUI.matchNumber]">
                                    <div class="d-inline">
                                        <form method="POST" class="d-inline" v-on:submit="resolveDisputeForm(event)">
                                            <input type="hidden" name="action" value="resolve">
                                            <input type="hidden" name="id"
                                                v-bind:value="dispute[reportUI.matchNumber].id">
                                            <input type="hidden" name="match_number"
                                                v-bind:value="dispute[reportUI.matchNumber].match_number">
                                            <input type="hidden" name="already_winner"
                                                v-bind:value="dispute[reportUI.matchNumber].resolution_winner">
                                            <input type="hidden" name="resolution_resolved_by"
                                                v-bind:value="disputeLevelEnums['ORGANIZER']">
                                            <button type="submit"
                                                class="btn py-0 d-inline rounded-pill bg-red text-light">
                                                Change Declaration
                                            </button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
