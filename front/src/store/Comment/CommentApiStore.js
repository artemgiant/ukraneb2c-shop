import {defineStore} from "pinia"
import $axios from "axios";
import {ref, computed, watch} from "vue"
import { useRoute } from 'vue-router'
import { useToast } from "vue-toastification";
import {useProductStore} from "../Product/ProductStore";
import {useProductApiStore} from "../Product/ProductApiStore";
export const useCommentApiStore = defineStore('commentApiStore', () => {

    const toast = useToast();
    const stars = ref({});
    const route = useRoute();
    const pagination = ref({
        total: 4,
        length: 3,
        page: 1,
        startIndex: computed(() => (pagination.value.page - 1) * pagination.value.length),
        endIndex: computed(() => pagination.value.page * pagination.value.length),
        maxPage: computed(() => {
            return (pagination.value.total / pagination.value.length) > 10 ? 10 : Math.ceil(pagination.value.total / pagination.value.length);
        }),
        previousPage: computed(() => pagination.value.page > 0 ? pagination.value.page - 1 : 1),
        nextPage: computed(() => {
            const maxPage = (pagination.value.total / pagination.value.length) > 10 ? 10 : Math.ceil(pagination.value.total / pagination.value.length);
            return maxPage > pagination.value.page ? pagination.value.page + 1 : maxPage
        })
    }, {deep: true})
    const comments = ref({ }, {deep: true});
    const comment = ref({
        commentable_type:'App\\Models\\Product\\Product',
        commentable_id:route.params.id,
        message:'',
        stars:'3',
        guest_name:'',
        guest_email:'',
    },{deep:true});


    watch(pagination.value,()=>{
        getComments();
    });

    const getComments = async () => {

        const start = (pagination.value.page - 1) * pagination.value.length;

        await  $axios.get(`api/comments?commentable_type=App\\Models\\Product\\Product&commentable_id=${route.params.id}&start=${start}&length=${pagination.value.length}`).then((res)=>{

            comments.value = res.data.comments;
            comments.value.total = res.data.totalLength;
            stars.value = res.data.stars;

        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }

    const create = async () => {
        await  $axios.post(`api/comments`,comment.value).then((res)=>{
            getComments();

            comment.value = {
                commentable_type:'App\\Models\\Product\\Product',
                commentable_id:route.params.id,
                message:'',
                stars:'3',
                guest_name:'',
                guest_email:'',
            }

            // or with options
            toast.success("Комментій успішно додан!");
        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }




    return {getComments,create,comments,comment,stars,pagination }
});
