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
import {onMounted,watch} from "vue";
import {useRoute} from "vue-router";

const route = useRoute()
const settingsApiStore = useSettingsApiStore();

onMounted(()=>{
  settingsApiStore.getPage()
})

// fetch the user information when params change
watch(
    () => route.params.key,
    async key => {
       console.log(key)
      settingsApiStore.getPage()
    }
)


</script>

<style scoped>

</style>
