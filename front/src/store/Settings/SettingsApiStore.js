import {defineStore} from "pinia"
import $axios from "axios";
import {ref, computed, watch} from "vue"
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification";
import {useProductStore} from "../Product/ProductStore";
import {useProductApiStore} from "../Product/ProductApiStore";
export const useSettingsApiStore = defineStore('settingsApiStore', () => {

    const toast = useToast();
    const page = ref({});
    const route = useRoute();


    const getPage = async () => {

        await  $axios.get(`api/settings/page/${route.params.key}`).then((res)=>{
            page.value = res.data;
            console.log(res.data);

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }



    return {getPage,page }
});
