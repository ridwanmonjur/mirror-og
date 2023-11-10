// open/ close
function openElementById(id) {
    const element = document.getElementById(id);
    if (element) element?.classList.remove("d-none");
}

function closeElementById(id) {
    const element = document.getElementById(id);
    if (element && !element.classList.contains("d-none")) element?.classList.add("d-none");
}

export { openElementById, closeElementById };