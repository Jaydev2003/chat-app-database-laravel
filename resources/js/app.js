import './bootstrap';
import Echo from "laravel-echo";
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();



window.Echo = new Echo({
    broadcaster: "reverb",
});
