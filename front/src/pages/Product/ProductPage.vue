<template src="./Product.html">
</template>


<script setup>
import { useRoute } from 'vue-router'
import { ref,onMounted} from "vue";
// STORE
import {useProductApiStore} from "@/store/Product/ProductApiStore";
import {useBasketStore} from "@/store/basketStore"


const route = useRoute()
const productApiStore = useProductApiStore();
const basketStore = useBasketStore();
const product = ref({});

onMounted(() => {
  productApiStore.getProduct(route.params.id).then(data => {
    product.value = {...data.products[0],quantity:1}
    // активація слайдеру в товарі це в нас находится front/src/js/main.js
    setTimeout(()=>{ window.productSlick();},100);
  });

})

</script>


<style scoped>
.disabled , [disabled]{
  pointer-events: none;
  opacity: 0.5;
}
</style>
