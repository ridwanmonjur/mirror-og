function goToRegistrationScreen(teamId) {
    let url = document.getElementById('register_route').value;
    url = url.replace(':id', teamId);
    window.location.href = url;
}

function goToViewEvent(eventId) {
    let url = document.getElementById('event_view_route').value;
    url = url.replace(':id', eventId);
    window.location.href = url;
}