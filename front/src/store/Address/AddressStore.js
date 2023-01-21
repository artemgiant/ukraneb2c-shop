import {defineStore} from "pinia"
import $axios from "@/lib/axios";

import {ref,computed} from 'vue'

export const useAddressStore = defineStore('addressStore', () => {

    const cities = ref([]);
    const citiesRecipient = ref([]);
    const streets = ref([]);

    const warehouses = ref([]);
    const queryWarehouse = ref('');
    const filteredWarehouses = computed(()=>{
        // если в строке есть только цифры
        if (parseInt(queryWarehouse.value).toString().length === queryWarehouse.value.length)
            return  warehouses.value.filter((w) => parseInt(w.warehouse_number) === parseInt(queryWarehouse.value))
        else
            return warehouses.value.filter(w => w.text.includes(queryWarehouse.value))
    })
    const searchWarehouses = (q) => {
        queryWarehouse.value = q;
    }

    return {
        cities,
        citiesRecipient,
        warehouses,
        streets,
        filteredWarehouses,
        searchWarehouses
    }

})
