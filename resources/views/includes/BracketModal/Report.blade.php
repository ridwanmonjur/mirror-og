<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered "   >
        <div class="modal-content modal-body-transparent " >
            <div class="modal-body m-0 p-0  ">
                <div class="{{ ' popover-report text-center  p-0 ' . 'Team1' . ' ' . 'Team2' }}"
                >

                    <div class=" popover-box bg-white border-2 border  row justify-content-start border border-dark border px-0 py-0"
                    >
                        <div class="text-center  mt-4">
                            <div class="border border-primary px-3 w-75 rounded-3 border d-inline-block my-1 py-1 mb-3 text-center mx-auto px-2">
                                <div class="mx-0 w-100" >
                                    <div class="d-flex justify-content-start mb-1 px-0">
                                        <a class="d-flex w-100 justify-content-start align-items-center"
                                            href="{{ route('public.event.view', ['id' => $event->id, 'title' => $event->slug ]) }}">

                                            <img 
                                                {!! trustedBladeHandleImageFailureBanner() !!} style="max-width: 50px; "
                                                src="{{ bladeImageNull($event->game->gameIcon) }}"
                                                class="object-fit-cover me-1 rounded-2" width="30px" height="30px"
                                                style="object-position: center;"    
                                            >
                                            <div class="text-truncate w-75 text-start pe-2"> 
                                                <b>
                                                <span class="ms-2"> {{ $event->eventName }}</span> 
                                                <span>@if ($event->tier->eventTier) 
                                                    <span>▪️ {{$event->tier->eventTier}}
                                                    @endif
                                                </span> 
                                                </span> 
                                                <span>@if ($event->game->gameTitle) 
                                                    <span>▪️ {{$event->game->gameTitle}}</span> 
                                                    @endif
                                                </span> 
                                                </b>
                                            </div>
                                        </a>
                                    </div>
                                    <div onclick="goToUrl(event, this)"
                                        data-url="{{ route('public.organizer.view', ['id' => $event->user->id, 'title' => $event->user->slug ]) }}"
                                        class=" d-flex justify-content-start align-items-center px-0 mx-0 ">
                                        <img 
                                            {!! trustedBladeHandleImageFailureBanner() !!}
                                            src="{{ $event->user->userBanner ? asset('storage/' . $event->user->userBanner) : '/assets/images/404.png' }}" 
                                            class="object-fit-cover me-2 rounded-circle rounded-circle2" >
                                        <div class="text-start d-inline-flex flex-column justify-content-center ">
                                            <small class="d-inline-block my-0 text-wrap ">{{ $event->user->name }} </small>
                                            <small class="small-text m-0" id="followCount" data-count="{{ $followersCount }}">
                                                {{ $followersCount }}
                                                    {{ $followersCount == 1 ? 'follower' : 'followers' }} 
                                            </small>
                                            {{-- <small
                                                data-count="{{ array_key_exists($joinEvent->eventDetails->user_id, $followCounts) ? $followCounts[$joinEvent->eventDetails->user_id] : 0 }} "
                                                class="d-block p-0 {{ 'followCounts' . $joinEvent->eventDetails?->user_id }}">
                                                {{ $followCounts[$joinEvent->eventDetails->user_id] }}
                                                follower{{ bladePluralPrefix($followCounts[$joinEvent->eventDetails->user_id]) }}
                                            </small> --}}
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            <br>
                            <h5 class="text-uppercase py-0 mt-1 mb-1 text-primary"><b> Match Results: <span  v-text="report.position"></span></b> </h5>
                            <p class="my-0 p-0 text-uppercase" v-text="report.completeMatchStatus"> </p>
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
                                    <span class="text-success" v-text="report.matchStatus[reportUI.matchNumber]"></span> 
                                </p>
                                <template v-if="userLevelEnums['IS_TEAM1'] === report.userLevel 
                                    || userLevelEnums['IS_TEAM2'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.__Report.Team')
                                </template>
                                <template v-if="userLevelEnums['IS_PUBLIC'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.__Report.Public')
                                </template>
                                <template v-if="userLevelEnums['IS_ORGANIZER'] === report.userLevel"
                                >
                                    @include('includes.BracketModal.__Report.Org')
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
