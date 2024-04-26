<div class="modal fade" id='editModal' tabindex="-1" aria-labelledby={{ 'editModal-label' }}
    aria-hidden="true">
    <div class="modal-dialog">
        <div x-data="{ countries: [], errorMessage: '' }"
            x-init="$nextTick(async () => { 
                countries = await fetchCountries();
            })" 
            class="modal-content"
        >
            <form onclick="editEvent(this)" action="{{ route('participant.team.editStore', ['id' => $selectTeam->id]) }}" method="POST">
                <div class="modal-body modal-body-overflow scrollbarline pe-4">
                    <div class="mx-auto text-center mt-3">
                        <h5> Edit team </h5>
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
                        <textarea  class="form-control" style="border-radius: 30px;" rows="4" name="description"> </textarea>
                        <br>
                        <select value="{{$selectTeam->country}}" class="form-control rounded-pill">
                            <template x-for="country in countries">
                                <option x-bind:value="country.name.en">
                                <span x-text="country.emoji_flag" class="mx-3"> </span>  
                                <span x-text="country.name.en"> </span>
                                </option>
                            </template>
                        </select>
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
<script>
    const fetchCountries = () => {
        return fetch('/countries')
            .then(response => response.json())
            .then(data => {
                console.log({data})
                return data?.data;
            })
            .catch(error => console.error('Error fetching countries:', error));
    }
</script>

