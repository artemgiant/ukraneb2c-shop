<template src="./Store.html">
</template>

<script setup>

import {computed,watch,onMounted,ref} from 'vue'

import {useProductApiStore} from "@/store/Product/ProductApiStore"
import {useProductStore} from "@/store/Product/ProductStore"
import {useBasketStore} from "@/store/basketStore"
import {useWishlistStore} from "@/store/wishlistStore"


// Components
import Card from "@/components/Card.vue";

const productApiStore = useProductApiStore();
const productStore    = useProductStore();
const basketStore     = useBasketStore();
const wishlistStore   = useWishlistStore();
const products = ref({})

productApiStore.getProducts()


onMounted(() => {
  window.owlCarouselSlider();
  console.log('on mounted')

})



// Pagination
const startIndex = computed(() => (productStore.page - 1) * productStore.length)
const endIndex = computed(() => productStore.page * productStore.length)
const maxPage = computed(() => {
  return (productStore.total / productStore.length) > 10 ? 10 : Math.ceil(productStore.total / productStore.length);
})
const previousPage = computed(() => productStore.page > 0 ? productStore.page - 1 : 1)
const nextPage = computed(() => {
  const maxPage = (productStore.total / productStore.length) > 10 ? 10 :  Math.ceil(productStore.total / productStore.length);
  return maxPage > productStore.page ? productStore.page + 1 : maxPage
})




import 'vue3-carousel/dist/carousel.css'

import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'


</script >


<style lang="scss" >
li.product .product-thumbnail img{
  max-height: 197px;
  max-width: 262px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translateX(-50%) translateY(-50%);
}

li.product:hover .product-inner{
  position: relative;
}


@import "@/assets/css/bootstrap.min.css";
@import "@/assets/css/font-awesome.min.css";
@import "@/assets/css/animate.min.css";
@import "@/assets/css/font-electro.css";
@import "@/assets/css/style.css";
@import "@/assets/css/colors/yellow.css";
</style>

<!--<script >-->

<!--</script>-->
