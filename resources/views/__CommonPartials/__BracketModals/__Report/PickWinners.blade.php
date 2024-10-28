<button x-on:click="selectTeamToWin(event, 0)" :disabled="getDisabled()"
    class="selectedButton ps-0 btn mb-2 mt-2 d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
    <img :src="'/storage/' + report.teams[0].banner" alt="Team Banner" width="35" height="35"
        onerror="this.src='{{ asset('assets/images/404.png') }}';"
        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
    <small class="ms-2 py-0" x-text="report.teams[0]?.name"></small>
</button>
<button x-on:click="selectTeamToWin(event, 1)" :disabled="getDisabled()"
    class="selectedButton ps-0 btn d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
    <img :src="'/storage/' + report.teams[1].banner" alt="Team Banner" width="35" height="35"
        onerror="this.src='{{ asset('assets/images/404.png') }}';"
        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover">
    <small class="ms-2 py-0" x-text="report.teams[1]?.name"></small>
</button>
<template x-if="getDisabled()">
    <p class="selectionMessage text-secondary my-3">Selection is not yet available.</p>
</template>
<template x-if="!getDisabled()">
    <p class="selectionMessage text-primary my-3">Select a winner for Game
        <span x-text="reportUI.matchNumber+1">
        </span>.
    </p>
</template>
<input type="hidden" id="selectedTeamIndex" type="text" name="selectedTeamIndex">

<div class="d-flex justify-content-center  mt-2">
    <button class="btn border rounded-pill border-dark me-3" data-dismiss="modal" id="reportModalCancelBtn"
        data-bs-dismiss="modal"> Cancel </button>
    <button x-on:click="onSubmitSelectTeamToWin" class="selectTeamSubmitButton btn border rounded-pill text-light btn-secondary me-3"> Submit </button>
</div>
