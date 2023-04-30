import {defineStore} from "pinia"
import {ref, watch, computed} from "vue";
import $axios from "@/lib/axios";
import { useToast } from "vue-toastification";
import {useProductStore} from "../Product/ProductStore";

export const useCategoryStore = defineStore('categoryStore', ()=>{

    const tree = ref({});


    const getTree = async () => {

        await  $axios.get(`/api/category/tree`).then((resp)=>{
            tree.value = resp.data;
            console.log(resp);
        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }

    return {
        getTree, tree
    }
});
