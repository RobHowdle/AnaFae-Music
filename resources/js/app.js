import "./bootstrap";

import Alpine from "alpinejs";
import { createApp } from "vue";
import ComingSoon from "./ComingSoon.vue";

window.Alpine = Alpine;

Alpine.start();

const root = document.getElementById("app");

if (root) {
    createApp(ComingSoon).mount(root);
}
