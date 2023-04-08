<template>
    <div class="item_block">
        <div class="head_section">
            <label>Order ID #{{ item.order_info.order_id }}</label>                    
        </div>
        <div class="body_section">
            <div class="delivery_person_info">
                <div class="img">
                    <Gravatar :email="item.user.email" :size="60" :alt="item.user.user_info.full_name" />
                </div>
                <div class="name">{{ item.user.user_info.full_name }}</div>
            </div>
            <div class="rating_question_block">
                <h4>How was the delivery?</h4>
            </div>
            <div class="rating_input_block">                
                <StarRatingBlock :cur_rating="cur_rating" :len="$store.state.rating_out_off" :editable="true" />
            </div>
            <div class="rating_caption_block">
                <template v-if="sel_rating">
                    {{ cur_rating_caption[sel_rating] }}
                </template>
                <template v-else-if="cur_rating">
                    {{ cur_rating_caption[cur_rating] }}
                </template>
            </div>
        </div>
        <div v-if="pre_loader" class="pre_loader">
            <i class="fa fa-cog fa-spin"></i> Submitting...wait
        </div>
        <div v-else :class="['submit_btn',{active:cur_rating}]" @click="submit_rating">
            <i class="fa fa-check"></i>
            <span>Submit</span>
        </div>
    </div>
</template>
<script>
import StarRatingBlock from '~/components/StarRating'
export default {
    name: 'OrderDeliveryReviewItemBlock',
    props: ['item'],
    components: {
        StarRatingBlock
    },
    data(){
        return {
            pre_loader: false,
            sel_rating: 0,
            cur_rating: 0,
            cur_rating_caption: {
                1: 'Very Bad',
                2: 'Bad',
                3: 'Not expected',
                4: 'Good',
                5: 'Very Fast'
            },
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    methods: {
        select_star_rating(val){
            this.cur_rating = val
        },
        submit_rating: function(){
            if(this.cur_rating>0){
                this.pre_loader = true
                let url = '/api/order-delivery-person-infos/review/store';
                let formData = {
                    order_id: this.item.order_id,
                    rating_points: this.cur_rating,
                    review_comments: this.cur_rating_caption[this.cur_rating]
                }
                this.$axios.post(url, formData, {
                    headers: {
                        'Authorization': 'Bearer ' + this.user_access_token,
                        'Content-Type': 'application/json',
                        'X-XSRF-TOKEN': this.user_access_token
                    }
                }).then( async response => {
                    console.log(response)
                    
                    this.$notify.success({
                        title: 'Success',
                        message: 'Thank you for rating',
                        position: 'bottomRight',
                        transitionIn: 'bounceInRight',
                        transitionOut: 'fadeOutLeft',
                        timeout: 1500
                    })
                                        
                    this.pre_loader = false
                }).catch(e => {
                    this.pre_loader = false
                    this.$toast.error('Rating submit failed!!!', {icon: "error_outline"})
                });
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .item_block{
        display: block;
        margin: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 0 5px #ccc;
    }
    .head_section{
        display: flex;
        height: 30px;
        width: 100%;
        padding: 0 10px;
        background-color: #f7f7f7;
        border-bottom: 1px solid#ddd;
        margin-bottom: 25px;
    }
    .head_section > label{
        align-self: center;        
        font-size: 12px;
        font-weight: bold;
        margin: 0;
        color: #888;
    }
    .head_section > div{
        align-self: center;
        width: 100%;
    }
    .head_section > .action_btn{
        margin-left: auto;
        width: auto;        
        color: #888;
        white-space: nowrap;
        cursor: pointer;
         line-height: 10px;
    }
    .head_section > .action_btn > i{
        font-size: 14px; line-height: 10px;
    }
    .body_section{
        display: block;
        text-align: center;
        margin: 40px 15px 15px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .delivery_person_info .name{
        display: block;
        font-size: 12px;
        margin: 3px 0;
    }
    .delivery_person_info{
        .img{
            display: inline-block;
            width: 60px;
            height: 60px;
            line-height: 60px;
            margin-top: -30px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 50%;
            overflow: hidden;
            :deep(img){
                width: 100%; height: 100%;
                object-fit: cover;
            }
        }
    }
    .rating_question_block  h4{
        margin: 0; color: #333; font-size: 22px;
    }
    .rating_input_block{
        display: inline-block;
        margin-top: 15px
    }
    .rating_caption_block{
        display: block;
        height: 36px; line-height: 36px;
        color: #999;
    }
    .pre_loader{
        display: block;
        margin: 10px 15px;
        text-align: center;
    }
    .submit_btn{
        display: block;
        margin: 10px 15px;
        text-align: center;
        background-color: #ccc;
        color: #fff; padding: 5px 15px;
        border-radius: 5px;
        transition: all 0.4s
    }
    .submit_btn.active{
        cursor: pointer;
        background-color: darkorange;
    }
    .submit_btn.active:hover{
        background-color: #CD0000;
    }
</style>