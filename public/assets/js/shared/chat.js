let error = document.getElementById('errorMessage')?.value;
if (error) {
    localStorage.setItem("error", "true");
    localStorage.setItem("message", error);
}

window.onload = () => {
    window.loadMessage();
    const event = new CustomEvent("fetchstart"); 
    window.dispatchEvent(event);
};

