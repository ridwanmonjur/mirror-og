<template v-if="report.teams[reportUI.otherTeamNumber]?.winners[reportUI.matchNumber] || report.teams[reportUI.teamNumber]?.winners[reportUI.matchNumber]">
    <small class="d-block">

        <template v-if="report.teams[0]?.winners[reportUI.matchNumber]">
            <small class="d-block">
                <small v-text="report.teams[0].name"> </small>
                declared
                <small class="text-primary" v-text="report.teams[report.teams[0].winners[reportUI.matchNumber]].name"> </small>
                to be the winner for Game <small v-text="reportUI.matchNumber+1"> </small>
            </small>
        </template>

        <template v-if="report.teams[1].winners[reportUI.matchNumber] ">
            <small class="d-block">
                <small v-text="report.teams[1]?.name"> </small>       
                declared
                <small  class="text-primary" v-text="report.teams[report.teams[1]?.winners[reportUI.matchNumber]]?.name"> </small>
                to be the winner for Game <small v-text="reportUI.matchNumber+1"> </small>
            </small>
        </template>

        <template v-if="report.organizerWinners[reportUI.matchNumber]">
            <div>
                <small>
                    Organizer has chosen
                    <small  class="text-primary" v-text="report.teams[report.organizerWinners[reportUI.matchNumber]].name"> </small>
                    to be the winner for Game <small v-text="reportUI.matchNumber+1"> </small>
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
                    <template v-if="report.organizerWinners && report.organizerWinners[reportUI.matchNumber]">
                        <div>
                            <small>
                                Winner declared by organizer.
                            </small>
                        </div>
                    </template>
                    <template v-if="report.randomWinners && report.randomWinners[reportUI.matchNumber]">
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
