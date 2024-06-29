import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.VITE_PUSHER_APP_KEY,
  wsHost: process.env.VITE_PUSHER_HOST,
  wsPort: process.env.VITE_PUSHER_PORT,
  wssPort: process.env.VITE_PUSHER_PORT,
  forceTLS: false,
  encrypted: true,
  disableStats: true,
  enabledTransports: ['ws', 'wss'],
});