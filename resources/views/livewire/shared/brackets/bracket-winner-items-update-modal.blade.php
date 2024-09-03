<div class="modal fade" id="winnerMatchModal" tabindex="-1" aria-labelledby="winnerMatchModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="winnerMatchModalLabel">Create/Edit Match</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="order" class="form-label">Order</label>
            <input type="number" class="form-control" id="order" name="order" required>
          </div>
          <div class="mb-3">
            <label for="team1_id" class="form-label">Team 1</label>
            <select class="form-select" id="team1_id" name="team1_id" required>
              <!-- Populate options with team data -->
            </select>
          </div>
          <div class="mb-3">
            <label for="team2_id" class="form-label">Team 2</label>
            <select class="form-select" id="team2_id" name="team2_id" required>
              <!-- Populate options with team data -->
            </select>
          </div>
          <div class="mb-3">
            <label for="team1_score" class="form-label">Team 1 Score</label>
            <input type="number" class="form-control" id="team1_score" name="team1_score" min="0" value="0">
          </div>
          <div class="mb-3">
            <label for="team2_score" class="form-label">Team 2 Score</label>
            <input type="number" class="form-control" id="team2_score" name="team2_score" min="0" value="0">
          </div>
          <div class="mb-3">
            <label for="team1_position" class="form-label">Team 1 Position</label>
            <input type="text" class="form-control" id="team1_position" name="team1_position">
          </div>
          <div class="mb-3">
            <label for="team2_position" class="form-label">Team 2 Position</label>
            <input type="text" class="form-control" id="team2_position" name="team2_position">
          </div>
          <div class="mb-3">
            <label for="winner_id" class="form-label">Winner</label>
            <select class="form-select" id="winner_id" name="winner_id">
              <option value="">Select Winner</option>
              <!-- Populate options with team data -->
            </select>
          </div>
          <div class="mb-3">
            <label for="winner_next_position" class="form-label">Winner Next Position</label>
            <input type="text" class="form-control" id="winner_next_position" name="winner_next_position">
          </div>
          <div class="mb-3">
            <label for="loser_next_position" class="form-label">Loser Next Position</label>
            <input type="text" class="form-control" id="loser_next_position" name="loser_next_position">
          </div>
          <div class="mb-3">
            <label for="team1_points" class="form-label">Team 1 Points</label>
            <input type="number" class="form-control" id="team1_points" name="team1_points" min="0" value="0">
          </div>
          <div class="mb-3">
            <label for="team2_points" class="form-label">Team 2 Points</label>
            <input type="number" class="form-control" id="team2_points" name="team2_points" min="0" value="0">
          </div>
          <div class="mb-3">
            <label for="event" class="form-label">Event</label>
            <input type="text" class="form-control" id="event" name="event">
          </div>
          <div class="mb-3">
            <label for="match_type" class="form-label">Match Type</label>
            <select class="form-select" id="match_type" name="match_type" required>
              <option value="league">League</option>
              <option value="tournament">Tournament</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="stage_name" class="form-label">Stage Name</label>
            <input type="text" class="form-control" id="stage_name" name="stage_name">
          </div>
          <div class="mb-3">
            <label for="inner_stage_name" class="form-label">Inner Stage Name</label>
            <input type="text" class="form-control" id="inner_stage_name" name="inner_stage_name">
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="upcoming">Upcoming</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="result" class="form-label">Result</label>
            <input type="text" class="form-control" id="result" name="result">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>