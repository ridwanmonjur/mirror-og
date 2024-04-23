<div class="modal fade" id='editProfile' tabindex="-1" aria-labelledby={{ 'editProfile-label' }}
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form onsubmit="addAchievement(event);">
                <div class="modal-body modal-body-overflow scrollbarline pe-4">
                    <div class="mx-auto text-center mt-3">
                        <h5> Choose an achievement </h5>
                        <br>
                        <div>
                            <label class="form-check-label fw-bold">
                                Choose team
                            </label>
                            <select class="form-select mx-auto" name="teamId" aria-label="Select Team"
                                style="max-width: 200px !important;">
                                {{-- @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                    <option value="{{ $joinEventAndTeam->team_id }}">
                                        {{ $joinEventAndTeam->teamName }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <br>
                        <label class="form-check-label fw-bold">
                            Achievement Title
                        </label>
                        <input type="text" class="form-control mx-auto" name="title" style="width: 250px;">
                        <br>
                        <label class="form-check-label fw-bold">
                            Achievement Description
                        </label>
                        <input type="text" class="form-control" name="description">
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