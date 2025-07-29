<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered "   >
        <div class="modal-content modal-body-transparent " >
            <div class="modal-body m-0 p-0  ">
                <div class="{{ ' popover-report text-center  p-0 ' . 'Team1' . ' ' . 'Team2' }}"
                >

                    <div class=" popover-box bg-white border-2 border  row justify-content-start border border-dark border px-0 py-0"
                    >
                        <div class="text-center  mt-4">
                             <h5 class="text-uppercase py-0 mt-0 mb-0 text-primary">
                                <svg width="20" height="20" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#43a4d7;} </style> <g> <rect x="119.256" y="222.607" class="st0" width="50.881" height="50.885"></rect> <rect x="341.863" y="222.607" class="st0" width="50.881" height="50.885"></rect> <rect x="267.662" y="222.607" class="st0" width="50.881" height="50.885"></rect> <rect x="119.256" y="302.11" class="st0" width="50.881" height="50.885"></rect> <rect x="267.662" y="302.11" class="st0" width="50.881" height="50.885"></rect> <rect x="193.46" y="302.11" class="st0" width="50.881" height="50.885"></rect> <rect x="341.863" y="381.612" class="st0" width="50.881" height="50.885"></rect> <rect x="267.662" y="381.612" class="st0" width="50.881" height="50.885"></rect> <rect x="193.46" y="381.612" class="st0" width="50.881" height="50.885"></rect> <path class="st0" d="M439.277,55.046h-41.376v39.67c0,14.802-12.195,26.84-27.183,26.84h-54.025 c-14.988,0-27.182-12.038-27.182-26.84v-39.67h-67.094v39.297c0,15.008-12.329,27.213-27.484,27.213h-53.424 c-15.155,0-27.484-12.205-27.484-27.213V55.046H72.649c-26.906,0-48.796,21.692-48.796,48.354v360.246 c0,26.661,21.89,48.354,48.796,48.354h366.628c26.947,0,48.87-21.692,48.87-48.354V103.4 C488.147,76.739,466.224,55.046,439.277,55.046z M453.167,462.707c0,8.56-5.751,14.309-14.311,14.309H73.144 c-8.56,0-14.311-5.749-14.311-14.309V178.089h394.334V462.707z"></path> <path class="st0" d="M141.525,102.507h53.392c4.521,0,8.199-3.653,8.199-8.144v-73.87c0-11.3-9.27-20.493-20.666-20.493h-28.459 c-11.395,0-20.668,9.192-20.668,20.493v73.87C133.324,98.854,137.002,102.507,141.525,102.507z"></path> <path class="st0" d="M316.693,102.507h54.025c4.348,0,7.884-3.513,7.884-7.826V20.178C378.602,9.053,369.474,0,358.251,0H329.16 c-11.221,0-20.349,9.053-20.349,20.178v74.503C308.81,98.994,312.347,102.507,316.693,102.507z"></path> </g> </g></svg>                                <b> Match Results: <span  v-text="report.position"></span></b>     
                            </h5>
                            <p class="my-0 py-0">
                                <span v-bind:class="'my-0 p-0  text-uppercase Color-'+ report.completeMatchStatus + ' '" v-text="report.completeMatchStatus"> </span>
                            </p>
                            
                            <small style="color: #757577;" class="mt-0 mb-2 py-0 w-75 mx-auto d-block text-center text-truncate ">
                                @if ($event->eventName)
                                    <span>{{$event->eventName}}  </span>
                                @endif
                               
                            </small> 
                            <template v-if="report.deadline && userLevelEnums['IS_PUBLIC'] != report.userLevel">
                                <div class="my-0 py-0 ">
                                    <template v-if="!report.deadline.has_started">
                                        <div class="text-center small text-primary my-0">
                                            Reporting available in: 
                                            <span class="text-center" v-scope="CountDown({targetDate: report.deadline.diff_date}) " @vue:mounted="init()" @vue:unmounted="stopTimer()">
                                                <span class="text-center" v-text="dateText"> </span>
                                            </span>
                                        </div>
                                    </template>
                                    <template v-if="report.deadline.has_started && !report.deadline.has_ended">
                                        <div class="text-center text-red small  my-0">
                                            Time left to report:
                                            <span class="text-center" v-scope="CountDown({targetDate: report.deadline.diff_date}) " @vue:mounted="init()" @vue:unmounted="stopTimer()">
                                                <span class="text-center" v-text="dateText"> </span>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="col-4 px-0">
                            <div>
                                <img 
                                    v-bind:src="'/storage/' + report.teams[0].banner"  
                                    alt="Team Banner"  onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                                    class="border border-2 popover-team-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="mt-1 mb-0 py-0" v-text="report.teams[0]?.name"></p>
                            <div class="my-0 mb-2 py-0 dotted-score-container">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
                            </div>
                        </div>
                        <div class="col-4 px-0">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <h1 class="pe-1 py-0 my-0" v-text="report.teams[0].score"></h1>
                                <h1>-</h3>
                                <h1 class="ps-1 py-0 my-0" v-text="report.teams[1].score"></h1>
                            </div>
                        </div>
                        <div class="col-4 px-0">
                            <div>
                                <img 
                                    v-bind:src="'/storage/' + report.teams[1].banner" 
                                    alt="Team Banner"  onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                                    class="border border-2 popover-team-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="my-0 py-0" v-text="report.teams[1]?.name"></p>
                            <div class="my-0 mb-2 py-0 dotted-score-container">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>

                            </div>
                        </div>
                        
                        <hr class="d-none d-lg-block border border-dark">
                        <div class="  row px-0 mx-auto">
                            <div class="col-2 col-lg-1 px-0 h-100 d-flex justify-content-center align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    v-bind:class="{ 'd-none': reportUI.matchNumber === 0 }"
                                    v-on:click="changeMatchNumber(-1) "
                                    width="25" height="25" fill="#7f7f7f" 
                                    class="bi bi-chevron-left  cursor-pointer" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                                </svg>
                            </div>
                            <div class="col-8 col-lg-10 px-0">
                                <p>
                                    Game <span v-text="reportUI.matchNumber+1"> </span>
                                    <span v-bind:class="'fw-bold Color-'+ report.completeMatchStatus + ' '"  v-text="report.matchStatus"></span> 
                                </p>
                                <template v-if="userLevelEnums['IS_TEAM1'] === report.userLevel 
                                    || userLevelEnums['IS_TEAM2'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.Report.Team')
                                </template>
                                <template v-if="userLevelEnums['IS_PUBLIC'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.Report.Public')
                                </template>
                                <template v-if="userLevelEnums['IS_ORGANIZER'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.Report.Org')
                                </template>
                                <br>
                            </div> 
                            <div class="col-2 col-lg-1 px-0 h-100 d-flex justify-content-center align-items-center">
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
