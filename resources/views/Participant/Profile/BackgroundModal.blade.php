<div class="modal fade" id="profileModal" tabindex="2" aria-labelledby="#profileModal" aria-hidden="true">
    <div class="modal-dialog w-75">
        <div class="modal-content">
            <div class="modal-body">
                <div class="tabs mx-0 d-flex flex-row justify-content-around px-0"
                    style="width: 100% !important;"
                >
                    <button class="tab-button px-0 mx-0 me-3 modal-tab tab-button-active"
                        style="padding-left: 0px; padding-right: 0px; min-width: 200px;"
                        onclick="showTab(event, 'BackgroundPhoto', 'modal-tab')"
                    >Custom Background</button>
                    <button class="tab-button  mx-0 px-0 modal-tab" 
                        onclick="showTab(event, 'BackgroundColor', 'modal-tab')"
                        style="padding-left: 0px; padding-right: 0px; min-width: 200px;"
                    >Choose Your Color</button>
                </div>
                <div class="tab-content pb-4 modal-tab" id="BackgroundPhoto">
                    Hi
                </div>
                <div class="tab-content pb-4 modal-tab d-none" id="BackgroundColor">
                    Bye
                </div>

                <button type="submit" class="oceans-gaming-default-button me-3">Submit
                </button>
                <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                    data-bs-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>
