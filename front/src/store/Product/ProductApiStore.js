import {defineStore} from "pinia"
import $axios from "@/lib/axios";
import {useProductStore} from "./ProductStore";

export const useProductApiStore = defineStore('productApiStore', () => {


    const getProducts = async () => {

        const productStore = useProductStore();
        const start = (productStore.page - 1) * productStore.length;

      await  $axios.get(`/api/products?start=${start}&length=${productStore.length}`).then((res)=>{
          productStore.products = [];
            productStore.products.push(...res.data.products)
            productStore.total = res.data.totalLength;
        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }


    const getProduct = async (id) => {

     return   await  $axios.post(`/api/products`,{id:id}).then((res)=> res.data
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
