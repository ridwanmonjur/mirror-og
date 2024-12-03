const uploadButton = document.getElementById("upload-button");
const uploadButton2 = document.getElementById("upload-button2");

const imageUpload = document.getElementById("image-upload");
const uploadedImageList = document.getElementsByClassName("uploaded-image");
const uploadedImage = uploadedImageList[0];
uploadButton2?.addEventListener("click", function () {
    imageUpload.click();
});

imageUpload?.addEventListener("change", async function (e) {
    const file = e.target.files[0];

    try {
        const fileContent = await readFileAsBase64(file);
        const response = await fetch(publicProfileUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-type': 'application/json',
                'Accept': 'application/json',
                'credentials': 'include',
            },
            body: JSON.stringify({
                file: {
                    filename: file.name,
                    type: file.type,
                    size: file.size,
                    content: fileContent
                }
            }),
        });

        const data = await response.json();
        if (data.success) {
            uploadedImageList[0].style.backgroundImage = `url(${data.data.fileName})`;
            uploadedImageList[1].style.backgroundImage = `url(${data.data.fileName})`;
            document.querySelectorAll(".hyperlink-lightbox").forEach((hyperLinkElement) => {
                hyperLinkElement.setAttribute('href', data.data.fileName);
            });
            window.refreshFsLightbox();

        } else {
            console.error('Error updating member status:', data.message);
        }
    } catch (error) {
        console.error('There was a problem with the file upload:', error);
    }
});

async function readFileAsBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = function (event) {
            const base64Content = event.target.result.split(';base64,')[1];
            resolve(base64Content);
        };

        reader.onerror = function (error) {
            reject(error);
        };

        reader.readAsDataURL(file);
    });
}