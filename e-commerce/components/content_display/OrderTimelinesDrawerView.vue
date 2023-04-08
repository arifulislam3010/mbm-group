<template>
    <div :class="['order_timelines_drawer_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="$parent.close_order_timelines_block"></div>
        <div class="content clearfix">
            <div class="header">
                <div class="info">
                    <i class="fa fa-stream"></i>
                    <span class="pl-2">Order Tracking</span>
                </div>
            </div>
            <div :class="['main_content_block','pt-4',{delivery_person:$store.state.auth_info.user_data.user_type==$store.state.delivery_user_type_id}]">                
                <div v-if="delivery_timelines.length<order_timelines_len" class="status_list active">
                    <span class="left_border"></span>
                    <i class="fa fa-circle"></i>
                    <div class="text">
                        <div>{{ $store.state.order_status_list[delivery_timelines.length] }}</div>
                        <div><small>Running</small></div>
                    </div>
                </div>
                <div :class="['status_list',{done:order_timelines_len==item.timeline_id}]" v-for="(item,index) in delivery_timelines" :key="index">
                    <span class="left_border"></span>
                    <i class="fa fa-check"></i>
                    <div class="text">
                        <div>{{ $store.state.order_timeline_list[item.timeline_id] }}</div>
                        <div><small>{{ item.created_at }}</small></div>
                    </div>
                </div>
            </div>
            <template v-if="$store.state.auth_info.user_data.user_type==$store.state.delivery_user_type_id">
                <div v-if="delivery_timelines.length<order_timelines_len" class="footer">
                    <div v-if="pre_loader" class="loader">
                        <i class="fa fa-cog fa-spin"></i>
                        <div class="pl-3">Submitting...Wait</div>
                    </div>
                    <div v-else class="action_btn" @click="order_status_update(delivery_timelines.length + 1)">
                        <i class="fa fa-check-circle"></i>
                        <!-- <div class="pl-2">{{ $store.state.order_timeline_list[delivery_timelines.length + 1] }} submit</div> -->
                        <div class="pl-2">Submit &amp; Next Status</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    name: 'OrderTimelinesDrawerViewBlock',
    props: ['data'],
    data(){
        return {
            base_url: process.env.BASE_URL,
            delivery_timelines: this.data,
            order_timelines_len: Object.keys(this.$store.state.order_timeline_list).length,
            pre_loader: false
        }        
    },    
    mounted(){
        document.body.classList.add('popup_open')
    },
    methods: {
        fcm_notification_send: function($timeline_id, $registration_ids){
            let url = process.env.FCM_NOTIFICATION_URL
            let header_config = { 
                headers: {
                    'Authorization': 'Key=' + process.env.FCM_NOTIFICATION_AUTH_KEY, 
                    'Content-Type': 'application/json'
                }
            }
            let formData = JSON.stringify({
                "notification": {
                    "title": "MedQuicker",
                    "body": this.$store.state.order_timeline_list[$timeline_id],
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

                } else {
                    this.$toast.error(response.data.msg, {icon: "error_outline"})
                }                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.pre_loader = false
            });
        },
        order_status_update: function(id){            
            let url = '/api/manage-orders/update';
            let formData = {
                order_id: this.data[0].order_id,
                timeline_id: id,
                final_submit: false
            }

            if((this.delivery_timelines.length + 1) == this.order_timelines_len) formData.final_submit = true

            this.pre_loader = true
            this.$axios.post(url, formData, this.$parent.header_config).then( async response => {
                console.log(response)
                
                if(response.data.status){

                    // await this.$swal("Good job!", "Data has been saved successfully.", "success")

                    this.$notify.success({
                        title: 'Good job!',
                        message: 'Order status has been updated successfully.',
                        position: 'bottomRight',
                        transitionIn: 'bounceInRight',
                        transitionOut: 'fadeOutLeft',
                        timeout: 1500
                    })                                        

                    this.delivery_timelines = response.data.data

                    if(response.data.fcm_token_data.length>0){
                        let getFcmTokenData = response.data.fcm_token_data
                        let getFcmTokenList = []
                        getFcmTokenData.forEach(v => {
                            getFcmTokenList.push(v.token)
                        });

                        this.fcm_notification_send(this.delivery_timelines[0].timeline_id, getFcmTokenList)
                    }

                    this.$parent.load_data();

                } else {
                    this.$toast.error(response.data.msg, {icon: "error_outline"})
                }
                this.pre_loader = false                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.pre_loader = false
            });
        }
    }
}
</script>
<style scoped>
    .order_timelines_drawer_view_block{
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
        background-color: #fff;
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
    .label{
        font-size: 13px; font-weight: bold; color: #888;
        margin-bottom: 10px;
    }
    .header{
        display: block;
        height: 60px; padding: 0 15px;
        border-bottom: 1px solid #ddd;
        background-color: #f7f7f7;
    }
    .header > div{
        display: flex;
        height: 100%;
        font-size: 18px;
    }
    .header > div > i,
    .header > div > span{
        align-self: center;
    }
    .header > div > i{
        color: #999;
        font-size: 24px;
    }
    .main_content_block{
        position: absolute;
        top: 60px; left: 0;
        width: 100%;
        height: calc(100% - 60px);
        overflow-y: auto;
    }
    .main_content_block.delivery_person{
        height: calc(100% - 120px);
    }
    .status_list{
        display: flex;
        position: relative;
        padding: 0 12px;
        font-size: 12px;        
        background-color: #fff;        
        height: 50px;
    }
    .status_list > .left_border{
        position: absolute;
        display: inline-block;
        left: 19px;
        top: 12px;
        width: 1px;
        height: 40px;
        border-left: 2px dashed #ddd;
        z-index: 1;
    }
    .status_list:last-child > .left_border{
        border: none;
    }
    .status_list > i,
    .status_list > div{
        align-self: top;
    }
    .status_list > i{
        position: relative;
        text-align: center;
        width: 16px; height: 16px;
        background-color: rgb(71, 189, 71);
        border: 1px solid rgb(71, 189, 71);;
        color: #fff; font-size: 6px;
        line-height: 14px;
        border-radius: 50%;
        z-index: 5;
    }
    .status_list.active > i{
        background-color: transparent;
        color: rgb(71, 189, 71);
        font-size: 6px;
        animation: pulse 1.0s infinite cubic-bezier(0.10, 0, 0, 0.25);
    }
    .status_list.done > i{
        font-size: 8px;
        width: 22px;
        height: 22px;
        position: relative;
        left: -3px;
        line-height: 20px;
    }
    .status_list > div{
        margin-left: 10px;
        font-size: 12px;
    }
    .status_list.active > div{
        color: #333; font-weight: bold;
    }
    .status_list.done > div{
        font-size: 14px;
        margin-left: 5px;
        color: #333; font-weight: bold;
    }
    .status_list.done > div small{
        font-size: 10px;
    }
    .footer{
        position: absolute;
        left: 0; bottom: 0; height: 60px; padding: 15px; width: 100%;
        background-color: #f7f7f7;
        border-top: 1px solid #ddd;
        z-index: 5;
    }
    .footer > .action_btn{
        display: flex;
        height: 30px;
        padding: 2px 15px;
        border-bottom: 1px solid #ddd;
        border-radius: 25px;
        background-color: rgb(0, 177, 0);
        cursor: pointer;
        color: #fff;
        font-size: 13px;
        transition: all 0.4s;
    }
    .footer > .action_btn > i,
    .footer > .action_btn > div{
        align-self: center;
    }
    .footer > .action_btn:hover{
        background-color: #CD0000;
    }
    .loader{
        display: flex;
        height: 100%;
    }
    .loader > i,
    .loader > div{
        align-self: center;
    }
    @keyframes pulse {
        to {
            box-shadow: 0 0 0 20px rgba(71, 189, 71, 0.4);
        }
    }
</style>