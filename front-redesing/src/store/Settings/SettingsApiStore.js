import {defineStore} from "pinia"
import $axios from "@/lib/axios";
import {ref, computed, watch} from "vue"
import { useRoute } from 'vue-router'
export const useSettingsApiStore = defineStore('settingsApiStore', () => {

    const page = ref({});
    const basketItems = ref({});
    const deliveryPrices = ref([]);
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
    const getBasket = async () => {

        await  $axios.get(`api/settings/basket`).then((res)=>{
            basketItems.value = res.data;
            console.log(res.data);

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }

    const getDeliveryPrices = async  () => {

        await  $axios.get(`api/settings/delivery-prices`).then((res)=>{
            deliveryPrices.value = res.data;
            console.log(res.data);

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }


    return {getPage,page,getDeliveryPrices,deliveryPrices,basketItems,getBasket }
});
