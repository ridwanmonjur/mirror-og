<div class="modal fade" id='editModal' tabindex="-1" aria-labelledby={{ 'editModal-label' }}
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form onclick="editEvent(this)" action="{{ route('participant.team.editStore', ['id' => $selectTeam->id]) }}" method="POST">
                <div class="modal-body modal-body-overflow scrollbarline pe-4">
                    <div class="mx-auto text-center mt-3">
                        <h5> Edit team </h5>
                        <br>
                        <div class="text-red text-start d-none"> Hi </div>
                        <br>
                        <label class="form-check-label fw-bold">
                            Name
                        </label>
                        <input type="text" class="form-control mx-auto rounded-pill" name="name">
                        <br>
                        <label class="form-check-label fw-bold">
                            Description
                        </label>
                        <p>First push after renaming repo </p>
                        <textarea  class="form-control" style="border-radius: 30px;" rows="4" name="description"> </textarea>
                        <br><br>
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
