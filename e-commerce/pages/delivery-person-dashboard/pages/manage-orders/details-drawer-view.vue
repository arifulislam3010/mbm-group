<template>
    <div :class="['order_detail_drawer_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="$parent.close_order_dtl_block"></div>
        <div class="content clearfix">
            <div class="header">
                <div class="info">
                    <i class="fa fa-notes-medical"></i>
                    <span>Order Details</span>
                </div>                
            </div>
            <div class="main_content_block">
                <div class="order_info">
                    <div class="order_id">ID #{{ data.order_id }}</div>
                    <div class="ordered_at"><small>Placed on {{ data.created_at }}</small></div>
                </div>
                <div v-if="data.status" class="order_again">
                    <span>Order again if you want</span>
                    <i class="fa fa-angle-right"></i>
                </div>
                <div :class="['order_status',{bg:$store.state.order_status_images[curOrderStatus.timeline_id]}]" :style="'background-image:url(' + $store.state.order_status_images[curOrderStatus.timeline_id] + ')'">
                    <div class="label_section">
                        <div class="label">Order Status</div>
                        <div class="timeline_btn">
                            <span @click="$parent.view_order_timelines(data.delivery_timeline_info)">Timeline</span>
                        </div>
                    </div>
                    <div :class="['status',{running:!data.status}]">
                        <i v-if="data.status" class="fa fa-check-circle"></i>
                        <i v-else class="fa fa-circle"></i>
                        <div v-if="curOrderStatus" class="text_overflow max_one_line_allow">
                            <template v-if="data.status">{{ $store.state.order_timeline_list[curOrderStatus.timeline_id] }}</template>
                            <template v-else>{{ $store.state.order_status_list[curOrderStatus.timeline_id] }}</template>    
                        </div>
                    </div>
                    <!-- <div class="status_update_time">
                        <small>{{ curOrderStatus.created_at }}</small>
                    </div> -->
                </div>                
                <div v-if="data.customer_info" class="customer_data_info">
                    <div class="label">Customer Info</div>
                    <div class="user_info_block">
                        <CustomerInfo :data="data.customer_info" />
                        
                        <div v-if="data.customer_info.mobile" class="call_btn">
                            <a :href="`tel:${data.customer_info.mobile}`"><i class="fa fa-phone-alt pr-1"></i> Call</a>
                        </div>
                    </div>
                </div>
                <div class="shipping_info">
                    <div class="label">Shipping Address</div>
                    <div v-html="data.shipping_address"></div>                    

                    <div class="label mt-3">Billing Address</div>
                    <div v-html="data.billing_address"></div>

                    <div class="label mt-3">Contact number</div>
                    <div v-html="data.contact_no"></div>

                    <div class="label mt-3">E-mail</div>
                    <div v-html="data.email"></div>
                </div>
                <div class="receipt_info">
                    <div class="label">Receipt</div>
                    <OrderDetailsReceiptInfo :data="data.order_items_info" />
                    <hr style="border: 1px dashed #ddd" />
                    <OrderSummaryInfo />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import CustomerInfo from '~/components/content_display/CustomerInfo'
import OrderDetailsReceiptInfo from './components/OrderDetailsReceiptInfo'
import OrderSummaryInfo from './components/OrderSummaryInfo'
export default {
    name: 'OrderDetailsDrawerViewBlock',
    props: ['data'],
    components: {
        CustomerInfo,
        OrderDetailsReceiptInfo,
        OrderSummaryInfo
    },
    computed: {
        curOrderStatus: function(){
            let obj = {}
            if(this.data.delivery_timeline_info.length>0){
                let getTimeLines = this.data.delivery_timeline_info
                obj = getTimeLines[0]
            }

            return obj
        }
    },
    mounted(){
        document.body.classList.add('popup_open')
    }
}
</script>
<style lang="scss" scoped>
    .order_detail_drawer_view_block{
        position: relative;
        z-index: 1001;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content{
        position: fixed;
        color: #333;
        background-color: #eee;
        width: 350px;
        height: 100%;
        overflow-y: auto;
        left: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
    }
    .mobile_device .content{
        width: 300px;
    }    
    .label_section{
        display: flex;
        height: 30px;
        & > div{
            align-self: top;
        }
        & > .label{
            font-size: 13px; font-weight: bold; color: #888;
            margin-right: 15px;
        }
        & > .timeline_btn{
            margin-left: auto; cursor: pointer;
            & > span{
                display: inline-block;
                padding: 1px 15px;
                background-color: #f16837;
                color: #fff;
                font-size: 11px;
                border-radius: 25px;
                border: 1px solid #fff;
                box-shadow: 0 0 5px #999;
            }
        }
    }
    .header{
        display: flex;
        height: 60px; padding: 0 15px;
        border-bottom: 1px solid #ddd;
        background-color: #f7f7f7;
        & > div{
            align-self: center;
            font-size: 18px;
            & > i{
                color: #999; margin-right: 5px;
            }
        }
    }
    .main_content_block{
        position: absolute;
        top: 60px; left: 0;
        width: 100%;
        height: calc(100% - 60px);
        overflow-y: auto;
    }
    .order_info,
    .order_again,
    .order_status,
    .customer_data_info,
    .receipt_info,
    .shipping_info{
        display: block;
        background-color: #fff;
        padding: 8px 12px;
        border-bottom: 1px solid #ddd;
    }
    .order_info > .order_id{
        font-weight: bold;
    }
    .order_again{
        display: flex;
        margin-top: 5px;
        height: 40px;
        cursor: pointer;        
        & > span,
        & > i{
            align-self: center;
            color: #CD0000;
            font-size: 12px;
            font-weight: bold;
            margin-left: auto;
        }
    }
    .order_status{
        margin-top: 5px;
        &.bg{        
            background-position: center bottom;
            background-size: contain;
            height: 200px;
            background-repeat: no-repeat;
        }
        & > .status{
            display: flex;
            height: 22px;
            & > i,
            & > div{
                align-self: center;
                font-weight: bold;        
            }
            & > i{
                position: relative;
                text-align: center;
                width: 16px; height: 16px; margin-right: 10px;
                background-color: #47bd47;
                border: 1px solid #47bd47;;
                color: #fff; font-size: 14px;
                line-height: 14px;
                border-radius: 50%;
                z-index: 5;
            }
            &.running > i{
                background-color: transparent;
                color: #47bd47;
                font-size: 6px;
                animation: pulse 1.0s infinite cubic-bezier(0.10, 0, 0, 0.25);
            }
        }
    }
    .status_update_time{
        margin-left: 25px;
        line-height: 12px;
        font-size: 12px;
    }
    .shipping_info,
    .customer_data_info,
    .receipt_info{
        margin-top: 5px;
    }
    .customer_data_info > .user_info_block{
        display: flex;
        width: 100%;
        height: 40px;
        & > div{
            align-self: center;
            &.call_btn{
                margin-left: auto;
                & > a{
                    display: inline-block; margin-left: 10px;
                    background-color: #eee; font-size: 10px; font-weight: bold;
                    padding: 2px 10px; border: 1px solid #ddd;
                    border-radius: 25px; cursor: pointer;
                }
            }
        }
    }
    .shipping_info :deep(.contact_person){
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    .shipping_info :deep(.address_dtl){
        display: flex;
        margin-top: 5px;
        font-size: 12px;
    }
    .shipping_info :deep(.address_dtl) .address_lbl{
        display: inline-block;
        white-space: nowrap;
        padding: 1px 10px;
        height: 17px;
        margin-right: 10px;
        background-color: #666;
        color: #fff;
        border-radius: 25px;
        font-size: 10px;
        & > em{
            margin-right: 2px;
        }
        &.home{
            background-color: #006699;
        }
        &.office{
            background-color: #CD0000;
        }
    }
    @keyframes pulse {
        to {
            box-shadow: 0 0 0 20px #47bd4766;
        }
    }
</style>