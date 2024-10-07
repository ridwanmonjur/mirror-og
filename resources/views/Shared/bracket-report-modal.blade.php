<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content " style="background-color: transparent !important; ">
            <div class="modal-body my-3 px-5 ">
                <br>
                <div class="{{ ' text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}"
                    style="opacity: 1; z-index: 999 !important; ">

                    <div class="popover-box row justify-content-start border border-dark border rounded px-2 py-2"
                        style="background-color: white; min-width: 400px !important;">
                        <div class="text-center text-uppercase mt-4">
                            <h5> Match Results: U1 </h5>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div>
                                <img src="{{ asset('storage/images/team/teamBanner-1727678919.jpeg' ) }}" alt="Team Banner" width="100"
                                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="mt-1 mb-0 py-0">Team 1</p>
                            <div class="mt-1 mb-2 py-0">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary"
                                    style="width: 6px; height: 6px;"></div>
                                <div class="d-inline-block rounded-circle bg-secondary"
                                    style="width: 6px; height: 6px;"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <h1 class="pe-4">6</h1>
                                <h1>-</h3>
                                    <h1 class="ps-4">0</h1>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div>
                                <img src="{{ asset('storage/' ) }}" alt="Team Banner" width="100"
                                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                                >
                            </div>
                            <p class="mt-1 mb-2 py-0">Team 2</p>
                            <div class="mt-1 mb-2 py-0 ">
                                <div class="d-inline-block rounded-circle me-3 bg-secondary"
                                    style="width: 6px; height: 6px;"></div>
                                <div class="d-inline-block rounded-circle  bg-secondary"
                                    style="width: 6px; height: 6px;"></div>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="row px-0 mx-auto">
                            <div class="col-1 col-xl-2 px-0 h-100 d-flex justify-content-center align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#7f7f7f" 
                                class="bi bi-chevron-left  cursor-pointer" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                                </svg>
                            </div>
                            <div class="col-10 col-xl-8 px-0">
                                <p>
                                    Game 2:
                                    <span class="text-success">ONGOING</span> 
                                </p>
                                <button class="ps-0 btn mb-2 d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
                                    <img src="{{ asset('storage/images/team/teamBanner-1727678919.jpeg' ) }}" alt="Team Banner" width="35"
                                        height="35" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover"
                                    >
                                    <small class="ms-2 py-0">Team 1</small>
                                </button>
                                <button class="ps-0 btn d-block rounded-pill w-100 mx-auto py-0 border border-dark text-start">
                                    <img src="{{ asset('storage/images/team/' ) }}" alt="Team Banner" width="35"
                                        height="35" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                        class="ms-0 border border-1 border-dark popover-content-img rounded-circle object-fit-cover"
                                    >
                                    <small class="ms-2 py-0">Team 2</small>
                                </button>
                                <br>
                            </div> 
                            <div class="col-1 col-xl-2 px-0 h-100 d-flex justify-content-center align-items-center">
                               <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#7f7f7f" 
                                class="bi bi-chevron-right cursor-pointer" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </div> 
                        </div>
                        <br>
                        <div class="mb-4">
                           <button type="button" 
                                onclick="document.getElementById('reportModalCancelBtn')?.click();"    
                                class="btn btn-primary rounded-pill text-light d-inline px-3 py-2" 
                                data-bs-toggle="modal" data-bs-target="#disputeModal"
                            >
                                Launch dispute
                            </button>
                        </div>
                        <br>
                         <div class="d-flex justify-content-center mb-4">
                            <button class="btn border rounded-pill border-dark me-3" id="reportModalCancelBtn" data-bs-dismiss="modal"> Cancel </button>
                            <button class="btn border rounded-pill text-light btn-secondary me-3"> Submit </button>
                        </div>
                        
                    </div>
                </div>

               

            </div>

        </div>
    </div>

    <script src="{{ asset('/assets/js/shared/BracketUpdateModal.js') }}"></script>

</div>
