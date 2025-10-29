import axios from "axios";
// import * as $ from "jquery";
window.axios = axios;
// window.$ = window.jQuery = $;
// window.$ = jquery;
// window.jQuery = jquery;


// import "bootstrap/dist/css/bootstrap.min.css"
// import "bootstrap"

// jquery

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
