import { createApp } from "vue";
import App from "./App.vue";
import "./style.css";

import { VueQueryPlugin } from "@tanstack/vue-query";
import { createPinia } from "pinia";
import { queryClientConfig } from "./app/providers/query";
import router from "./router";



const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(VueQueryPlugin, queryClientConfig);

app.mount("#app");