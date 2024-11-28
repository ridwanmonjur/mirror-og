const storage = document.querySelector('.team-head-storage');

const routes = {
    signin: storage.dataset.routeSignin,
    profile: storage.dataset.routeProfile,
    teamBanner: storage.dataset.routeTeamBanner,
    backgroundApi: storage.dataset.routeBackgroundApi
};

const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles
};

let teamData = JSON.parse(document.getElementById('teamData').value);
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


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
    localStorage.setItem('colorOrGradient', color);
    document.getElementById('backgroundBanner').style.backgroundImage = 'none';
    document.getElementById('backgroundBanner').style.background = color;
    document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
        cursiveElement.style.backgroundImage = 'none';
        cursiveElement.style.background = color;
    });
    document.getElementById('changeBackgroundBanner').value = null;
}

function chooseGradient(event, gradient) {
    console.log({ gradient });
    if (event) applyBackground(event, gradient);
    document.querySelector("input[name='backgroundColor']").value = null;
    document.querySelector("input[name='backgroundGradient']").value = gradient;
    localStorage.setItem('colorOrGradient', gradient);
    document.getElementById('backgroundBanner').style.backgroundImage = gradient;
    document.getElementById('backgroundBanner').style.background = 'auto';
    document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
        cursiveElement.style.backgroundImage = gradient;
        cursiveElement.style.background = 'auto';
    });
    document.getElementById('changeBackgroundBanner').value = null;
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

let newFunction = function () {
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

    localStorage.setItem('isInited', "false");

    if (successInput) {
        localStorage.setItem('success', 'true');
        localStorage.setItem('message', successInput.value);
    } else if (errorInput) {
        localStorage.setItem('error', 'true');
        localStorage.setItem('message', errorInput.value);
    }


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
            let backgroundBanner2 = document.getElementById('backgroundBanner');
            backgroundBanner2.style.color = color;
            document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
                cursiveElement.style.color = color;
            });

            backgroundBanner.querySelectorAll('.form-control').forEach((element) => {
                element.style.color = color;
            });

            document.getElementById('team-name').color = color;
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


    window.loadMessage();
}

let oldOnLoad = window.onload;
if (typeof window.onload !== 'function') {
    window.onload = newFunction;
} else {
    window.onload = function () {
        if (oldOnLoad) {
            oldOnLoad();
        }
        newFunction();
    };
}

let uploadButton = document.getElementById("upload-button");
let uploadButton2 = document.getElementById("upload-button2");
let imageUpload = document.getElementById("image-upload");
let uploadedImageList = document.getElementsByClassName("uploaded-image");
let uploadedImage = uploadedImageList[0];
let backgroundBanner = document.getElementById("backgroundBanner")

uploadButton2?.addEventListener("click", function () {
    imageUpload.click();
});

imageUpload?.addEventListener("change", async function (e) {
    const file = e.target.files[0];

    if (file) {
        const formData = new FormData();
        formData.append('file', file);
        try {
            const response = await fetch(routes.teamBanner, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            const data = await response.json();
            console.log({ data })
            if (data.success) {
                uploadedImageList[0].style.backgroundImage = `url(/storage/${data.data.fileName})`;
                uploadedImageList[1].style.backgroundImage = `url(/storage/${data.data.fileName})`;
            } else {
                console.error('Error updating member status:', data.message);
            }
        } catch (error) {
            console.error('Error approving member:', error);
        }
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

function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    window.location.href = `${routes.signin}?url=${route}`;
}

function redirectToProfilePage(userId) {
    window.location.href = routes.profile.replace(':id', userId);
}