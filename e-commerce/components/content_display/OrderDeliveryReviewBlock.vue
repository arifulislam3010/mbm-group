<template>
    <div :class="['order_delivery_review_block',{mobile_device:$device.isMobile}]">
        <div class="black_overlay" @click="$parent.delivery_review_block_show=false"></div>
        <div class="white_overlay">
            <div class="content_block">                
                <carousel
                    :paginationEnabled="false"                
                    :perPage="1"                
                    :minSwipeDistance="16"
                    :loop="true">
                    <slide v-for="(item,index) in $store.state.order_delivery_review_pending_info" :key="index">
                        <OrderDeliveryReviewItemBlock :item="item" />
                    </slide>
                </carousel>
            </div>
        </div>
    </div>
</template>
<script>
import OrderDeliveryReviewItemBlock from './OrderDeliveryReviewItem'
export default {
    name: 'OrderDeliveryReviewBlock',
    components: {
        OrderDeliveryReviewItemBlock
    }
}
</script>
<style scoped>
    .white_overlay{
        position: fixed;
        width: 300px;        
        left: calc(50% - 150px);        
        top: 25%;        
        z-index: 10000;
    }
    .mobile_device .white_overlay{
        width: 80%;
        left: 10%;
        top: 40%;
    }
    .content_block{
        display: inline-block;
        background-color: #ddd;
        width: 100%; height: 100%;
        border: 1px solid #ddd;
        /* border-radius: 5px 5px 0 0; */
        box-shadow: 0 0 15px #000;
        /* overflow: hidden; */
    }
    .mobile_device .content_block{
        padding-bottom: 25px;
    }
</style>