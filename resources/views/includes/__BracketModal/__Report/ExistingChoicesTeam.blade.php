<template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] || report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
    <small class="d-block">
        <template v-if="report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
            <small class="d-block">
                You declared
                <small class="text-primary"
                    v-text="report.teams[report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]]?.name">
                </small>
                to be the winner for Game <small v-text="reportUI.matchNumber+1"> </small>
            </small>
        </template>

        <template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] ">
            <small class="d-block">
                Opponent Team declared
                <small class="text-primary"
                    v-text="report.teams[report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]]?.name">
                </small>
                to be the winner for Game <small v-text="reportUI.matchNumber+1"> </small>
            </small>
        </template>
        <template v-else>
            <small class="d-block">
                <template v-if="report.deadline.has_started && !report.deadline.has_ended">
                    <i class="d-block text-secondary">
                        Waiting for the other team to declare the winner for Game
                        <small v-text="reportUI.matchNumber+1"> </small>
                    </i>
                </template>
                <template v-if="report.deadline.has_ended">
                    <i class="d-block text-secondary">
                        Opponent didn't report any results.
                    </i>
                </template>
                <small class="d-block">
        </template>
        <template v-if="report.organizerWinners[reportUI.matchNumber]">
            <div>
                <small>
                    Organizer has chosen
                    <small class="text-primary"
                        v-text="report.teams[report.organizerWinners[reportUI.matchNumber]].name"> </small>
                    to be the winner
                </small>
            </div>
        </template>
    </small>
</template>
<template v-else>
    <small class="d-block">
        <template v-if="report.deadline.has_ended">
            <small class="d-block">
                <span>No winner declared by either team. 
                    <template v-if="report.organizerWinners[reportUI.matchNumber]">
                        <div>
                            <small>
                                Winner declared by organizer.
                            </small>
                        </div>
                    </template>
                    <template v-if="report.randomWinners[reportUI.matchNumber]">
                        <div>
                            <small>
                                Winner automatically chosen at random.
                            </small>
                        </div>
                    </template>
                </span>
            </small>
        </template>
    </small>
</template>
