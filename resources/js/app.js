import "./bootstrap";

import Alpine from "alpinejs";
import { createApp } from "vue";
import ComingSoon from "./ComingSoon.vue";
import Home from "./Home.vue";

window.Alpine = Alpine;

Alpine.start();

const homeRoot = document.getElementById("home-app");
if (homeRoot) {
    createApp(Home).mount(homeRoot);
}

const comingSoonRoot = document.getElementById("app");
if (comingSoonRoot) {
    createApp(ComingSoon).mount(comingSoonRoot);
}
