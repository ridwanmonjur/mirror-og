function redirectToProfilePage(userId) {
    const routes = document.getElementById('routeConfig');
    window.location.href = routes.dataset.profileRoute.replace(':id', userId);
}

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    document.getElementById(`${type}Btn`)?.click();
});