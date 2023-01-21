import {defineStore} from "pinia"
import {ref, watch, computed} from "vue";
import { useToast } from "vue-toastification";

export const useBasketStore = defineStore('basketStore', ()=>{
    const products = ref([]);
// Get toast interface
    const toast = useToast();




    const productsData = localStorage.getItem("basket:products");

    if (productsData && Object.keys(JSON.parse(productsData)).length)
        products.value = JSON.parse(productsData);

    watch(products, () => {
        console.log('change products')

        localStorage.setItem("basket:products", JSON.stringify(products.value));
    },{ deep: true })


    const delFromBasket = (product) => {
        products.value =  products.value.filter(p => p.id !== product.id)
    }

    const count = computed(() => {
        return products.value.reduce((accumulator, object) => {
            return accumulator + object.quantity;
        }, 0)
    })

    const sum = computed(() => {
        return products.value.reduce((accumulator, object) => {
            return accumulator + (parseInt(object.price) * object.quantity) ;
        }, 0)
    })


    const addToBasket = (product) => {

        // or with options
        toast.success("Товар додан в корзину!");

        const exits =  products.value.find(p => p.id === product.id)

        if (exits) {
            products.value.find(p => {
                if(p.id === product.id){
                    console.log(p)
                    p.quantity++;
                }
            })
        } else {
            product.quantity = 1;
            products.value.push(product)
        }
    };

    return {
        products,
        delFromBasket,
        addToBasket,
        count,
        toast,
        sum}
})
