<template>
    <div class="content_card_view">
        <template v-if="content_loader">
            <div class="form_block">
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </template>
        <template v-else-if="data.length>0">
            <div v-for="(item,index) in data" :key="index" class="list_item">
                <div class="head_info">
                    <div class="order_info">
                        <div class="order_id">ID #{{ item.order_id }}</div>
                        <div class="ordered_at"><small>Placed on {{ item.created_at }}</small></div>
                    </div>
                    <div class="action_btn">
                        <span @click="$parent.view_details_order(item)"><i class="fa fa-external-link"></i> View Detail</span>
                        <div class="total_items"><small>Total {{ item.order_items_info.length }}  item{{ item.order_items_info.length>1?'s':'' }}</small></div>
                    </div>
                </div>
                <div class="item_info">
                    <swiper class="swiper" :options="swiperOption">
                        <swiper-slide v-for="(product,pi) in item.order_items_info" :key="pi">
                            <div class="list">
                                <div class="img">
                                    <template v-if="product.product_photo_info && product.product_photo_info[0]">
                                        <nuxt-img
                                            format="webp"
                                            :src="product.product_photo_info[0].product_photo_data.content"
                                            :title="product.product_title"
                                        />
                                    </template>
                                    <img v-else class="empty-img" src="~/assets/images/empty-product.png" />
                                </div>
                                <div class="product_title text_overflow max_one_line_allow" v-html="product.product_title"></div>
                                <div class="product_type">{{ product.product_price_type }}</div>
                                <div class="quantity">Qty: {{ product.qty }}</div>
                            </div>
                        </swiper-slide>
                        <div class="swiper-pagination" slot="pagination"></div>
                    </swiper>                    
                </div>
                <div :class="['timeline_info',{active:$parent.sel_tab_index=='0'}]">
                    <template v-if="$parent.sel_tab_index=='0'">
                        <div @click="$parent.view_order_timelines(item.delivery_timeline_info)" class="action_btn">
                            <i class="far fa-check-circle"></i> Click here to update order status
                        </div>
                    </template>
                    <template v-else>
                        <div class="text_overflow max_one_line_allow"><i class="fa fa-check-circle"></i> {{ $store.state.order_timeline_list[item.delivery_timeline_info[0].timeline_id] }}</div>

                        <div @click="$parent.view_order_timelines(item.delivery_timeline_info)" class="action_btn">
                            <i class="far fa-check-circle"></i> Timeline
                        </div>
                    </template>
                </div>
            </div>
        </template>
        <template v-else>
            <EmptyCartBlock />
        </template>
    </div>
</template>
<script>
import EmptyCartBlock from '~/components/content_display/EmptyCartBlock'
export default {
    name: 'ContentCardView',
    props: ['data','content_loader','per_view'],
    components: {
        EmptyCartBlock
    },
    data(){
        return {
            swiperOption: {
                slidesPerView: this.per_view?this.per_view:2,
                spaceBetween: 10,
                freeMode: true,
                grabCursor: true
            }            
        }
    },
    computed: {
        cur_path: function(){
            return this.$route.fullPath
        }
    },
    methods: {
        curOrderStatus: function(data){
            let getId = '';
            if(data.delivery_timeline_info.length>0){
                let getTimeLines = data.delivery_timeline_info
                getId = getTimeLines[getTimeLines.length-1].timeline_id
            }
            
            return getId
        }
    }
}
</script>
<style scoped>
    .list_item{
        display: block;
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 3px;
        /* box-shadow: 0 0 5px #ccc */
    }
    .head_info{
        display: flex;
        padding: 5px 15px;
        min-height: 50px;
        background-color: #eee;
    }    
    .head_info > div.action_btn{
        align-self: center;
        margin-left: auto;
    }
    .head_info > div.action_btn > span{
        display: inline-block;
        border: 1px solid #ddd;
        background-color: #ccc;
        font-size: 12px; color: #333;
        padding: 1px 15px; border-radius: 25px;
        cursor: pointer;
    }
    .head_info > div.order_info{
        align-self: center;
    }
    div.total_items{
        text-align: center;        
    }
    div.total_items > small{
        font-weight: bold;
    }
    .item_info{
        display: block;
        padding:  0 15px;
        margin-top: 15px;
    }
    .list{
        display: block;
        position: relative;
        background-color: #ffffff;
        color: #666;        
        border: 1px solid #ddd;
        border-radius: 3px;
        box-shadow: 0 0 5px #ccc;
        transition: all 0.4s;
    }
    .list:hover{
        box-shadow: 0 0 5px #666;
    }
    .img{
        width: 100%;
        height: 80px;
        border-bottom: 1px solid #ddd;
    }
    .img > img{
        width: 100%; height: 100%; object-fit: contain;
    }
    .img > .empty-img{
        padding: 15px
    }
    .product_title{
        /* display: block; */
        padding: 8px 12px 0 12px;
        color: #252d49;
    }
    .product_type{
        padding: 3px 12px 0 12px;
        color: #666;
        font-size: 12px;
    }
    .quantity{
        display: block;
        padding: 0 12px;
        margin-bottom: 8px;        
        font-size: 14px;
        color: #333;
        overflow: hidden;
    }
    .timeline_info{
        display: flex;        
        margin-top: 15px;
        padding: 0 15px;
        font-size: 12px;
        height: 40px;
        border-top: 1px solid #ddd;
    }
    .timeline_info.active{
        display: block;        
        text-align: center;
        height: auto;
    }
    .timeline_info > div{
        align-self: center;
    }
    .timeline_info > .action_btn{        
        background-color: #CD0000;
        padding: 2px 15px;        
        margin-left: auto;
        color: #fff;
        cursor: pointer;
        white-space: nowrap;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .timeline_info.active > .action_btn{
        display: inline-block;
        margin: 8px 0;
        padding: 5px 15px;
    }
    .timeline_info > .action_btn:hover{
        background-color: #b15e00;
    }
</style>