<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../store/auth/auth";

const authStore = useAuthStore();

const form = ref({
  name: "Admin1",
  email: "admin1@gmail.com",
  phone: "33234",
  password: "12345678",
  password_confirmation: "12345678",
});
</script>
<template>

  <!-- SECTION -->
  <div class="section">
    <!-- container -->
    <div class="container">
      <!-- row -->
      <div class="row">

        <div class="col-md-6 col-md-offset-3">
          <!-- Billing Details -->
          <div class="billing-details">
            <form  @submit.prevent="authStore.handleRegister(form)" >
              <div class="section-title">
                <h3 class="title">Register</h3>
              </div>
              <div class="form-group">
                <input class="input form-control" type="text" v-model="form.name" name="first-name" placeholder="Name">
                <div v-if="authStore.errors.name" class="flex">
                  <span class="text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.name[0]
                    }}</span>
                </div>
              </div>

              <div class="form-group">
                <input class="input form-control" type="email" v-model="form.email" name="first-name" placeholder="Email">

                <div v-if="authStore.errors.email" class="flex">
                  <span class=" invalid-feedback text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.email[0]
                    }}</span>
                </div>
              </div>

              <div class="form-group">
                <input class="input form-control" type="text" v-model="form.phone" name="first-name" placeholder="Phone">

                <div v-if="authStore.errors.phone" class="flex">
                  <span class="text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.phone[0]
                    }}</span>
                </div>

              </div>


              <div class="form-group">
                <input class="input form-control" type="password" v-model="form.password" name="last-name" placeholder="Password">

                <div v-if="authStore.errors.password" class="flex">
                  <span class="text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.password[0]
                    }}</span>
                </div>
              </div>
              <div class="form-group">
                <input class="input form-control" type="password" v-model="form.password_confirmation" name="last-name" placeholder="Confirm password">

                <div v-if="authStore.errors.password_confirmation" class="flex">
                  <span class="text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.password_confirmation[0]
                    }}</span>
                </div>
              </div>

              <div class="form-group">
                <button  type="submit" class="primary-btn order-submit" data-v-5bdfd931="" style="width: 100%;">Увійти</button>
              </div>
              <div class="text-center">
                <router-link to="/login" >
                  Sign In
                </router-link>

              </div>
            </form>

          </div>


          <!-- /Order notes -->
        </div>

      </div>
      <!-- /row -->
    </div>
    <!-- /container -->
  </div>
  <!-- /SECTION -->




</template>
