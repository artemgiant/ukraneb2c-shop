<template src="./Basket.html"></template>

<!--<script src="./Loader.js"></script>-->

<script setup>
import {computed, ref, inject,watch, onMounted} from 'vue'
import $axios from "@/lib/axios";
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification";
import * as Yup from 'yup';
import {phoneMask} from "@/lib/phone-mask";
import _ from 'lodash';

import VueMultiselect from 'vue-multiselect'
import { Form, Field, ErrorMessage } from 'vee-validate';

// STORE
import {useBasketStore} from "@/store/basketStore"
import {useAddressApiStore} from "@/store/Address/AddressApiStore";
import {useAddressStore} from "@/store/Address/AddressStore";
import { useAuthStore } from "@/store/auth/auth";
import { useSettingsApiStore } from "@/store/Settings/SettingsApiStore";

const $storageUrl = inject('storageUrl')
const toast =  useToast();

// Components
const basketStore = useBasketStore();
const addressApiStore = useAddressApiStore();
const addressStore = useAddressStore();
const router = useRouter();
const authStore = useAuthStore();
const settingsApiStore = useSettingsApiStore();
const isAgree = ref({
  order: false,
  conditions:false,
});


const form = ref({
  recipient: {
    phone: null,
    email: null,
    first_name: null,
    last_name: null,
    middle_name: null,
    city: {  },
    street: { },
    house: null,
    flat: null,
  },
  address_delivery: {
    delivery_type: null,
    city: { },
    warehouse: { },
    street: null,
    house: null,
    flat: null
  },
  products: basketStore.products,
  payment_type: null,
  comment: null,
  promo_code: null,

  delivery_cost: '-',
  discount_delivery:null,
  sum_to_pay: '-',
  discount: null,
  order_discount:{},
  total_sum_product:computed(() => {
    return basketStore.products.reduce((accumulator, object) => {
      return accumulator + (parseInt(object.price) * object.quantity) ;
    }, 0)
  }),
  total_weight_kg:computed(() => {
    return basketStore.products.reduce((accumulator, object) => {
      return accumulator + (parseFloat(object.weight_kg) * object.quantity) ;
    }, 0)
  })
}, {deep: true})

const formErrors = ref({})


// глубокое слежения
watch(() => _.cloneDeep(form.value.address_delivery.delivery_type), (currentValue, oldValue) => {
  addressStore.cities = [];
  addressStore.warehouses = [];

  form.value.address_delivery = {
    delivery_type: currentValue,
    city: null,
    warehouse: null,
    street: null,
    house: null,
    flat: null,
  }

// перерахунок вартості замовлення
  prices();

});


const searchRecipientCity = (query) => {
  if (query.length >= 3)
    addressApiStore.getCity(query, 'meest_courier', 'address_recipient');
}

const selectedRecipientCity = (object)=> {
    addressApiStore.getStreet('meest_courier',object.id,'address_recipient');
}

const searchDeliveryCity = (query)=> {
  if(query.length>=3)
   addressApiStore.getCity(query,form.value.address_delivery.delivery_type,'address_delivery');
}

const selectedDeliveryCity = (object)=> {
  addressApiStore.getStreet('meest_courier',object.id,'address_delivery');
}


const asyncWarehouses = (object) => {
  form.value.warehouse = {}
  addressApiStore.getWarehouses(form.value.address_delivery.delivery_type, object.id);
}

const queryWarehouse = ref('');

const searchWarehouses = (q) => {
  queryWarehouse.value = q;
  addressStore.warehouse_number
// если в строке есть только цифры
  if (parseInt(q).toString().length === q.length) {
    addressStore.warehouses = addressStore.warehouses.filter((w) => parseInt(w.warehouse_number) === parseInt(q))
  }
}

const filteredWarehouses = computed(() => {
  return addressStore.warehouses;
})

const schema = Yup.object().shape({
  recipient:
          Yup.object().shape({
            first_name: Yup.string().required().min(1).nullable(),
            last_name: Yup.string().required().min(1).nullable(),
            middle_name: Yup.string().required().min(1).nullable(),
            email: Yup.string().email().required().nullable(),
            city: Yup.object().required().nullable(),
            street: Yup.object().required().nullable(),
            flat: Yup.string().required().nullable(),
            house: Yup.string().required().nullable(),
          }).strict(),
  address_delivery: Yup.object().shape({
    delivery_type: Yup.string().required().nullable(),
  })
      .strict(),
});
const elHeader = ref(null);

async function onSubmit(values, { resetForm }) {
  // display form values on success
  // alert('SUCCESS!! :-)\n\n' + JSON.stringify(values, null, 4));

  const url = `api/orders/create`;
  await $axios.post(url, form.value).then((res) => {

    for (const [key, product] of Object.entries(basketStore.products)) {
      basketStore.delFromBasket(product)
    }


    window.location.href = '/info/delivery_and_payment';



  }).catch( (error) => {

    if('errors' in error.response.data){
      formErrors.value = error.response.data.errors;
      elHeader.value.scrollIntoView({behavior: "smooth"})
    } else{
      console.log(error);
      alert(error);

    }
  });
}

async function prices() {
  // display form values on success
  // alert('SUCCESS!! :-)\n\n' + JSON.stringify(values, null, 4));

  const url = `api/orders/prices`;
  await $axios.post(url, form.value).then((res) => {

    form.value.sum_to_pay = res.data.data.sum_to_pay
    form.value.delivery_cost = res.data.data.delivery_cost
    form.value.standard_delivery_cost = res.data.data.standard_delivery_cost
    form.value.discount_delivery = res.data.data.discount_delivery
    form.value.discount = res.data.data.discount
    form.value.order_discount = res.data.data.order_discount
    console.log(res.data)

    alerts(res.data.message)
  }).catch((error) => {

    if ('errors' in error.response.data) {
      formErrors.value = error.response.data.errors;
      elHeader.value.scrollIntoView({behavior: "smooth"})
    } else {
      console.log(error);
      alert(error);

    }
  });
}

function alerts(message) {
  switch (message['alert-type']) {
    case 'error':
      toast.error(message.message)
      return;
    case 'success':
      toast.success(message.message)
      return;
  }
}


// DIRECTIVES
const vPhoneMask = {
  beforeMount: phoneMask

}



onMounted(()=>{
  settingsApiStore.getDeliveryPrices()
})



</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style src="./Basket.css" scoped></style>

<style >
.invalid-feedback{
  position: relative;
  margin: 0;
}
.invalid-feedback span{
  position: absolute;
}

.multiselect__content-wrapper * {position: relative; z-index: 6;}

.multiselect--active {
  z-index: 1000;
}
.multiselect__tags{
  border-radius: 1.571em;
  padding-left: 19px;

}
#ajax,#warehouse{
  border-radius: 0;
  border: 0;
  padding: 0 0!important;
}
</style>

