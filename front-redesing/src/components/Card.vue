<template>

  <li class="product" >
    <div class="product-outer">
      <div class="product-inner">
        <span class="loop-product-categories"><a href="product-category.html" rel="tag">Smartphones</a></span>
        <router-link class="quick-view"  target='_blank'
                     :to="{ name: 'Product',params:{id:product.id}}">
          <h3> {{ product.name }}</h3>
          <div class="product-thumbnail">
            <img :src="$storageUrl+'/'+product.image_main.name" :alt="$storageUrl+'/'+product.image_main.name">
          </div>

<!--          <div class="product-rating">-->
<!--            <div title="Rated 4 out of 5" class="star-rating"><span style="width:80%"><strong class="rating">4</strong> out of 5</span></div> (3)-->
<!--          </div>-->

<!--          <div class="product-short-description">-->
<!--            <ul>-->
<!--              <li><span class="a-list-item">Intel Core i5 processors (13-inch model)</span></li>-->
<!--              <li><span class="a-list-item">Intel Iris Graphics 6100 (13-inch model)</span></li>-->
<!--              <li><span class="a-list-item">Flash storage</span></li>-->
<!--              <li><span class="a-list-item">Up to 10 hours of battery life2 (13-inch model)</span></li>-->
<!--              <li><span class="a-list-item">Force Touch trackpad (13-inch model)</span></li>-->
<!--            </ul>-->
<!--          </div>-->

<!--          <div class="product-sku">SKU: 5487FB8/15</div>-->
        </router-link>
        <div class="price-add-to-cart">
                                                        <span class="price">
                                                            <span class="electro-price">
                                                              <span class="amount">{{product.price}} грн</span>
                                                            </span>
                                                        </span>

          <a  rel="nofollow" class="button add_to_cart_button" v-on:click="addInBasket()">Додати в кошик</a>
        </div><!-- /.price-add-to-cart -->

        <div class="hover-area">
          <div class="action-buttons">

            <a rel="nofollow" class="add_to_wishlist"  v-on:click="addInWishlist()">Додати до бажань</a>

<!--            <button-->
<!--                class="add-to-wishlist"-->
<!--                v-tippy="{ content: inWishlist?'Прибрати':'Додати до бажань' }"-->
<!--                v-on:click="addInWishlist()">-->
<!--              <i class="fa" :class="[inWishlist?'fa-heart':'fa-heart-o']"></i>-->
<!--            </button>-->


<!--            <a href="#" class="add-to-compare-link">Compare</a>-->
          </div>
        </div>

      </div><!-- /.product-inner -->
    </div><!-- /.product-outer -->
  </li>

</template>


<script>
import {directive} from 'vue-tippy'
import {inject} from 'vue'

export default {
  name: "Card",
  data() {
    return {
      $storageUrl: inject('storageUrl')
    }
  },
  props: {
    product: {
      type: Object,
      required: true,
      default: () => {
      },
    },
    inWishlist: {
      type: Boolean,
      required: false,
      default: false,
    }
  },
  emits: [
    'basket-add-product'
  ],
  methods: {
    addInBasket() {
      this.$emit('basket-add-product', this.product)
    },
    addInWishlist() {
      this.$emit('wishlist-add-product', this.product)
    }
  },
  directives: {
    tippy: directive,
  },
}
</script>

<style scoped>

</style>
