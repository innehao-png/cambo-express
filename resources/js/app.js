import "./bootstrap";
import App from "./vue/App.vue";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./vue/routes/routes";
import apiClient from "./vue/api/axios";
import axios from "axios";

window.apiClient = apiClient;
window.axios = axios;

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);
app.mount("#app");
