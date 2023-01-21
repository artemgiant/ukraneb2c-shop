import {defineStore} from "pinia"
import $axios from "@/lib/axios";
import {useAddressStore} from "./AddressStore";

export const useAddressApiStore = defineStore('addressApiStore', () => {


    const getCity = async (q, deliveryType)  => {
        const addressStore = useAddressStore();
         const url = `api/addresses/search/city`;
         const data = {
             q:q,
             delivery_type:deliveryType
         };

        await $axios.post(url, data).then((res) => {
            if (Array.isArray(res.data.results))
                if (deliveryType === 'meest_courier')
                    addressStore.citiesRecipient = res.data.results
                else
                    addressStore.cities = res.data.results

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });

    }


    const getStreet = async (deliveryType,cityId)  => {
        const addressStore = useAddressStore();
        const url = `api/addresses/search/street`;
        const data = {
            q: null,
            delivery_type:'meest_courier',
            id:cityId,
        };

        await $axios.post(url, data).then((res) => {

            console.log(res.data.results)
            if (Array.isArray(res.data.results))
                addressStore.streets = res.data.results

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });

    }


    const getWarehouses = async (deliveryType,cityId)  => {
        const addressStore = useAddressStore();
        const url = `api/addresses/search/warehouse`;
        const data = {
            q: '',
            delivery_type:deliveryType,
            id:cityId,
        };

        await $axios.post(url, data).then((res) => {

            console.log(res.data.results)
            if (Array.isArray(res.data.results))
                addressStore.warehouses = res.data.results

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });

    }


    return {
        getCity,getWarehouses,getStreet
    }

})
