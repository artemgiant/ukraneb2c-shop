<template>

  <li class="product" :class="classProduct">
    <div class="product-outer">
      <div class="product-inner">
        <span class="loop-product-categories"><a href="product-category.html" rel="tag"></a></span>
        <router-link class="quick-view"
                     :to="{ name: 'Product',params:{value:product.slug?product.slug:product.id}}">
<!--          <h3> {{ product.name }}</h3>-->
          <div class="product-thumbnail">
            <img :src="$storageUrl+'/'+product.image_main.name" :alt="$storageUrl+'/'+product.image_main.name">
          </div>
        </router-link>

        <div  style="font-size: 1em;font-weight: bold; display: flex; justify-content: space-between;  width: 100%;">
         <h3> {{ product.name }}</h3>
        </div>

        <div style=" display: flex;justify-content: space-between; width: 100%;">

          <span class="price">
          <span class="electro-price">
             <span class="amount">{{ product.price }} грн</span>
               </span>
             </span>

          <button type="button" class="btn btn-warning btn-add-to-cart" v-on:click="addInBasket()"
                  style="padding: 8px 18px;">
            Купити
          </button>
        </div><!-- /.price-add-to-cart -->


<!--        <div class="hover-area">-->
<!--          <div class="action-buttons">-->

<!--            <a rel="nofollow" class="add_to_wishlist"  v-on:click="addInWishlist()">Додати до бажань</a>-->
<!--          </div>-->
<!--          </div>-->


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
    },
    index:{type:Number},
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
  computed: {
    classProduct(){
      if (this.index === 0)
        return 'first'
      else if (Number.isInteger(this.index  / 4))
        return 'first'
      else if (Number.isInteger((this.index + 1) / 4))
        return 'last'
    }
  }
}
</script>

<style scoped>
.btn-add-to-cart{
  display: none;
}
.btn-add-to-cart:hover{
background-color: #fed700;
}

li.product:hover .btn-add-to-cart{
  display: block;
}

ul.products li.product::after{
  border: none;
}

li.product a{
  color:#333e48;
}

</style>
