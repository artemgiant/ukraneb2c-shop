<template>
  <!-- HEADER -->
  <header>
    <!-- TOP HEADER -->
    <div id="top-header">
      <div class="container">
        <ul class="header-links pull-left">
          <li><a href="#"><i class="fa fa-phone"></i>+38(067)-978-67-44</a></li>
          <li><a href="#"><i class="fa fa-envelope-o"></i>artemgiant@gmail.com</a></li>
          <li><a href="#"><i class="fa fa-map-marker"></i>Житомир вул. Пемоги 4</a></li>
        </ul>
        <ul class="header-links pull-right">
          <li><a href="#"><i class="fa fa-hryvnia">₴</i>UAH</a></li>
<!--          <li><a href="#"><i class="fa fa-user-o"></i> My Account</a></li>-->
        </ul>
      </div>
    </div>
    <!-- /TOP HEADER -->

    <!-- MAIN HEADER -->
    <div id="header">
      <!-- container -->
      <div class="container">
        <!-- row -->
        <div class="row">
          <!-- LOGO -->
          <div class="col-md-3">
            <div class="header-logo">
              <router-link class="logo" to="/">
                <img src="/img/logo.png" alt="">
              </router-link>
            </div>
          </div>
          <!-- /LOGO -->

          <!-- SEARCH BAR -->
          <div class="col-md-6" style="opacity: 0.5;cursor: not-allowed!important;pointer-events: none">
            <div class="header-search">
              <form>
                <select class="input-select">
                  <option value="0">All Categories</option>
                  <option value="1">Category 01</option>
                  <option value="1">Category 02</option>
                </select>
                <input class="input" placeholder="Search here">
                <button class="search-btn">Search</button>
              </form>
            </div>
          </div>
          <!-- /SEARCH BAR -->

          <!-- ACCOUNT -->
          <div class="col-md-3 clearfix">
            <div class="header-ctn">
              <!-- Wishlist -->
              <div>
                <router-link to="/wishlist">
                  <i class="fa fa-heart-o"></i>
                  <span>Бажання</span>
                  <div class="qty">{{wishlistStore.products.length}}</div>
                </router-link>
              </div>
              <!-- /Wishlist -->

              <!-- Cart -->
              <div class="dropdown ">
                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  <i class="fa fa-shopping-cart"></i>
                  <span>Ваш кошик</span>
                  <div class="qty">{{basketStore.products.length}}</div>
                </a>
                <div class="cart-dropdown">
                  <div class="cart-list">


                    <div v-for="product in basketStore.products" :key="product.id" class="product-widget">
                      <div class="product-img">
                        <img :src="this.$storageUrl+'/'+product.image_main.name" alt="">
                      </div>
                      <div class="product-body">
                        <h3 class="product-name"><a href="#">{{product.name}}</a></h3>
                        <h4 class="product-price"><span class="qty">{{product.quantity}}x</span>{{product.price}} грн</h4>
                      </div>
                      <button class="delete" v-on:click="basketStore.delFromBasket(product)"><i class="fa fa-close"></i></button>
                    </div>

                  </div>
                  <div class="cart-summary">
                    <small>{{basketStore.count}} Товар(ів) вибрано</small>
                    <h5>ПІДСУМОК: ₴ {{basketStore.sum}}.00</h5>
                  </div>
                  <div class="cart-btns">
<!--                    <router-link style="display: none" to="/basket">Переглянути кошик</router-link>-->
                    <router-link to="/basket">Переглянути кошик  <i class="fa fa-arrow-circle-right"></i></router-link>
                  </div>
                </div>
              </div>
              <!-- /Cart -->

              <!-- Menu Toogle -->
              <div class="menu-toggle">
                <a href="#">
                  <i class="fa fa-bars"></i>
                  <span>Menu</span>
                </a>
              </div>
              <!-- /Menu Toogle -->
            </div>
          </div>
          <!-- /ACCOUNT -->
        </div>
        <!-- row -->
      </div>
      <!-- container -->
    </div>
    <!-- /MAIN HEADER -->
  </header>
  <!-- /HEADER -->
</template>


<script setup>
import {useBasketStore} from "@/store/basketStore"
import {useWishlistStore} from "@/store/wishlistStore"
const basketStore = useBasketStore();
const wishlistStore = useWishlistStore();

</script>

<style lang="scss">

</style>
