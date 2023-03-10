import {defineStore} from "pinia"
import $axios from "axios";
import {useAddressStore} from "./AddressStore";

export const useAddressApiStore = defineStore('addressApiStore', () => {


    const getCity = async (q, deliveryType, mode) => {
        const addressStore = useAddressStore();
        const url = `api/addresses/search/city`;
        const data = {
            q: q,
            delivery_type: deliveryType
        };

        if (mode === 'address_recipient')
            addressStore.citiesRecipient = [];
        else if (mode === 'address_delivery')
            addressStore.cities = [];

        await $axios.post(url, data).then((res) => {
            if (Array.isArray(res.data.results))
                if (mode === 'address_recipient')
                    addressStore.citiesRecipient = res.data.results
                else if (mode === 'address_delivery')
                    addressStore.cities = res.data.results

        }).catch(function (error) {
            console.log(error);
            alert(error.message);
        });

    }


    const getStreet = async (deliveryType,cityId,mode)  => {
        const addressStore = useAddressStore();
        const url = `api/addresses/search/street`;
        const data = {
            q: null,
            delivery_type:'meest_courier',
            id:cityId,
        };

         if (mode === 'address_recipient')
             addressStore.streetsRecipient = []
         else if(mode === 'address_delivery')
             addressStore.streets = []

        await $axios.post(url, data).then((res) => {

            console.log(res.data.results)
            if (Array.isArray(res.data.results))
                if (mode === 'address_recipient')
                    addressStore.streetsRecipient = res.data.results
                else if(mode === 'address_delivery')
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
        addressStore.warehouses = [];

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
