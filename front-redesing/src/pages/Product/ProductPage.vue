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

  productApiStore.getProduct(route.params.value).then(data => {
    product.value = {...data.products[0],quantity:1}
    // активація слайдеру в товарі це в нас находится front/src/js/main.js
    // setTimeout(()=>{ window.productSlick();},100);
  });

  commentApiStore.getComments();



  const lightbox2 = new PhotoSwipeLightbox({
    gallery: '#' + 'gallery',
    children: 'a',
    showHideAnimationType: 'zoom',
    pswpModule: () => import('photoswipe'),
  });

  lightbox2.init();
})


const sizeImg = (key,type)=>{
   console.log()
  key++
   const img =  document.querySelector(`#thumbnails > div.carousel__viewport > ol > li:nth-child(${key}) > div > img`)
  if((img!==null)) {
    // console.log(img,img.naturalWidth,(type === 'width') ? img.naturalWidth : img.naturalHeight)
    return (type === 'width') ? img.naturalWidth : img.naturalHeight;
  }
}


import 'vue3-carousel/dist/carousel.css'

import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

const currentSlide = ref(1);

const slideTo = (val) => {
  currentSlide.value = val
}


import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';


</script>


<style scoped>

@import "@/assets/css/bootstrap.min.css";
@import "@/assets/css/font-awesome.min.css";
@import "@/assets/css/animate.min.css";
@import "@/assets/css/font-electro.css";
@import "@/assets/css/style.css";
@import "@/assets/css/colors/yellow.css";


.disabled , [disabled]{
  pointer-events: none;
  opacity: 0.5;
}
.opacity-0{
  opacity: 0;
}
.opacity-100{
  opacity: 1;
}
</style>
