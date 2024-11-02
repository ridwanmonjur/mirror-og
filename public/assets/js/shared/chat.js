window.onload = () => {
    window.loadMessage();
    const event = new CustomEvent("fetchstart"); 
    window.dispatchEvent(event);
};
