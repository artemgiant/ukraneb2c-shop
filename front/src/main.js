// window._ = require('lodash');

import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "@/lib/router";
import Toast from "vue-toastification";

// Import the CSS or use your own!
import "vue-toastification/dist/index.css";

import "@/style/normalize.css";
import "@/style/global.scss";

import "@/style/lib/bootstrap.min.css";
import "@/style/lib/nouislider.min.css";
import "@/style/lib/slick.css";
import "@/style/lib/slick-theme.css";
import "@/style/lib/font-awesome.min.css";
import "@/style/style.css";

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

const app = createApp(App)
    .use(router)
    .use(Toast, optionsToast)
    .use(createPinia())
;

// Config
app.config.productionTip = true;
app.config.globalProperties.$backendUrl = import.meta.env.VITE_APP_BACKEND_URL;
app.config.globalProperties.$storageUrl= import.meta.env.VITE_APP_STORAGE_URL;
app.config.globalProperties.$appName = import.meta.env.VITE_APP_NAME;

app.mount("#app");


