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

const statusMap = {
    successJoin: storage.dataset.successJoin,
    errorJoin: storage.dataset.errorJoin
};

let teamData = JSON.parse(document.getElementById('teamData').value);
let csrfToken77 = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


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

    if (statusMap['successJoin']) {
        window.Swal.fire({
            icon: 'success',
            confirmButtonColor: "#43A4D7",
            text: statusMap['successJoin']
        });
    }
    
    if (statusMap['errorJoin']) {
        toastError(statusMap['errorJoin'])
    }


    window.loadMessage();

    // window.motion.slideInLeftRight();

    // document.querySelectorAll('.animation-container').forEach(element => {
    //     window.motion.createStaggerChildren(element);
    // });

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

    if (file) {
        try {
            const fileUrl = URL.createObjectURL(file);

            uploadedImageList[0].style.backgroundImage = `url(${fileUrl})`;
            uploadedImageList[1].style.backgroundImage = `url(${fileUrl})`;
        } catch (error) {
            toastError("Failed to upload this file!")
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


let tabButtonBalueValue = localStorage.getItem("tab");
let currentTabIndexForNextBack = 0;
if (tabButtonBalueValue !== null || tabButtonBalueValue!== undefined){
    if (tabButtonBalueValue === "PendingMembersBtn") {
        currentTabIndexForNextBack = 1;
    }

    if (tabButtonBalueValue === "NewMembersBtn") {
        currentTabIndexForNextBack = 2;
    }
}

function goBackScreens () {
    if (currentTabIndexForNextBack <=0 ) {
        Toast.fire({
            'icon': 'success',
            'text': 'Notifications sent already!'
        });
    } else {
        let tabs = document.querySelectorAll('.tab-content');
        console.log({tabs, tabsChildren: tabs});
        for (let tabElement of tabs) {
            tabElement.classList.add('d-none');
        }

        currentTabIndexForNextBack--;
        tabs[currentTabIndexForNextBack].classList.remove('d-none');
    }
}

function goNextScreens () {
    if (currentTabIndexForNextBack >= 2) {
        document.getElementById('manageRosterUrl').click();
    } else {

        let tabs = document.querySelectorAll('.tab-content');
        console.log({tabs, tabsChildren: tabs, currentTabIndexForNextBack});

        for (let tabElement of tabs) {
            tabElement.classList.add('d-none');
        }

        currentTabIndexForNextBack++;
        tabs[currentTabIndexForNextBack].classList.remove('d-none');
    }
}

let actionMap = {
    'approve': approveMemberAction,
    'disapprove': disapproveMemberAction,
    'invite': inviteMemberAction,
    'deleteInvite': withdrawInviteMemberAction,
    'reject': rejectMemberAction
};

let dialogForMember = new DialogForMember();

function generateHeaders() {
    return {
        'X-CSRF-TOKEN': csrfToken77,
        'credentials': 'include', 
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
}


addOnLoad( () => { window.loadMessage(); } )


function reloadUrl(currentUrl) {
    if (currentUrl.includes('?')) {
        currentUrl = currentUrl.split('?')[0];
    } 

    localStorage.setItem('success', 'true');
    localStorage.setItem('message', 'Successfully updated user.');
    window.location.replace(currentUrl);
}

function toastError(message, error = null) {
    console.error(error)
    Toast.fire({
        icon: 'error',
        text: message
    });
}

function takeYesAction() {
    console.log({
        memberId: dialogForMember.getMemberId(),
        action: dialogForMember.getActionName()
    })

    const actionFunction = actionMap[dialogForMember.getActionName()];
    if (actionFunction) {
        actionFunction();
    } else {
        Toast.fire({
            icon: 'error',
            text: "No action found."
        })
    }
} 

function takeNoAction() {
    dialogForMember.reset();
}

function approveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('approve')
    window.dialogOpen('Continue with accepting team?', takeYesAction, takeNoAction)
}

function inviteMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('invite')
    window.dialogOpen('Are you sure you want to send invite to this team?', takeYesAction, takeNoAction)
}

function withdrawInviteMember(memberId, teamId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setTeamId(teamId);
    dialogForMember.setActionName('deleteInvite')
    window.dialogOpen('Are you sure you want to delete your invite to this team?', takeYesAction, takeNoAction)
}

function disapproveMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('disapprove')
    window.dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
}

function rejectMember(memberId) {
    dialogForMember.setMemberId(memberId);
    dialogForMember.setActionName('reject')
    window.dialogOpen('Continue with rejecting this team?', takeYesAction, takeNoAction)
}



function approveMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl('participantMemberUpdateUrl', memberId);

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error accepting member.', error);},  
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'user', 'status' : 'accepted'
            })
        }
    );
}

async function disapproveMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl("participantMemberUpdateUrl", memberId);
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error disapproving member.', error);}, 
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'team', 'status' : 'left'
            })
        }
    );
}

async function rejectMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const url = getUrl("participantMemberUpdateUrl", memberId);
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error disapproving member.', error);}, 
        {
            headers: generateHeaders(), 
            body: JSON.stringify({
               'actor' : 'user', 'status' : 'rejected'
            })
        }
    );
}


async function inviteMemberAction() {
    const memberId = dialogForMember.getMemberId();
    const teamId = dialogForMember.getTeamId();
    const urlTemplate = document.getElementById('participantMemberInviteUrl').value;
    const url = urlTemplate.replace(':userId', memberId).replace(':id', teamId);

    fetchData(
        url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
            } else {
               toastError(responseData.message);
            }
        },
        function(error) { toastError('Error inviting members.', error); }, 
        {  headers: generateHeaders(),  }
    );
}

async function withdrawInviteMemberAction() {
    const memberId = dialogForMember.getMemberId();
    
    const urlTemplate = document.getElementById('participantMemberDeleteInviteUrl').value;
    const url = urlTemplate.replace(':id', memberId);

    fetchData(
        url,
        function(responseData) {
            if (responseData.success) {
                let currentUrl = document.getElementById('currentMemberUrl').value;
                reloadUrl(currentUrl);
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error deleting invite members.', error);}, 
        {  headers: generateHeaders()  }
    );
}

function uploadImageToBanner(event) {
    var file = event.target.files[0];
    if (file) {
        var cachedImage = URL.createObjectURL(file);
        backgroundBanner.style.backgroundImage = `url(${cachedImage})`;
    }
}