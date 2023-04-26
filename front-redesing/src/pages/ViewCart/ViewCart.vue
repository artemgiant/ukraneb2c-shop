<template src="./ViewCart.html">
</template>


<script setup>
import {useBasketStore} from "@/store/basketStore"
import {useWishlistStore} from "@/store/wishlistStore"
import { useAuthStore } from "@/store/auth/auth";

const authStore = useAuthStore();

import { inject,onMounted } from 'vue'
const basketStore = useBasketStore();
const wishlistStore = useWishlistStore();

const $storageUrl = inject('storageUrl')


</script>


<style>
.disabled{
  pointer-events: none;
}
</style>
