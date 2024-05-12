import "./bootstrap";
import { createApp } from "vue";
// import IncrementCounter from "./components/IncrementCounter.vue";
import router from "./router";
import App from "./App.vue";

createApp(App).use(router).mount("#app");
// createApp({})
//     .use(router)
//     .component("IncrementCounter", IncrementCounter)
//     .mount("#app");
