window.onload = async function  () {
    const event = new CustomEvent("fetchstart"); 
    window.dispatchEvent(event);
};

