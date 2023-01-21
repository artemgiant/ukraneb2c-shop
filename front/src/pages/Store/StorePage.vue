<template src="./Store.html">
</template>


<script setup>

import {computed,watch,ref} from 'vue'

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


</script>


