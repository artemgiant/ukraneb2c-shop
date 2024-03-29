 import {defineStore} from "pinia"
 import {ref, watch,computed} from "vue";
 import  {useProductApiStore} from "./ProductApiStore";

 export const useProductStore = defineStore('productStore', () => {

     const products = ref([]);
     const total  = ref(0);
     const length = ref(10);
     const page  = ref(1);
     // Searching
     const filters = ref({
         category:null,
         brand: null,
         search:null,
         price:{from:null,to:null}
     },'deep')

     watch(page, () => {
         useProductApiStore().getProducts();
     })

     watch(length, () => {
         page.value = 1;
         useProductApiStore().getProducts();
     })

     watch(filters.value, () => {
         page.value = 1;
         useProductApiStore().getProducts();
     },'deep')

     return {
         products,
         total,
         length,
         page,
         filters
     }

 })
