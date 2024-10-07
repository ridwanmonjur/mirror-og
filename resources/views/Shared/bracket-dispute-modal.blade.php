<div class="modal fade show" data-show="true" id="disputeModal" tabindex="3" aria-labelledby="disputeModalLabel"
    aria-hidden="false">
    <div class="modal-dialog " style="min-width: 90vw;">
        <div class="modal-content " style="background-color: transparent !important; ">
            <div class="modal-body my-3 px-5 ">
                <div class="row">
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 pe-0 pe-lg-5' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div class="row justify-content-start border border-3 border-dark border rounded px-2 py-2"
                            style="background-color: white; ps-5">
                            <h5 class="text-start my-3"> Event Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Name: Totally awesome event </p>
                                <p class="my-0">Organizer: Totally awesome organizer</p>
                                <br>
                                <p class="my-0"> Title: Totally awesome title </p>
                                <p class="my-0">Type: Tournament</p>
                                <p class="my-0">Tier: Starfish</p>
                                <p class="mb-3">Tier: Starfish</p>
                            </div>
                        </div>
                    </div>
                    <div class="{{ 'col-12 col-lg-6 text-center pt-0 pb-2 ps-0 ps-lg-5 pe-0 ' . 'Team1' . ' ' . 'Team2' }}"
                        style="opacity: 1; z-index: 999 !important; ">
                        <div class="row justify-content-start border border-3 border-dark border rounded px-2 py-2"
                            style="background-color: white; ps-5">
                            <h5 class="text-start my-3"> Match Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Match: U1</p>
                                <p class="my-0">Game: 3</p>
                                <br>
                                <div class="row px-0 w-75 mx-auto">
                                    <div class="col-12  text-center col-lg-4">
                                        <div>
                                            <img src="{{ asset('storage/images/team/teamBanner-1727678919.jpeg') }}"
                                                alt="Team Banner" width="50" height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-4 popover-content-img rounded-circle object-fit-cover"
                                            >
                                        </div>
                                        <p class="mt-1 mb-2 py-0">Team 1</p>
                                        <br>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <div class="d-flex  text-center justify-content-center align-items-center h-100">
                                            <h1>VS</h1>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4  text-center">
                                        <div>
                                            <img src="{{ asset('storage/') }}" alt="Team Banner" width="50"
                                                height="50"
                                                onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                                class="border border-4 popover-content-img rounded-circle object-fit-cover">
                                        </div>
                                        <p class="mt-1 mb-2 py-0">Team 2</p>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="{{ 'col-12 text-center pt-0 pb-2 px-0 ' . 'Team1' . ' ' . 'Team2' }}">
                        <div class="row justify-content-start border border-3 border-dark border rounded px-2 py-2"
                            style="background-color: white; ">
                            <h5 class="text-start my-3"> Dispute Information </h5>
                            <div class="ps-5 ps-5 text-start">
                                <p class="my-0"> Disputing Team </p>
                                <p class="my-0">Organizer: Totally awesome organizer</p>
                                <br>
                                <p class="my-0"> Title: Totally awesome title </p>
                                <p class="my-0">Type: Tournament</p>
                                <p class="my-0">Tier: Starfish</p>
                                <p class="mb-3">Tier: Starfish</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
