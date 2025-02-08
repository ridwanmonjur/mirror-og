<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/notifications.js'])
    @include('__CommonPartials.HeadIcon')

</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <br><br>
    <div     
        @vue:mounted="init"
        id="notif-container" 
        v-scope="PageNotificationComponent()"
        style="width: min(1000px, 95%); "
    >
        <h4 class="ms-4">
            All Notifications
        </h4>
        <div class="tabs d-block row ms-4 px-0 " >
            <button id="SocialBtn" class="tab-button d-inline  col-12 col-lg-3 py-2   outer-tab"
                onclick="showTab(event, 'Social', 'outer-tab')"
                v-bind:class="{ 'tab-button-active': currentTab == 'social' }" 
                v-on:click="changeNotificationTab('social')"   
            >Social
            <span v-if="true" class="me-2">
                <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                    <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['social']"></circle>
                </svg>
            </span>
            </button>
            <button id="TeamsBtn" class="tab-button py-2 col-12 col-lg-3  d-inline  outer-tab"
                onclick="showTab(event, 'Teams', 'outer-tab')"
                v-on:click="changeNotificationTab('teams')"  
                v-bind:class="{ 'tab-button-active': currentTab == 'teams' }" 
            >
                Teams
                <span v-if="true" class="me-2">
                    <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                        <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['teams']"></circle>
                    </svg>
                </span>
            </button>
            <button id="EventBtn" class="tab-button py-2  col-12 col-lg-3 d-inline  outer-tab"
                onclick="showTab(event, 'Event', 'outer-tab')"
                v-on:click="changeNotificationTab('event')" 
                v-bind:class="{ 'tab-button-active': currentTab == 'event' }" 
            >
                Event
                <span v-if="true" class="me-2">
                    <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                        <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['event']"></circle>
                    </svg>
                </span>
            </button>
        </div>
        <div class="ms-4">
            <div class="notification-list">
                <template v-for="notification2 in notificationList" :key="notification2.id">
                    <div class="notification-item d-flex align-items-center cursor-pointer p-3 border-0"
                        v-on:click="markNotificationRead(notification2.id, notification2.link)"
                    >
                        <div class="notification-icon me-3">
                            <span v-if="!notification2.isRead" class="me-2">
                                <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                                    <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors[currentTab]"></circle>
                                </svg>
                            </span>
                            <template v-if="notification2.iconType" >
                                <span style="margin-right: 6px;" v-html="getIconSvg(notification2.iconType)"></span>
                            </template>
                            <template v-else-if="notification2.imageSrc">
                                <img v-bind:src="notification2.imageSrc" class="rounded-circle object-fit-cover" width="30"
                                    height="30" alt="Profile">
                            </template>
                        </div>
                        <div class="notification-content flex-grow-1">
                            <div v-html="notification2.html"></div>
                            <small class="text-muted" v-text="formatTime(notification2.createdAt)"></small>
                        </div>
                    </div>
                </template> 
            </div>
        </div>
    </div>

</body>
