function addOnLoad(newFunction) {
    const oldOnLoad = window.onload;
    if (typeof window.onload !== 'function') {
        window.onload = newFunction;
    } else {
        window.onload = function() {
            if (oldOnLoad) {
                oldOnLoad();
            }
            newFunction();
        };
    }
}
