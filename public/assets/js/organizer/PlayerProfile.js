const input = document.querySelector("#phone");

const storage = document.querySelector('.profile-storage');
const styles = {
    backgroundStyles: storage.dataset.backgroundStyles,
    fontStyles: storage.dataset.fontStyles
};

const routes = {
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


document.querySelectorAll('.followFormProfile')?.forEach((elementOuter) => {
    elementOuter.addEventListener('submit', async function (event) {
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
                'credentials': 'include',
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

            if (count < 0) {
                window.location.reload();
            }

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
        toastError('Error occured.');
    }
})});

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
    document.getElementById('changeBackgroundBanner').addEventListener('click', (event)=> {
        event.currentTarget.value = '';
    });

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
    
    window.loadMessage();

    // document.querySelectorAll('.animation-container').forEach(element => {
    //     window.motion.createStaggerChildren(element);
    // });

    // window.motion.slideInLeftRight();
}

const uploadButton = document.getElementById("upload-button");
const uploadButton2 = document.getElementById("upload-button2");
const imageUpload = document.getElementById("image-upload");
const uploadedImageList = document.getElementsByClassName("uploaded-image");
const uploadedImage = uploadedImageList[0];
uploadButton2?.addEventListener("click", function () {
    imageUpload.value = "";
    imageUpload.click();
});

imageUpload?.addEventListener("change", async function (e) {
    const file = e.target.files[0];
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
        toastError("Please select only PNG or JPG/JPEG images!");
        imageUpload.value = ""; 
        return;
    }

    try {
        const fileUrl = URL.createObjectURL(file);
        uploadedImageList[0].style.backgroundImage = `url(${fileUrl})`;
        uploadedImageList[1].style.backgroundImage = `url(${fileUrl})`;
    } catch (error) {
        imageUpload.value = "";
        console.error('Error displaying image:', error);
    }
});

function reddirectToLoginWithIntened(route) {
    route = encodeURIComponent(route);
    const loginRoute = document.getElementById('routeContainer').dataset.loginRoute;
    url = `${loginRoute}?url=${route}`;
    window.location.href = url;
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

function uploadImageToBanner(event) {
    var file = event.target.files[0];
    if (file) {
        var cachedImage = URL.createObjectURL(file);
        backgroundBanner.style.backgroundImage = `url(${cachedImage})`;
    }
}