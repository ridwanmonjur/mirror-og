<div class="my-0 py-0">
<template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] || report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
    <span class="d-block fst-italic fs-7">
        <template v-if="report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
            <span class="d-block">
                You declared
                <span class="text-primary"
                    v-text="report.teams[report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]]?.name">
                </span>
                to be the winner for Game <span v-text="reportUI.matchNumber+1"> </span>
            </span>
        </template>

        <template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] ">
            <span class="d-block">
                Opponent Team declared
                <span class="text-primary"
                    v-text="report.teams[report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]]?.name">
                </span>
                to be the winner for Game <span v-text="reportUI.matchNumber+1"> </span>
            </span>
        </template>
        <template v-else>
            <span class="d-block">
                <template v-if="report.deadline.has_started && !report.deadline.has_ended">
                    <i class="d-block text-secondary">
                        Waiting for the other team to declare the winner for Game
                        <span v-text="reportUI.matchNumber+1"> </span>
                    </i>
                </template>
                <template v-if="report.deadline.has_ended">
                    <i class="d-block text-secondary">
                        Opponent didn't report any results.
                    </i>
                </template>
                <span class="d-block">
        </template>
        <template v-if="report.organizerWinners[reportUI.matchNumber]">
            <div>
                <span>
                    Organizer has chosen
                    <span class="text-primary"
                        v-text="report.teams[report.organizerWinners[reportUI.matchNumber]].name"> </span>
                    to be the winner
                </span>
            </div>
        </template>
    </span>
</template>
<template v-else>
    <span class="d-block fst-italic fs-7">
        <template v-if="report.deadline.has_ended">
            <span class="d-block">
                <span>No winner declared by either team. 
                    <template v-if="report.organizerWinners[reportUI.matchNumber]">
                        <span>
                            <span>
                                Winner declared by organizer.
                            </span>
                        </span>
                    </template>
                    <template v-if="report.randomWinners[reportUI.matchNumber]">
                        <span>
                            <span>
                                Winner automatically chosen at random.
                            </span>
                        </span>
                    </template>
                </span>
            </span>
        </template>
    </span>
</template>
</div>