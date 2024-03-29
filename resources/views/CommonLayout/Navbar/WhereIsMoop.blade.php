<div class="team-dropdown">
    <button onclick="toggleTeamList()" class="oceans-gaming-default-button oceans-gaming-gray-button">Where is moop?</button>
    <div id="teamList" class="team-dropdown-content">
    <a href="{{ url('/participant/team/create/' ) }}">Create a Team</a>
    <a href="{{ url('/participant/team/list/' ) }}">Team List</a>
    <a href="{{ url('/participant/request/' ) }}">Team Requests</a>
    </div>
</div>