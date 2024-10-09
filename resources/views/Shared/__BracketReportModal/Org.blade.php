<div>
    <template x-if="report.realWinners[reportUI.matchNumber-1]">
        <div>
            <span x-text="report.teams[1].name"> </span>
            has chosen
            <span x-text="report.teams[winners[reportUI.matchNumber-1]].name"> </span>
            to be the winner
        </div>
    </template>

    <template x-if="!report.teams[1].winners[reportUI.matchNumber-1]">
        <div>
            <template x-if="report.teams[0].winners[reportUI.matchNumber-1]">
                <div>
                    You have chosen
                    <span x-text="report.teams[winners[reportUI.matchNumber-1]].name"> </span>
                    to be the winner
                </div>
            </template>

            <template x-if="!report.teams[0].winners[reportUI.matchNumber-1]">
                <div>
                    <button class="ps-0 btn mb-2 d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
                        <img :src="'/storage/' + report.teams[0].banner" alt="Team Banner" width="35" height="35"
                            onerror="this.src='{{ asset('assets/images/404.png') }}';"
                            class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                        <small class="ms-2 py-0" x-text="report.teams[0]?.name"></small>
                    </button>
                    <button class="ps-0 btn d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
                        <img :src="'/storage/' + report.teams[1].banner" alt="Team Banner" width="35"
                            height="35" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                            class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
                        <small class="ms-2 py-0" x-text="report.teams[1]?.name"></small>
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>
