import axios from "axios";
window.axios = axios;

// Ensure Laravel handles AJAX requests correctly
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Ensure CSRF token is sent with every request
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken.content;
} else {
    console.error("CSRF token not found. Make sure it's included in the <head> section.");
}

// Import Echo configuration
import "./echo";
