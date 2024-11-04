function uploadImageToBanner(event) {
    var file = event.target.files[0];
    if (file) {
        var cachedImage = URL.createObjectURL(file);
        backgroundBanner.style.backgroundImage = `url(${cachedImage})`;
    }
}