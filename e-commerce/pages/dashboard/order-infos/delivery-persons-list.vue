
<template>
    <div :class="['delivery_persons_list_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="$parent.close_delivery_list_block"></div>
        <div class="content clearfix">
            <div class="header">
                <div class="info">
                    <i class="fa fa-biking"></i>
                    <span>Delivery persons list</span>
                </div>                
            </div>
            <div class="main_content_block">
                <template v-if="assign_pre_loader">
                    <div class="black_overlay_for_loader"></div>
                    <div class="loader_content">
                        <i class="fa fa-cog fa-spin"></i> Submitting...Wait
                    </div>
                </template>
                <div class="delivery_man_info">                    
                    <div v-if="content_loader" class="p-4">
                        <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                        <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                    </div>
                    <template v-else>
                        <template v-for="(item,index) in delivery_person_list">
                            <div v-if="!assign_items[item.id]" :key="index" class="person_list mb-3">
                                <DeliverPersonInfo :data="item" />
                                <div class="action_info">
                                    <div class="action_btn" @click="assign_delivery_person(item)">Assign</div>
                                    <div class="status"><i class="fa fa-check"></i> Available</div>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>            
            <!-- <div class="pagination_block" v-if="pagination_show">
                <Pagination :config="pagination_config" />                    
            </div> -->
        </div>        
    </div>
</template>
<script>
import DeliverPersonInfo from '~/components/content_display/DeliveryPersonInfo'
export default {
    name: 'DeliveryPersonsListViewBlock',
    props: ['order_id'],
    components: {
        DeliverPersonInfo
    },
    data(){
        return {
            base_url: process.env.BASE_URL,
            content_loader: false,
            assign_pre_loader: false,
            pagination_show: false,
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            delivery_person_list: [],
            assign_items: {},
            srch_person: '',
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 10
        }
    },
    mounted(){
        document.body.classList.add('popup_open')
        this.load_data()
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
            let formData = JSON.stringify({
                "notification": {
                    "title": "MedQuicker",
                    "body": "The new order is waiting for you.",
                    "click_action": this.base_url,
                    "icon": this.base_url + "/icon.png"
                },
                "registration_ids": $registration_ids
            })
            

            this.$axios.post(url, formData, header_config).then( response => {
                console.log(response)
                if(response.data.success){

                    this.$notify.success({
                        title: 'Success',
                        message: 'Notification has been sent to delivery person',
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
        assign_delivery_person: function(user){
            let url = '/api/order-delivery-person-infos/store';
            let formData = {
                delivery_person_id: user.id,
                order_id: this.order_id,
                timeline_id: 2
            }

            this.assign_pre_loader = true
            
            this.$axios.post(url, formData, this.$parent.$parent.header_config).then( async response => {
                console.log(response)                                
                if(response.data.status){
                    this.assign_items[user.id] = true

                    await this.$swal("Good Job!", "Order has been assigned to delivery person.", "success")
                    this.assign_pre_loader = false
                    
                    if(response.data.fcm_token_data.length>0){
                        let getFcmTokenData = response.data.fcm_token_data
                        let getFcmTokenList = []
                        getFcmTokenData.forEach(v => {
                            getFcmTokenList.push(v.token)
                        });

                        this.fcm_notification_send(getFcmTokenList)
                    }

                    this.$parent.$parent.load_data()
                    
                    this.$parent.close_delivery_list_block()
                }else{
                    this.$toast.error(response.data.msg, {icon: "error_outline"})
                    this.assign_pre_loader = false
                }
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.assign_pre_loader = false
            });
        },
        async load_data(pg=this.cur_page){
            let url = '/api/delivery-persons/available'+ (this.limit>0?'?limit=' + this.limit:'') + (pg>1?'&page=' + pg:'') + (this.srch_person?'&keyword=' + this.srch_person:'');

            this.content_loader = true
            this.pagination_show = false
            this.delivery_person_list = [];
            this.$axios.get(url, this.$parent.$parent.header_config).then( (response) => {
                console.log(response)
                if(response.data.data.length>0){
                    this.delivery_person_list = response.data.data
                    this.pagination_config.data = response.data.meta                    
                    this.pagination_show = true
                }
                this.content_loader = false                
                this.cur_page = pg
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false
            });
        }
    }
}
</script>
<style scoped>
    .delivery_persons_list_view_block{
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
    }
    .header > div > i{
        color: #999; margin-right: 5px;
    }
    .main_content_block{
        position: absolute;
        top: 60px; left: 0;
        width: 100%;
        height: calc(100% - 60px);
        overflow-y: auto;
    }
    .black_overlay_for_loader{
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background-color: #00000080;
        z-index: 5;
    }
    .loader_content{
        position: absolute;
        width: 100%;
        height: auto;
        top: 35%; left: 0;
        padding: 15px;
        text-align: center;
        background-color: #fff;
        color: #666;
        z-index: 10;
    }
    .person_list{
        display: flex;
        height: 60px;
        padding: 0 12px;        
        background: #f7f7f7;   
    }
    .person_list > div{
        align-self: center;
    }    
    .action_info{
        margin-left: auto;
        min-width: 100px;
        text-align: right;
    }
    .action_info > .action_btn{
        display: inline-block; font-size: 10px;
        padding: 1px 10px; background-color: sienna;
        color: #fff; cursor: pointer;
        border-radius: 25px; transition: all 0.4s;
    }
    .action_info > .action_btn:hover{
        background-color: #cd5c00;
        font-size: 12px;
    }
    .action_info .status{
        margin-top: 2px;
        font-size: 10px; color: rgb(33, 131, 33)
    }
    /* .pagination_block{
        position: absolute;
        width: 100%;
        display: flex;
        bottom: 0; height: 60px;
        border-top: 1px solid #ddd;
        background-color: #fff;
    } */
</style>