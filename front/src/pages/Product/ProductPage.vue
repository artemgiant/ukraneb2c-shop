<template src="./Product.html">
</template>


<script setup>
import { useRoute } from 'vue-router'
import { ref,onMounted, inject} from "vue";
import {useAuthStore} from "../../store/auth/auth";
// STORE
import {useProductApiStore} from "@/store/Product/ProductApiStore";
import {useBasketStore} from "@/store/basketStore"
import {useCommentApiStore} from "@/store/Comment/CommentApiStore"


const route = useRoute()
const productApiStore = useProductApiStore();
const commentApiStore = useCommentApiStore();
const basketStore = useBasketStore();
const product = ref({});
const $storageUrl = inject('storageUrl')
const authStore = useAuthStore();


onMounted(() => {
  productApiStore.getProduct(route.params.id).then(data => {
    product.value = {...data.products[0],quantity:1}
    // активація слайдеру в товарі це в нас находится front/src/js/main.js
    setTimeout(()=>{ window.productSlick();},100);
  });

  commentApiStore.getComments();
})

</script>


<style scoped>
.disabled , [disabled]{
  pointer-events: none;
  opacity: 0.5;
}
</style>
