<template>
    <div :class="['running_order_info_block',{mobile_device:$device.isMobile}]">
        <div class="container">
            <div class="content_block">
                <div class="head_section">
                    <label>Ongoing</label>
                    <div v-for="(n,i) in 1" class="ml-3 mr-3" :key="i">
                        <FormBlockLoader :cols="1" :height="15" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    </div>
                    <div class="action_btn">                        
                        <i v-if="bar_active" @click="bar_active=false" class="fa fa-angle-down"></i>
                        <i v-else @click="bar_active=true" class="fa fa-angle-up"></i>
                    </div>
                </div>
                <template v-if="bar_active">
                    <carousel
                        :paginationEnabled="false"                
                        :perPage="1"                
                        :minSwipeDistance="16"
                        :loop="true">
                        <slide v-for="(item,index) in $store.state.running_order_info" :key="index">
                            <div class="order_content" @click="view_details_order(item)">
                                <div class="current_status_bg">
                                    <img v-if="item.delivery_timeline_info[0]" :src="$store.state.order_status_images[item.delivery_timeline_info[0].timeline_id]" :title="$store.state.order_status_list[item.delivery_timeline_info[0].timeline_id]" :alt="$store.state.order_status_list[item.delivery_timeline_info[0].timeline_id]" />
                                </div>
                                <div class="info">
                                    <div class="status text_overflow max_one_line_allow" v-if="item.delivery_timeline_info[0]">{{ $store.state.order_status_list[item.delivery_timeline_info[0].timeline_id] }}</div>
                                    <div class="order_no">Order #{{ item.order_id }}</div>
                                </div>
                                <div class="dtl_btn"><i class="fa fa-angle-right"></i></div>
                            </div>
                        </slide>
                    </carousel>
                </template>
            </div>
        </div>

        <OrderDetailsDrawerView v-if="open_order_details" :data="sel_order_data" />
        <OrderTimelinessDrawerView v-if="open_order_timelines" :data="sel_timelines_data" />
    </div>
</template>
<script>
import OrderDetailsDrawerView from '~/pages/user-dashboard/pages/my-orders/details-drawer-view'
import OrderTimelinessDrawerView from '~/components/content_display/OrderTimelinesDrawerView'
export default {
    name: 'RunningOrderStatusBlock',
    components: {
        OrderDetailsDrawerView,
        OrderTimelinessDrawerView
    },
    data(){
        return {
            bar_active: true,
            open_order_details: false,
            open_order_timelines: false,
            sel_order_data: [],
            sel_timelines_data: []
        }
    },
    methods: {
        view_details_order: function(data){
            this.open_order_details = true
            this.sel_order_data = data
        },
        view_order_timelines: function(data){
            this.open_order_timelines = true
            this.sel_timelines_data = data
        },
        close_order_dtl_block: function(){
            this.open_order_details = false
            document.body.classList.remove('popup_open')
        },
        close_order_timelines_block: function(){
            this.open_order_timelines = false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style scoped>
    .running_order_info_block{
        position: fixed;
        bottom: 0; left: 0;
        width: 100%;
        z-index: 999;
    }
    .running_order_info_block .container{
        position: relative
    }
    .running_order_info_block.mobile_device{
        bottom: 44px;
    }
    .content_block{
        position: absolute;
        display: inline-block;
        background-color: #fff;
        bottom: 0; left: 5px;
        width: 350px;
        max-width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px 5px 0 0;
        box-shadow: 0 0 15px #000;
        overflow: hidden;
    }
    .mobile_device .content_block{
        padding-bottom: 25px;
    }
    .head_section{
        display: flex;
        height: 30px;
        width: 100%;
        padding: 0 10px;
        background-color: #f7f7f7;
        border-bottom: 1px solid#ddd;
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
    .order_content{
        display: flex;        
        height: 60px;
        padding: 10px;
    }
    .order_content > div{
        align-self: center;
    }
    .order_content > div.current_status_bg{
        width: 90px; height: 100%;
        border: 1px solid #ddd;
        text-align: center;
    }
    .order_content > div.current_status_bg > img{
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .order_content > div.info{
        width: calc(100% - 30px); margin: 0 10px;
    }
    .order_content > div.info > .status{
        font-size: 14px;
    }
    .order_content > div.info > .order_no{
        margin-top: 5px; font-size: 11px;
    }
    .order_content > div.dtl_btn{
        margin-left: auto;
    }
</style>