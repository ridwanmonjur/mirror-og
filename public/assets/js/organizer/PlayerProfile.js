const input = document.querySelector("#phone");
window.intlTelInput(input, {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@22.0.2/build/js/utils.js",
});
const iti = window.intlTelInput.getInstance(input);
const storage = document.querySelector('.profile-storage');
const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles
};

const routes = {
    userBannerUrl: storage.dataset.userBannerUrl,
    routeBackgroundApi: storage.dataset.routeBackgroundApi
};

let initialUserProfile = JSON.parse(document.getElementById('initialUserProfile').value);
let initialOrganizer = JSON.parse(document.getElementById('initialOrganizer').value);
let initialAddress = JSON.parse(document.getElementById('initialAddress').value);



function reddirectToLoginWithIntened(route) {
    const loginRoute = document.getElementById('routeContainer').dataset.loginRoute;

    route = encodeURIComponent(route);
    let url = `${loginRoute}?url=${route}`;
    window.location.href = url;
}

carouselWork();
window.addEventListener('resize', debounce((e) => {
    carouselWork();
}, 250));


function redirectToProfilePage(userId) {
    const profileRoute = document.getElementById('routeContainer').dataset.profileRoute;
    window.location.href = profileRoute.replace(':id', userId);
}

function reddirectToLoginWithIntened(route) {
    const loginRoute = document.getElementById('routeContainer').dataset.loginRoute;
    route = encodeURIComponent(route);
    let url = `${loginRoute}?url=${route}`;
    window.location.href = url;
}


document.getElementById('followFormProfile')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    let followButtons = document.getElementsByClassName("followButton" + initialUserProfile.id);
    let followCounts = document.getElementsByClassName("followCounts" + initialUserProfile.id);
    console.log({ followButtons });
    let form = this;
    let formData = new FormData(form);
    [...followButtons].forEach((button) => {
        button.style.setProperty('pointer-events', 'none');
    });

    try {
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }
        let response = await fetch(form.action, {
            method: form.method,
            body: JSON.stringify(jsonObject),
            headers: {
                ...window.loadBearerHeader(),
                'Accept': 'application/json',
                "Content-Type": "application/json",
            }
        });

        let data = await response.json();
        [...followButtons].forEach((followButton) => {
            followButton.style.setProperty('pointer-events', 'none');

            if (data.isFollowing) {
                followButton.innerText = 'Following';
                followButton.style.backgroundColor = '#8CCD39';
                followButton.style.color = 'black';
            } else {
                followButton.innerText = 'Follow';
                followButton.style.backgroundColor = '#43A4D7';
                followButton.style.color = 'white';
            }

            followButton.style.setProperty('pointer-events', 'auto');
        });

        let count = Number(followCounts[0].dataset.count);
        if (data.isFollowing) {
            count++;
        } else {
            count--;
        }

        [...followCounts].forEach((followCount) => {
            followCount.dataset.count = count;
            if (count == 1) {
                followCount.innerHTML = '1 follower';
            } else if (count == 0) {
                followCount.innerHTML = `0 followers`;
            } else {
                followCount.innerHTML = `${followCount.dataset.count} followers`;
            }
        })
    } catch (error) {
        followButton.style.setProperty('pointer-events', 'auto');
        toastError('Error occured.', error);
    }
});

let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let backgroundBanner = document.getElementById("backgroundBanner")
let colorOrGradient = null;
function applyBackground(event, colorOrGradient) {
    document.querySelectorAll('.color-active').forEach(element => {
        element.classList.remove('color-active');
    });
    event.target.classList.add('color-active');
}
function chooseColor(event, color) {
    if (event) applyBackground(event, color);
    document.querySelector("input[name='backgroundColor']").value = color;
    document.querySelector("input[name='backgroundGradient']").value = null;
    document.getElementById('backgroundBanner').style.backgroundImage = 'none';
    document.getElementById('backgroundBanner').style.background = color;
}
function chooseGradient(event, gradient) {
    if (event) applyBackground(event, gradient);
    document.querySelector("input[name='backgroundColor']").value = null;
    document.querySelector("input[name='backgroundGradient']").value = gradient;
    document.getElementById('backgroundBanner').style.backgroundImage = gradient;
    document.getElementById('backgroundBanner').style.background = 'auto';
}
let successInput = document.getElementById('successMessage');
let errorInput = document.getElementById('errorMessage');
function formRequestSubmitById(message, id) {
    const form = document.getElementById(id);
    if (message) {
        window.dialogOpen(message, () => {
            console.log({ message, id })
            form?.submit();
        });
    } else {
        form?.submit();
    }
}
function visibleElements() {
    let elements = document.querySelectorAll('.show-first-few');
    elements.forEach((element) => element.classList.remove('d-none'));
    let element2 = document.querySelector('.show-more');
    element2.classList.add('d-none');
}
window.onload = () => {
    localStorage.setItem('isInited', "false");


    window.setupFileInputEditor('#changeBackgroundBanner', (file) => {
        if (file) {
            var cachedImage = URL.createObjectURL(file);
            document.getElementById('backgroundBanner').style.backgroundImage = `url(${cachedImage})`;
            document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
                cursiveElement.style.backgroundImage = `url(${cachedImage})`;
                cursiveElement.style.background = 'auto';
            });
            document.querySelector("input[name='backgroundColor']").value = null;
            document.querySelector("input[name='backgroundGradient']").value = null;
        }
    });
    // window.fileUploadPreviewById('file-upload-preview-1');
    window.createGradientPicker(document.getElementById('div-gradient-picker'),
        (gradient) => {
            chooseGradient(null, gradient);
        }
    );

    window.createColorPicker(document.getElementById('div-color-picker'),
        (color) => {
            chooseColor(null, color);
        }
    );
    window.createColorPicker(document.getElementById('div-font-color-picker-with-bg'),
        (color) => {
            document.querySelector("input[name='fontColor']").value = color;
            document.getElementById('backgroundBanner').style.color = color;
            backgroundBanner.querySelectorAll('.form-control').forEach((element) => {
                element.style.color = color;
            });
            backgroundBanner.querySelectorAll('.followCounts').forEach((element) => {
                element.style.color = color;
                element.style.fill = color;
            });
        }
    );
    window.createColorPicker(document.getElementById('div-font-color-picker-with-frame'),
        (color) => {
            document.querySelectorAll('.uploaded-image').forEach((element) => {
                document.querySelector("input[name='frameColor']").value = color;
                element.style.borderColor = color;
            })
        }
    );
    window.addEventListener(Events.IMAGE_ADDED, async (e) => {
        const { detail } = e;
        console.log('detail', detail);
        const file = detail.files[0];
        const fileContent = await readFileAsBase64(file);
        await changeBackgroundDesignRequest({
            backgroundBanner: {
                filename: file.name,
                type: file.type,
                size: file.size,
                content: fileContent
            }
        }, (data) => {
            if (backgroundBanner) {
                backgroundBanner.style.backgroundImage = `url(/storage/${data.data.backgroundBanner})`;
                backgroundBanner.style.background = 'auto';
            }
        }, (error) => {
            console.error(error);
        });
    });
    window.loadMessage();
}

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
        const response = await fetch(routes.userBannerUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-type': 'application/json',
                'Accept': 'application/json',
                ...window.loadBearerHeader()
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
        } else {
            console.error('Error updating member status:', data.message);
        }
    } catch (error) {
        console.error('There was a problem with the file upload:', error);
    }
});
async function changeBackgroundDesignRequest(body, successCallback, errorCallback) {
    try {
        const response = await fetch(routes.routeBackgroundApi, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-type': 'application/json',
                'Accept': 'application/json',
                ...window.loadBearerHeader()
            },
            body: JSON.stringify(body),
        });
        const data = await response.json();

        if (data.success) {
            successCallback(data);
        } else {
            errorCallback(data.message);
        }
    } catch (error) {
        errorCallback('There was a problem with the request: ' + error);
    }
}
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
function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    const loginRoute = document.getElementById('routeContainer').dataset.loginRoute;
    url = `${loginRoute}?url=${route}`;
    window.location.href = url;
}