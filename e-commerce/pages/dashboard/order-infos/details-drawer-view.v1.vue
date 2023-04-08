
<template>
    <div :class="['order_detail_drawer_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="$parent.close_order_dtl_block"></div>
        <div class="content clearfix">
            <div v-if="pre_loader" class="preloader_block">
                <div class="content_loader">
                    <span>Loading...</span>
                    <i class="fa fa-cog fa-spin"></i>
                </div>
            </div>
            <div class="header">
                <div class="info">
                    <i class="fa fa-notes-medical"></i>
                    <span>Order Details</span>
                </div>
                <div v-if="sel_data.status=='0'" class="cancel_btn">
                    <span @click="order_cancel(sel_data.id)"><i class="fa fa-times-circle"></i> Order Cancel</span>
                </div>
            </div>
            <div :class="['main_content_block',{edit_mode:edit_receipt}]">
                <template v-if="edit_receipt">
                    <div class="receipt_info">
                        <div class="title_block">
                            <label>Receipt</label>
                        </div>
                        <template v-if="Object.keys(sel_data).length>0">
                            <OrderDetailsReceiptInfo :data="sel_data.order_items_info" />
                            <hr style="border: 1px dashed #ddd" />
                            <OrderSummaryInfo :data="sel_data" />
                        </template>
                    </div>
                    <div class="action_block">
                        <span @click="edit_receipt=false" class="mr-2"><i class="fa fa-times-circle"></i> Cancel</span>
                        <span @click="order_update(sel_data.id)"><i class="fa fa-save"></i> Update</span>
                    </div>
                </template>
                <template v-else>
                    <div class="order_info">
                        <div class="order_id">ID #{{ sel_data.order_id }}</div>
                        <div class="ordered_at"><small>Placed on {{ sel_data.created_at }}</small></div>
                    </div>                
                    <div class="delivery_man_info">
                        <div class="label">Delivery Person</div>
                        <div v-if="sel_data.delivery_person_info" class="user_info_block">
                            <DeliverPersonInfo :data="sel_data.delivery_person_info.user" />
                        </div>
                        <div v-else class="assign_delivery_person_block">
                            <span @click="$parent.open_order_details=false; $parent.open_delivery_person_list=true; $parent.sel_order_id=sel_data.id"><i class="fa fa-biking"></i> Assign Delivery Person</span>
                        </div>
                    </div>
                    <div class="shipping_info">
                        <div class="label">Shipping Address</div>
                        <div v-html="sel_data.shipping_address"></div>                    

                        <div class="label mt-3">Billing Address</div>
                        <div v-html="sel_data.billing_address"></div>

                        <div class="label mt-3">Contact number</div>
                        <div v-html="sel_data.contact_no"></div>

                        <div class="label mt-3">E-mail</div>
                        <div v-html="sel_data.email"></div>
                    </div>
                
                    <div class="receipt_info">
                        <div class="title_block">
                            <label>Receipt</label>
                            <div v-if="sel_data.status=='0'">                            
                                <span @click="edit_receipt=true"><i class="fa fa-edit"></i> Edit</span>                            
                            </div>
                        </div>
                        <template v-if="Object.keys(sel_data).length>0">
                            <OrderDetailsReceiptInfo :data="sel_data.order_items_info" />
                            <hr style="border: 1px dashed #ddd" />
                            <OrderSummaryInfo :data="sel_data" />
                        </template>
                    </div>
                </template>
            </div>
        </div>        
    </div>
</template>
<script>
import DeliverPersonInfo from '@/components/content_display/DeliveryPersonInfo'
import OrderDetailsReceiptInfo from './components/OrderDetailsReceiptInfo'
import OrderSummaryInfo from './components/OrderSummaryInfo'
export default {
    name: 'OrderDetailsDrawerViewBlock',
    // props: ['data'],
    components: {
        OrderDetailsReceiptInfo,
        DeliverPersonInfo,
        OrderSummaryInfo
    },
    data(){
        return {
            // getDataList: {},
            pre_loader: false,
            order_canceled: false,
            order_updated: false,
            edit_receipt: false
        }
    },
    computed: {
        sel_data: function(){
            return this.$store.state.order_info.sel_data
        }
    },
    mounted(){
        document.body.classList.add('popup_open')
        // this.getDataList = this.data
    },
    methods: {
        fcm_notification_send: function($registration_ids){
            let url = process.env.FCM_NOTIFICATION_URL
            let header_config = { 
                headers: {
                    'Authorization': 'Key=' + process.env.FCM_NOTIFICATION_AUTH_KEY, 
                    'Content-Type': 'application/json'
                }
            }
            let body_msg = 'Welcome'
            
            if(this.order_canceled) body_msg = 'The order #' + this.order_id + ' has been canceled'
            else if(this.order_updated) body_msg = 'The order #' + this.order_id + ' has been updated'
            let formData = JSON.stringify({
                "notification": {
                    "title": process.env.APP_NAME,
                    "body": body_msg,
                    "click_action": this.base_url,
                    "icon": this.base_url + "/icon.png",
                    "type": "timeline"
                },
                "registration_ids": $registration_ids
            })
            

            this.$axios.post(url, formData, header_config).then( response => {
                console.log(response)
                if(response.data.success){

                    this.$notify.success({
                        title: 'Success',
                        message: 'Notification has been sent to customer',
                        position: 'bottomRight',
                        transitionIn: 'bounceInRight',
                        transitionOut: 'fadeOutLeft',
                        timeout: 1500
                    })

                    this.order_canceled = false
                    this.order_updated = false

                } else {
                    this.$toast.error(response.data.msg, {icon: "error_outline"})
                }                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.pre_loader = false
            });
        },
        order_update: function(id){
            if(confirm('Are you sure to update the order?')){
                let url = '/api/order-infos/update/' + id;                

                this.pre_loader = true
                this.$axios.post(url, this.$store.state.order_info.sel_data, this.$parent.$parent.header_config).then( async response => {
                    console.log(response)
                    
                    if(response.data.status){

                        this.order_updated = true                    

                        this.$notify.success({
                            title: 'Good job!',
                            message: 'The order has been updated successfully.',
                            position: 'bottomRight',
                            transitionIn: 'bounceInRight',
                            transitionOut: 'fadeOutLeft',
                            timeout: 1500
                        })

                        if(response.data.fcm_token_data.length>0){
                            let getFcmTokenData = response.data.fcm_token_data
                            let getFcmTokenList = []
                            getFcmTokenData.forEach(v => {
                                getFcmTokenList.push(v.token)
                            });

                            this.fcm_notification_send(getFcmTokenList)
                        }

                        this.$parent.$parent.load_data();

                    } else {
                        this.$toast.error(response.data.msg, {icon: "error_outline"})
                    }
                    this.pre_loader = false                
                }).catch(e => {
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.pre_loader = false
                });
            }
        },
        order_cancel: function(id){
            if(confirm('Are you sure to cancel the order?')){
                let url = '/api/order-infos/cancel';
                let formData = {
                    order_id: id
                }            

                this.pre_loader = true
                this.$axios.post(url, formData, this.$parent.$parent.header_config).then( async response => {
                    console.log(response)
                    
                    if(response.data.status){

                        this.order_canceled = true                    

                        this.$notify.success({
                            title: 'Good job!',
                            message: 'The order has been canceled successfully.',
                            position: 'bottomRight',
                            transitionIn: 'bounceInRight',
                            transitionOut: 'fadeOutLeft',
                            timeout: 1500
                        })

                        if(response.data.fcm_token_data.length>0){
                            let getFcmTokenData = response.data.fcm_token_data
                            let getFcmTokenList = []
                            getFcmTokenData.forEach(v => {
                                getFcmTokenList.push(v.token)
                            });

                            this.fcm_notification_send(getFcmTokenList)
                        }

                        this.$parent.$parent.load_data();

                    } else {
                        this.$toast.error(response.data.msg, {icon: "error_outline"})
                    }
                    this.pre_loader = false                
                }).catch(e => {
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.pre_loader = false
                });
            }
        },
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
        right: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
        transition: all 0.4s;
    }
    .mobile_device .content{
        width: 300px;
    }
    .preloader_block{
        position: absolute;
        width: 100%; height: 100%;
        background-color: #ffffff80;
        z-index: 5
    }
    .preloader_block .content_loader{
        display: block;
        width: 100%;        
        padding: 15px;
        text-align: center;        
        margin-top: 45%;
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        box-shadow: 0 0 5px #ccc;
        background-color: #fff;
    }
    .label{
        font-size: 13px; font-weight: bold; color: #888;
        margin-bottom: 10px;
    }
    .header{
        display: flex;
        height: 60px; padding: 0 15px;
        border-bottom: 1px solid #ddd;
        background-color: #f7f7f7;
    }
    .header > div{
        align-self: center;
        font-size: 18px;
        width: 100%;
    }
    .header > div > i{
        color: #999; margin-right: 5px;
    }
    .header > div.cancel_btn{
        margin-left: auto;
        text-align: right;        
    }
    .header > div.cancel_btn > span{
        display: inline-block;
        padding: 2px 10px;
        font-size: 10px;
        font-weight: bold;
        color: #fff;
        background-color: #CD0000;
        cursor: pointer;
        border-radius: 25px;
        transition: all 0.4s
    }
    .header > div.cancel_btn > span:hover{
        background-color: #CC0000; color: #f7f7f7
    }
    .main_content_block{
        position: absolute;
        top: 60px; left: 0;
        width: 100%;
        height: calc(100% - 60px);
        overflow-y: auto;
    }
    .main_content_block.edit_mode{
        height: calc(100% - 100px);
    }
    .order_info,
    .order_again,
    .order_status,
    .delivery_man_info,
    .receipt_info,
    .shipping_info{
        display: block;
        background-color: #fff;
        padding: 8px 12px;
        border-bottom: 1px solid #ddd;
    }
    .receipt_info > .title_block{
        display: flex;
        height: 40px
    }
    .receipt_info > .title_block > label,
    .receipt_info > .title_block > div {
        align-self: center;
        margin: 0; width: 100%;
    }
    .receipt_info > .title_block > label{
        font-size: 13px; font-weight: bold; color: #888        
    }
    .receipt_info > .title_block > div{
        font-size: 12px; cursor: pointer;
        margin-left: auto; text-align: right
    }
    .user_info_block{
        display: flex;
        height: 60px;
        /* padding: 0 12px;        
        background: #f7f7f7;    */
    }
    .user_info_block > div{
        align-self: center;
    }
    .order_info > .order_id{
        font-weight: bold;
    }
    .order_again{
        display: flex;
        margin-top: 5px;
        height: 40px;
        cursor: pointer;        
    }
    .order_again > span,
    .order_again > i{
        align-self: center;
        color: #CD0000;
        font-size: 12px;
        font-weight: bold;
    }
    .order_again > i{
        margin-left: auto;
    }
    .order_status{
        margin-top: 5px;
    }    
    .order_status > .status{
        display: flex;
        height: 30px;
    }
    .order_status > .status > i,
    .order_status > .status > div{
        align-self: center;
        font-weight: bold;        
    }
    .order_status > .status > i{
        margin-right: 10px;
        font-size: 16px;
        color: #4dd24d;
    }
    .shipping_info,
    .delivery_man_info,
    .receipt_info{
        margin-top: 5px;
    }
    .delivery_man_info > .user_info{
        display: flex;
        width: 100%;
        height: 40px;
    }
    .delivery_man_info > .user_info > div{
        align-self: center;
    }
    .delivery_man_info > .user_info > div.photo{
        margin-right: 10px;
        width: 36px;
        height: 36px;
        line-height: 34px;
        text-align: center;
        background-color: #f7f7f7;
        border: 1px solid #ddd;
        border-radius: 50%;
    }
    .delivery_man_info > .user_info > div.photo > i{
        width: 100%;
        height: 100%;
        color: #ccc;
    }
    .delivery_man_info > .user_info > div.call_btn{
        margin-left: auto;
    }
    .delivery_man_info > .user_info > div.call_btn > span{
        display: inline-block; margin-left: 10px;
        background-color: #eee; font-size: 10px; font-weight: bold;
        padding: 2px 10px; border: 1px solid #ddd;
        border-radius: 25px; cursor: pointer;
    }
    .assign_delivery_person_block > span{
        display: inline-block;
        padding: 1px 10px;
        border: 1px solid #ddd;
        border-radius: 25px;
        cursor: pointer;
        font-size: 12px;
        background-color: #f7f7f7;
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
        .address_lbl{
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
    }
    .action_block{
        position: fixed;
        line-height: 40px;
        right: 0;
        bottom: 0;        
        width: 350px;
        background-color: #f7f7f7;
        padding: 0 15px;
        border-top: 1px solid #ddd;
        text-align: right;
        & > span{
            display: inline-block;
            background-color: #2d97c9;
            color: #fff;
            font-size: 10px;
            line-height: 16px;
            cursor: pointer;
            padding: 1px 10px;
            border-radius: 25px;
            transition: all 0.4s;
            &:hover{
                background-color: #006699
            }
            &:first-child{
                background-color: #cf3b00;
                &:hover{
                    background-color: #CD0000
                }
            }
        }
    }
</style>