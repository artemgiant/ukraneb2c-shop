import {defineStore} from "pinia"
import $axios from "@/lib/axios";
import {useProductStore} from "./ProductStore";

export const useProductApiStore = defineStore('productApiStore', () => {


    const getProducts = async () => {

        const productStore = useProductStore();
        const start = (productStore.page - 1) * productStore.length;

        const filtersString =  Object.keys(productStore.filters).map(key => {
         const val = encodeURIComponent(productStore.filters[key]) === 'null'? '': encodeURIComponent(productStore.filters[key]);
            return `${key}=${val}`;
        }).join('&');

      await  $axios.get(`/api/products?start=${start}&length=${productStore.length}&${filtersString}`).then((res)=>{
          productStore.products = [];
            productStore.products.push(...res.data.products)
            productStore.total = res.data.totalLength;
        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }


    const getProduct = async (value) => {
     return   await  $axios.post(`/api/products`,{value:value},{
         headers: {
             'Accept':'application/json',
             'Authorization':'20|xO8xdQ9dGpHRmcgWfyfDIIbsa5hPT8IsDcyhagkn'
         }
     }).then((res)=> res.data
        )  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }

    return {
        getProducts,
        getProduct
    }

})
