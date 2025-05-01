<div class="my-0 py-0">
<template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] || report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
    <span class="d-block fst-italic fs-7">

        <template v-if="report.teams[0]?.winners[reportUI.matchNumber]">
            <span class="d-block ">
                <span v-text="report.teams[0].name"> </span>
                declared
                <span class="text-primary" v-text="report.teams[report.teams[0].winners[reportUI.matchNumber]]?.name"> </span>
                to be the winner for Game <span v-text="reportUI.matchNumber+1"> </span>
            </span>
        </template>

        <template v-if="report.teams[1].winners[reportUI.matchNumber] ">
            <span class="d-block fst-italic fs-7">
                <span v-text="report.teams[1]?.name"> </span>       
                declared
                <span  class="text-primary" v-text="report.teams[report.teams[1]?.winners[reportUI.matchNumber]]?.name"> </span>
                to be the winner for Game <span v-text="reportUI.matchNumber+1"> </span>
            </span>
        </template>

        <template v-if="report.organizerWinners[reportUI.matchNumber]">
            <div class="fst-italic fs-7">
                <span>
                    Organizer has chosen
                    <span  class="text-primary" v-text="report.teams[report.organizerWinners[reportUI.matchNumber]]?.name"> </span>
                    to be the winner for Game <span v-text="reportUI.matchNumber+1"> </span>
                </span>
            </div>
        </template>
    </span>
</template>
<template v-else>
    <span class="d-block fst-italic fs-7">
        <template v-if="report.deadline.has_ended">
            <span class="d-inline-block">
                <span>No winner declared by either team. 
                    <template v-if="report.organizerWinners && report.organizerWinners[reportUI.matchNumber]">
                        <span>
                            <span>
                                Winner declared by organizer.
                            </span>
                        </span>
                    </template>
                    <template v-if="report.randomWinners && report.randomWinners[reportUI.matchNumber]">
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
