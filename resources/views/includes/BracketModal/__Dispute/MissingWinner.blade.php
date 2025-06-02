<div>
    <template v-if="userLevelEnums['IS_ORGANIZER'] != report.userLevel">
        <div class="row">
            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                <div class="row justify-content-start bg-light border border-3 border rounded px-2 py-2">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-start my-3"> Resolution </h5>
                        <div class="text-end my-3">
                            <p class="my-0">Time left until auto-resolve:</p>
                            <small>0d 0h</small>
                        </div>
                    </div>
                    <div class="ps-5 ps-5 text-start">
                        <br><br>
                        <p class="text-center fw-lighter">
                            When the opponent team submits their counter-evidence, the organizer will
                            resolve the dispute.
                            <br>
                            If the opponent team does not respond by the allocated time, the dispute
                            will automatically resolve in the favor of the team that raised the dispute.
                        </p>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </template>
    <template v-if="userLevelEnums['IS_ORGANIZER'] == report.userLevel">
        <div class="row">
            <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                <div class="row justify-content-start bg-light border border-3 border rounded px-2 py-2">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-start my-3"> Resolution </h5>
                        <div class="text-end my-3">
                            <p class="my-0">Time left until auto-resolve:</p>
                            <small>0d 0h</small>
                        </div>
                    </div>
                    <div class="ps-5 ps-5 text-start">
                        <form method="POST" v-on:submit="resolveDisputeForm(event)">
                            <input type="hidden" name="action" value="resolve">
                            <input type="hidden" name="id" v-bind:value="dispute[reportUI.matchNumber]?.id">
                            <input type="hidden" name="match_number"
                                v-bind:value="dispute[reportUI.matchNumber]?.match_number">

                            <input type="hidden" name="resolution_winner" id="resolution_winner_input">
                            <input type="hidden" name="resolution_resolved_by" value="disputeLevelEnums['ORGANIZER']">
                            <p class="text-primary text-center"> The dispute will be resolved in favor of (Choose): </p>
                            <div class="d-flex justify-content-center flex-column mt-2">
                                <button type="button" v-on:click="decideResolution(event, 0)"
                                    v-bind:disabled="getDisabled()"
                                    class="selectedButton selectedDisputeResolveButton ps-0 btn mb-2 mt-2 rounded-pill mx-auto py-0 border border-dark text-start">
                                    <img v-bind:src="'/storage/' + report.teams[0]?.banner" alt="Team Banner"
                                        width="35" height="35"
                                        onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                                        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                                    <small class="ms-2 py-0" v-text="report.teams[0]?.name"></small>
                                </button>
                                <button type="button" v-on:click="decideResolution(event, 1)"
                                    v-bind:disabled="getDisabled()"
                                    class="selectedButton selectedDisputeResolveButton ps-0 btn  rounded-pill mx-auto py-0 mt-2 border border-dark text-start">
                                    <img v-bind:src="'/storage/' + report.teams[1]?.banner" alt="Team Banner"
                                        width="35" height="35"
                                        onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                                        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                                    <small class="ms-2 py-0" v-text="report.teams[1]?.name"></small>
                                </button>
                                <button type="submit"
                                    class="selectTeamSubmitButton btn border mx-auto border border-primary text-primary py-2 btn-sm rounded-pill mt-4 px-4">
                                    Submit </button>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
