
<div class="modal fade" id='editProfile' tabindex="-1" aria-labelledby={{ 'editProfile-label' }}
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form onsubmit="addAchievement(event);">
                <div class="modal-body modal-body-overflow scrollbarline pe-4">
                    <div class="mx-auto text-center mt-3">
                        <h5> Profile </h5>
                        <br>
                        <label class="form-check-label fw-bold">
                            Name
                        </label>
                        <input value="{{$userProfile->name}}" type="text" class="form-control rounded-pill" name="title">
                        <br>
                        <div class="input-group mb-3">
                            <span style="border-radius: 20px 0 0px 20px;" class="input-group-text" id="basic-addon1">@</span>
                            <input style="border-radius: 0 20px 20px 0px;" value="{{$userProfile->domain}}" type="text" value="domain" class="form-control" placeholder="Domain" aria-label="domain" aria-describedby="basic-addon1">
                        </div>
                        <label class="form-check-label fw-bold">
                            Bio
                        </label>
                        <textarea value="{{$userProfile->bio}}"  class="form-control" style="border-radius: 30px;" rows="4" name="description"> </textarea>
                        <br>
                        <br>
                        <br>
                        <button type="submit" class="oceans-gaming-default-button">Submit
                        </button>
                        <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                            data-bs-dismiss="modal">Close
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>