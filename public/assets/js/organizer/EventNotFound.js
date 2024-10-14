const goToManageScreen = () => {
    let url = document.getElementById('manage_event_route').value;
    window.location.href = url;            
}

const goToEditScreen = () => {
    let url = document.getElementById('edit_event_route').value;
    window.location.href = url;
}