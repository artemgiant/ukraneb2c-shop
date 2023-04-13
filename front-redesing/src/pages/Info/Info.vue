<template>

  <!-- SECTION -->
  <div class="section">
    <!-- container -->
    <div class="container">
      <div class="row">
          <div v-html="settingsApiStore.page.value"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {useSettingsApiStore} from '@/store/Settings/SettingsApiStore'
import {onMounted} from "vue";

const settingsApiStore = useSettingsApiStore();

onMounted(()=>{
  settingsApiStore.getPage()
})

</script>

<style scoped>

</style>
