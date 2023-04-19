<template>
  <body class="left-sidebar ">
  <div id="page" class="hfeed site">
    <Header/>
    <RouterView/>
    <Footer/>
  </div>
  </body>
</template>

<script setup>
  import Header from "@/components/Header.vue";
  import Footer from "@/components/Footer.vue";
</script>
