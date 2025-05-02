<div class="my-0 py-0">
    <button v-on:click="selectTeamToWin(event, 0)" v-bind:disabled="getDisabled()"
        class="selectedButton ps-0 btn mb-2 mt-2 d-block rounded-pill w-75 text-truncate mx-auto py-0 border border-dark text-start">
        <img v-bind:src=" report.teams[0]?.banner ? '/storage/' + report.teams[0].banner : '/assets/images/.png' " alt="Team Banner" width="35" height="35"
            onerror="this.src='{{ asset('assets/images/404q.png') }}';"
            class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
        <span class="ms-2 py-0" v-text="report.teams[0]?.name"></span>
    </button>
    <button v-on:click="selectTeamToWin(event, 1)" :disabled="getDisabled()"
        class="selectedButton ps-0 btn d-block rounded-pill w-75 text-truncate mx-auto py-0 border border-dark text-start">
        <img v-bind:src="report.teams[1]?.banner ? '/storage/' + report.teams[1].banner : '/assets/images/.png' " alt="Team Banner" width="35" height="35"
            onerror="this.src='{{ asset('assets/images/404q.png') }}';"
            class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
        <span class="ms-2 py-0" v-text="report.teams[1]?.name"></span>
    </button>
        <p  
            v-text="reportUI.statusText"
            v-bind:class="['selectionMessage my-3 ',
            getDisabled() ? 'text-secondary' :  'text-primary'
            ]"   
        >   
        </p>
    <input type="hidden" id="selectedTeamIndex" type="text" name="selectedTeamIndex">

    <div class="d-flex justify-content-center  mt-2">
        <button class="btn border rounded-pill border-dark me-3" data-dismiss="modal" id="reportModalCancelBtn"
            data-bs-dismiss="modal"> Cancel </button>
        <button v-on:click="onSubmitSelectTeamToWin" class="selectTeamSubmitButton btn border rounded-pill text-light btn-secondary me-3"> Submit </button>
    </div>
</div>