import {defineStore} from "pinia"
import $axios from "axios";
import {ref,computed} from "vue"
import { useRoute } from 'vue-router'
export const useCommentApiStore = defineStore('commentApiStore', () => {

    const comments = ref({
        avg: 0,
        one: 0,
        two: 0,
        three: 0,
        four: 0,
        five: 0,
    }, {deep: true});

    const stars = ref([]);
    const route = useRoute();

    const comment = ref({
        commentable_type:'App\\Models\\Product\\Product',
        commentable_id:route.params.id,
        message:'',
        stars:'3',
        guest_name:'',
        guest_email:'',
    },{deep:true});


    const getComments = async () => {

        await  $axios.get(`api/comments?commentable_type=App\\Models\\Product\\Product&commentable_id=${route.params.id}`).then((res)=>{

            comments.value = res.data.product.comments;
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
        })  .catch(function (error) {
            console.log(error);
            alert(error.message);
        });
    }




    return {getComments,create,comments,comment,stars}
});
