let currentUrl = getUrl('currentUrlInput');
let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let eventId = document.getElementById('eventId').value;
var awardToDeleteId = null;
var achievementToDeleteId = null;
var actionToTake = null;
const actionMap = {
    'achievement': deleteAchievementsAction,
    'award': deleteAwardAction
};

function loadBearerCompleteHeader() {
    return {
        credentials: 'include',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
}
 
window.onload = () => { window.loadMessage(); loadTab(); }

function reloadUrl(currentUrl, message, tab) {
    if (currentUrl.includes('?')) {
        currentUrl = currentUrl.split('?')[0];
    }

    localStorage.setItem('success', 'true');
    localStorage.setItem('message', message);
    localStorage.setItem('tab', tab);
    window.location.replace(currentUrl);
}

function loadTab() {
    let main = document.querySelector('main');
    main.classList.remove("d-none");
    let tab = localStorage.getItem('tab');

    if (!tab || tab == '') {
        tab = 'PositionBtn';
    }

    let tabElement = document.getElementById(tab);
    if (tabElement) {
        tabElement.click();
        window.scrollTo(
            {
                bottom: tabElement.getBoundingClientRect().bottom,
                behavior: 'smooth' 
            }
        )
    }
}

function takeYesAction() {
    const actionFunction = actionMap[actionToTake];
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
    awardToDeleteId = null;
    achievementToDeleteId = null;
    actionToTake = null;
}

function deleteAward(id) {
    awardToDeleteId = id;
    actionToTake = 'award';
    window.dialogOpen('Are you sure you want to remove this award from this user?', takeYesAction, takeNoAction)
}

function deleteAchievement(id) {
    achievementToDeleteId = id;
    actionToTake = 'achievement';
    window.dialogOpen('Are you sure you want to remove this achievement from this user?', takeYesAction, takeNoAction)
}

function editCreatePosition(event) {
    event.preventDefault();
    let formData = new FormData(event.target);
    let joinEventId = formData.get('id');
    let joinEventPosition = formData.get('position');
    const url = getUrl('event-results-store-route');
    
    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                reloadUrl(currentUrl, responseData.message, 'PositionBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error changing position.', error);  }, 
        {
            headers: { 'X-CSRF-TOKEN': csrfToken, 
                ...loadBearerCompleteHeader() 
            },                     
            body: JSON.stringify({
                'join_events_id': joinEventId,
                'position': joinEventPosition,
                'teamName': formData.get('teamName'),
                'team_id': formData.get('team_id'),
                'eventName': formData.get('eventName'),
                'teamBanner': formData.get('teamBanner'),
                'creator_id': formData.get('creator_id')
            })
        }
    );
}

async function addAward(event) {
    event.preventDefault();
    let formData = new FormData(event.target);
    let teamId = formData.get('teamId');
    let awardId = formData.get('awardId');
    const url = getUrl('event-awards-store-route');

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                reloadUrl(currentUrl, responseData.message, 'AwardsBtn');
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error changing awards.', error);  }, 
        {
            headers: { 'X-CSRF-TOKEN': csrfToken, ...loadBearerCompleteHeader() },    
            body: JSON.stringify({
                'team_id': Number(teamId),
                'award_id': Number(awardId),
                'event_details_id': eventId,
                'award': formData.get('awardName')
            })
        }
    );
}

async function addAchievement(event) {
    event.preventDefault();
    let formData = new FormData(event.target);
    let teamId = formData.get('teamId');
    let title = formData.get('title');
    let description = formData.get('description');
    const url = getUrl("event-achievements-store-route");

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                reloadUrl(currentUrl, responseData.message, 'AchievementsBtn');
            } else {
                toastError(responseData.message)
            }
        },
        function(error) { toastError('Error changing awards.', error);  }, 
        {
            headers: { 'X-CSRF-TOKEN': csrfToken, ...loadBearerCompleteHeader() },    
            body: JSON.stringify({
                'team_id': Number(teamId),
                title,
                description,
                'event_details_id': eventId
            })
        }
    );
}

async function deleteAwardAction() {

    const url = getUrl('event-awards-destroy-route', awardToDeleteId);

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                reloadUrl(currentUrl, responseData.message, 'AwardsBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error changing awards.', error);  }, 
        {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, ...loadBearerCompleteHeader() },  
        }
    );
}

 async function deleteAchievementsAction() {
    const url = getUrl('event-achievements-destroy-route', achievementToDeleteId);

    fetchData(url,
        function(responseData) {
            if (responseData.success) {
                reloadUrl(currentUrl, responseData.message, 'AchievementsBtn');
            } else {
                toastError(responseData.message);
            }
        },
        function(error) { toastError('Error changing achievements.', error);  }, 
        {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, ...loadBearerCompleteHeader() },  
        }
    );
}

function redirectToProfilePage(userId) {
    window.location.href = getUrl('profile-route', userId);
}

function goToCreateScreen() {
    window.location.href = getUrl('create-event-route');
}

function goToEditScreen() {
    window.location.href = getUrl('edit-event-route');
}

function redirectToTeamPage(teamId) {
    window.location.href = getUrl('team-route', teamId);
}