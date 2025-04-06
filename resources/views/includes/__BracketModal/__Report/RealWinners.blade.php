<div class="mt-2">
    <div>
        <img v-bind:src="report.teams[report.realWinners[reportUI.matchNumber]]?.banner ? 
            '/storage/' + report.teams[report.realWinners[reportUI.matchNumber]].banner : 
            '/assets/images/.png'
            " alt="Team Banner"
            width="50" height="50" onerror="this.src='{{ asset('assets/images/404q.png') }}';"
            class="ms-0 border border-3 border-primary popover-content-img rounded-circle object-fit-cover">
    </div>
    <p class="mt-2 d-block">
        <span v-text="report.teams[report.realWinners[reportUI.matchNumber]]?.name"> </span>
        wins this game
    </p>
</div>
