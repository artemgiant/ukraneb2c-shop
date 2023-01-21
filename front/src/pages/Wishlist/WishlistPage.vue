<template src="./Wishlist.html">

</template>

<script setup>
import {useBasketStore} from "@/store/basketStore"
import {useWishlistStore} from "@/store/wishlistStore"

// Components
import Card from "@/components/Card.vue";

const basketStore     = useBasketStore();
const wishlistStore   = useWishlistStore();

</script>

<style scoped>

</style>
