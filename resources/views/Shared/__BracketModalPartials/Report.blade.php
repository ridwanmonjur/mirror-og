<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog "  style="min-width: 45vw;" >
        <div class="modal-content " style="background-color: transparent !important;  ">
            <div class="modal-body my-3 px-5 ">
                <br>
                <div class="{{ ' text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}"
                    style="opacity: 1; z-index: 999 !important; ">

                    <div class="user-select-none popover-box row justify-content-start border border-dark border px-2 py-2"
                        style="background-color: white; min-width: 400px !important; border-radius: 25px !important;">
                        <div class="text-center text-uppercase mt-4">
                            <h5> Match Results: <span  v-text="report.position"></span> </h5>
                            <p class="my-0 p-0" v-text="report.completeMatchStatus"> </p>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div>
                                <img 
                                    v-bind:src="'/storage/' + report.teams[0].banner"  
                                    alt="Team Banner" width="100"
                                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="mt-1 mb-0 py-0" v-text="report.teams[0]?.name"></p>
                            <div class="mt-1 mb-2 py-0 dotted-score-container">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <h1 class="pe-4" v-text="report.teams[0].score"></h1>
                                <h1>-</h3>
                                <h1 class="ps-4" v-text="report.teams[1].score"></h1>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div>
                                <img 
                                    v-bind:src="'/storage/' + report.teams[1].banner" 
                                    alt="Team Banner" width="100"
                                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="mt-1 mb-2 py-0" v-text="report.teams[1]?.name"></p>
                            <div class="mt-1 mb-2 py-0 dotted-score-container">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>

                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class=" user-select-none row px-0 mx-auto">
                            <div class="col-1 col-xl-2 px-0 h-100 d-flex justify-content-center align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    v-bind:class="{ 'd-none': reportUI.matchNumber === 0 }"
                                    v-on:click="changeMatchNumber(-1) "
                                    width="25" height="25" fill="#7f7f7f" 
                                    class="bi bi-chevron-left  cursor-pointer" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                                </svg>
                            </div>
                            <div class="col-10 col-xl-8 px-0">
                                <p>
                                    Game <span v-text="reportUI.matchNumber+1"> </span>
                                    <span class="text-success" v-text="report.matchStatus[reportUI.matchNumber]"></span> 
                                </p>
                                <template v-if="userLevelEnums['IS_TEAM1'] === report.userLevel 
                                    || userLevelEnums['IS_TEAM2'] === report.userLevel"
                                >
                                    @include('Shared.__BracketModalPartials.__Report.Team')
                                </template>
                                <template v-if="userLevelEnums['IS_PUBLIC'] === report.userLevel"
                                >
                                    @include('Shared.__BracketModalPartials.__Report.Public')
                                </template>
                                <template v-if="userLevelEnums['IS_ORGANIZER'] === report.userLevel"
                                >
                                    @include('Shared.__BracketModalPartials.__Report.Org')
                                </template>
                                <br>
                            </div> 
                            <div class="col-1 col-xl-2 px-0 h-100 d-flex justify-content-center align-items-center">
                               <svg
                                v-on:click="changeMatchNumber(1)"
                                xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#7f7f7f" 
                                class="bi bi-chevron-right cursor-pointer" 
                                v-bind:class="{ 'd-none': reportUI.matchNumber === 2 }"
                                viewBox="0 0 16 16"
                            >
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <script src="{{ asset('/assets/js/shared/BracketUpdateModal.js') }}"></script> --}}

</div>
