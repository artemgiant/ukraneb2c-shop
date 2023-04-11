import axios, {AxiosInstance} from "axios";

const $axios = axios.create({
    baseURL: import.meta.env.VITE_APP_BACKEND_URL,
    params: {
        'shop-alias': import.meta.env.VITE_APP_SHOP_ALIAS
},
});

axios.defaults.withCredentials = true;

export default $axios;
