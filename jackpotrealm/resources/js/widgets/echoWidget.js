import Echo from "laravel-echo";

window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: false,
    disableStats: true,
    wsHost: process.env.MIX_WEBSOCKET_HOST,
    wsPort: process.env.MIX_WEBSOCKET_PORT,
    enabledTransports: ['ws']
});
