
 <template v-if="report.teams[0].winners[reportUI.matchNumber]">
    <small class="d-block">
        <small v-text="report.userLevel === userLevelEnums['IS_TEAM1'] ? 
            'You have'
            : report.teams[0].name + ' has '  
        "> </small>
        chosen
        <small v-text="report.teams[report.teams[0].winners[reportUI.matchNumber]].name"> </small>
        to be the winner
    </small>
</template>

<template v-if="report.teams[1].winners[reportUI.matchNumber] ">
    <small class="d-block">
        <small v-text="report.userLevel === userLevelEnums['IS_TEAM2'] ? 
            'You have'
            : report.teams[1].name + ' has '  
        "> </small>      
        chosen
        <small v-text="report.teams[report.teams[1].winners[reportUI.matchNumber]].name"> </small>
        to be the winner
    </small>
</template>

<template v-if="report.organizerWinners[reportUI.matchNumber]">
    <div>
        <small>
            <small v-text="report.userLevel === userLevelEnums['IS_ORGANIZER'] ? 
            'You have '
            : 'Organizer has '  
            "> </small>   
            chosen
            <small v-text="report.teams[report.organizerWinners[reportUI.matchNumber]].name"> </small>
            to be the winner
        </small>
    </div>
</template>