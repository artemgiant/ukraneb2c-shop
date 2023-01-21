<template>
  <div class="col-md-4 col-xs-6">
    <div class="product">
      <div class="product-img">
        <img :src="this.$storageUrl+'/'+product.image_main.name" alt="">
      </div>
      <div class="product-body">
        <p class="product-category">Категорія</p>
        <h3 class="product-name"><a href="#">{{product.name}}</a></h3>
        <h4 class="product-price">{{product.price}} грн
          <!--          <del class="product-old-price">$990.00</del>-->
        </h4>
        <div class="product-rating">
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
          <i class="fa fa-star"></i>
        </div>
        <div class="product-btns">
          <button
              class="add-to-wishlist"
              v-tippy="{ content: inWishlist?'Прибрати':'Додати до бажань' }"
              v-on:click="addInWishlist()">
            <i class="fa" :class="[inWishlist?'fa-heart':'fa-heart-o']"></i>
          </button>

          <router-link class="quick-view" v-tippy="{ content: 'Перегляд' }" target='_blank' :to="{ name: 'Product',params:{id:product.id}}">
            <i class="fa fa-eye"></i>
          </router-link>

        </div>
      </div>
      <div class="add-to-cart">
        <button class="add-to-cart-btn" v-on:click="addInBasket()"><i class="fa fa-shopping-cart"></i>Додати в кошик</button>
      </div>
    </div>
  </div>
</template>

<script >
import { directive } from 'vue-tippy'

export default {
  name: "Card",
  props:{
    product: {
      type: Object,
      required: true,
      default: () => {},
    },
    inWishlist:{
      type: Boolean,
      required: false,
      default: false,
    }
  },
  emits:[
    'basket-add-product'
  ],
  methods: {
    addInBasket(){
      this.$emit('basket-add-product',this.product)
    },
    addInWishlist(){
      this.$emit('wishlist-add-product',this.product)
    }
  },
  directives: {
    tippy: directive,
  },
}
</script>

<style scoped>

</style>
