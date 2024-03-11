<div class="d-flex flex-column align-items-center justify-content-center">
    <input type="text" value="{{ $team ? $team->teamName : '' }}" name="teamName" id="teamName" placeholder="Team Name"
        onclick="clearPlaceholder(this)" onblur="restorePlaceholder(this)">
    <input type="text" style="height: 100px;" value="{{ $team ? $team->teamDescription : '' }}" name="teamDescription"
        id="teamDescription" placeholder="Write your team description..." onclick="clearPlaceholder(this)"
        onblur="restorePlaceholder(this)">
    <br> <br>
    <input type="submit" onclick="" value="{{ $buttonLabel }}">
    
</div>
