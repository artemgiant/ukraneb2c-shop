<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/store/auth/auth";

const authStore = useAuthStore();

const form = ref({
  email: "admin2@gmail.com",
  password: "adminadmin",
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
            <form  @submit.prevent="authStore.handleLogin(form)" >
              <div class="section-title">
                <h3 class="title">Login</h3>
              </div>
              <div class="form-group">
                <input class="input  form-control" type="email" v-model="form.email" name="first-name" placeholder="Email">

                <div v-if="authStore.errors.email" class="flex">
                  <span class="text-red-400 text-sm m-2 p-2">{{
                      authStore.errors.email[0]
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
                <button  type="submit" class="primary-btn order-submit" data-v-5bdfd931="" style="width: 100%;">Увійти</button>
              </div>
              <div class="text-center">
                <p>Not a member? <router-link to="/register">Register</router-link></p>
                <button type="button" class="btn btn-link btn-floating mx-1">
                  <i class="fab fa-facebook-f"></i>
                </button>

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
