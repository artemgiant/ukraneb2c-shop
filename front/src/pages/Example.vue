<template>
  <div v-tippy="{ content: 'Hasdi!' }">Test</div>

  <tippy content="Hi!">
    <button>Tippy!</button>
  </tippy>


</template>

<script setup>

import { useToast } from "vue-toastification";
import { Tippy } from 'vue-tippy'
// Get toast interface
const toast = useToast();

// Use it!
toast("I'm a toast!");

// or with options
toast.success("My toast content", {
  timeout: 2000
});




import 'tippy.js/dist/tippy.css'
</script>
