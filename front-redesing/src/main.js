// window._ = require('lodash');

import { createApp,markRaw } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "@/lib/router";
import Toast from "vue-toastification";

// Import the CSS or use your own!
import "vue-toastification/dist/index.css";


const optionsToast = {
    position: "bottom-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    maxToasts: 3,
    icon: true,
    rtl: false
};
const pinia = createPinia();
pinia.use(({ store }) => {
    store.router = markRaw(router);
});



const app = createApp(App)
    .use(router)
    .use(Toast, optionsToast)
    .use(pinia)
;

// Config
app.config.productionTip = true;
app.provide('backendUrl', import.meta.env.VITE_APP_BACKEND_UR)
app.provide('storageUrl', import.meta.env.VITE_APP_STORAGE_URL)
app.provide('appName', import.meta.env.VITE_APP_NAME)

app.mount("#app");


