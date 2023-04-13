<template>
  <!-- HEADER -->
  <header>

  </header>
  <!-- /HEADER -->
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

const logoUrl = '/img/logos/'+import.meta.env.VITE_APP_SHOP_ALIAS+'/'+import.meta.env.VITE_APP_SHOP_ALIAS+'.png';
// onMounted(async () => {
//   await authStore.getUser();
// });

</script>

<style lang="scss" scoped>
.header-links .dropdown-menu li a {
  color: #181717;
}
.header-links .dropdown-menu li a:hover {
  color: #D10024;
}
</style>
