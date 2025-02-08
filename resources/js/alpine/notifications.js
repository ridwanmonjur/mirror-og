import { createApp } from 'petite-vue';
import { PageNotificationComponent } from '../custom/notifications';

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        PageNotificationComponent,
    }).mount('#notif-container');

});