<div class="mt-2">
    <div>
        <img v-bind:src="report.realWinners && report.teams[report.realWinners]?.banner ? 
            '/storage/' + report.teams[report.realWinners].banner : 
            '/assets/images/.png'
            " alt="Team Banner"
            width="50" height="50" onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="ms-0 border border-3 border-primary popover-content-img rounded-circle object-fit-cover">
    </div>
    <p class="mt-2 d-block">
        <span v-if="report.defaultWinners && report.defaultWinners"> (Default) </span>
        <span class="text-primary" v-text="report.teams[report.realWinners]?.name"> </span>
        wins this game
    </p>
</div>
