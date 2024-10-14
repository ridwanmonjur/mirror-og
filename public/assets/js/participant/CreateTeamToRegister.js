function goToCancelButton(eventId) {
    let url = document.getElementById('event_view_route').value;
    url = url.replace(':id', eventId);
    window.location.href = url;
}