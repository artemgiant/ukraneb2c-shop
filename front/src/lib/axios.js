import axios, {AxiosInstance} from "axios";

const $axios = axios.create({
    baseURL: import.meta.env.VITE_APP_BACKEND_URL
});

axios.defaults.withCredentials = true;


export default $axios;
