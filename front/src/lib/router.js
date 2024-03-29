import { createRouter, createWebHistory } from "vue-router";

import Index from "@/pages/Index.vue";
import Example from "@/pages/Example.vue";
import Store from "@/pages/Store/StorePage.vue"
import Basket from "@/pages/Basket/BasketPage.vue"
import Wishlist from "@/pages/Wishlist/WishlistPage.vue"
import Product from "@/pages/Product/ProductPage.vue"
import NotFound from "@/pages/NotFound.vue"
import Info from "@/pages/Info/Info.vue"

const routes = [
  {
    component: Store,
    path: "/",
    name: "index"
  },
  {
    component: Example,
    path: "/example",
    name: "example"
  },
  {
    component: Store,
    path: '/store',
    name: 'Store'
  },
  {
    component: Wishlist,
    path: '/wishlist',
    name: 'Wishlist'
  },
  {
    component: Basket,
    path: '/basket',

  },
  {
    component: Product,
    path: '/product/:id',
    name: 'Product'
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("../pages/Auth/Login.vue"),
  },
  {
    path: "/login-old",
    name: "LoginOld",
    component: () => import("../pages/Auth/LoginOld.vue"),
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("../pages/Auth/Register.vue"),
  },
  {
    path: "/info/:key",
    name: "Info",
    component: () => import("../pages/Info/Info.vue"),
  },

  {
    path: "/account",
    name: "Account",
    component: () => import("../pages/Account/Account.vue"),
  },
  {
    component: NotFound,
    path: '/:pathMatch(.*)*'
  }

];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.afterEach((to) => {
  const baseTitle = "Vue + TypeScript + Vite";

  if (to.name === "index") {
    document.title = baseTitle;
  } else {
    document.title = `${to.meta.title} | ${baseTitle}`;
  }
});

export default router;
