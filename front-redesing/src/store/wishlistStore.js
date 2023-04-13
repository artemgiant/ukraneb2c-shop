import {defineStore} from "pinia"
import {ref, watch, computed} from "vue";
import { useToast } from "vue-toastification";

export const useWishlistStore = defineStore('wishlistStore', ()=>{
    const products = ref([]);
// Get toast interface
    const toast = useToast();

    const productsData = localStorage.getItem("wishlist:products");

    if (productsData && Object.keys(JSON.parse(productsData)).length)
        products.value = JSON.parse(productsData);


    watch(products, () => {
        console.log('change products')

        localStorage.setItem("wishlist:products", JSON.stringify(products.value));
    })

    const delFromWishlist = (product) => {
        products.value =  products.value.filter(p => p.id !== product.id)
    }

    const addToWishlist = (product) => {
        const exits =  products.value.find(p => p.id === product.id)
        if (exits) {
            delFromWishlist(product)
        } else {
            product.quantity = 1;
            products.value.push(product)
            toast.success("Товар додан до списку бажань!");
        }
    };

    const has = (product) => {
        return (products.value.find(p => parseInt(p.id) === parseInt(product.id)) !== undefined)
    }

    const count = computed(() => {
        return products.value.reduce((accumulator, object) => {
            return accumulator + object.quantity;
        }, 0)
    })

    return {
        products,
        delFromWishlist,
        addToWishlist,
        count,
        toast,
        has
        }
})
