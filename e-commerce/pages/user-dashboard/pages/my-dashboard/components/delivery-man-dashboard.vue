<template>
    <div class="my_dashboard_block">        
        <div class="row">
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="content_block">
                            <label>Total Orders</label>
                            <div v-if="pre_loader">
                                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                            </div>
                            <div v-else class="total">{{ countable_data.total_orders }}</div>
                            <div class="icon yellow">
                                <i class="fa fa-shopping-basket"></i>
                            </div>
                        </div>
                    </div>            
                    <div class="col-md-6 mb-4">
                        <div class="content_block">
                            <label>Total Delivered Orders</label>
                            <div v-if="pre_loader">
                                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                            </div>
                            <div v-else class="total">{{ countable_data.total_delivered_orders }}</div>
                            <div class="icon orange">
                                <i class="fa fa-truck"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content_block">
                            <label>Total Cash on Delivery</label>
                            <div v-if="pre_loader">
                                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                            </div>
                            <div v-else class="total">{{ countable_data.total_cash_on_delivery }}</div>
                            <div class="icon violet">
                                <i class="fa fa-hand-holding-usd"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content_block">
                            <label>Total Digital Payment Received</label>
                            <div v-if="pre_loader">
                                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                            </div>
                            <div v-else class="total">{{ countable_data.total_digital_payment_delivery }}</div>
                            <div class="icon green">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
</template>
<script>
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
export default {
    name: 'MyDashboard',
    components: {
        PriceViewBlock
    },
    data(){
        return {
            pre_loader: false,
            countable_data: []
        }
    },
    mounted (){
        this.get_data()
    },
    methods: {
        get_data: function(){
            this.pre_loader = true
            this.$axios.get('/api/data-report/delivery-man-order-history', this.$parent.$parent.header_config).then( async (response) => {
                console.log('Get Data', response.data)
                this.countable_data = response.data.data
                this.pre_loader = false;
            }).catch(e => {
                // console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.pre_loader = false;
            });
        }
    }
}
</script>
<style lang="scss" scoped>
    .my_dashboard_block{
        display: block;
        padding: 20px
    }
    .content_block{
        position: relative;
        display: block;
        background: $white;
        border: 1px solid #ddd;
        border-radius: 3px;
        padding: 20px; height: 125px;
        box-shadow: 0 0 10px #ddd;
    }
    .content_block label{
        width: 105px;
        font-size: 12px;
        line-height: 18px;
    }
    .content_block .total{
        color: #333;
        font-size: 24px;
    }
    .content_block .icon{
        display: inline-block;
        position: absolute;
        right: 25px;
        top: 25px;
        width: 50px; height: 50px; line-height: 50px;
        text-align: center;
        border-radius: 50%;        
    }
    .content_block .icon::after{
        position: absolute;
        content: '';
        width: 70px;
        height: 70px;
        border-radius: 50%;
        bottom: 0;
        top: -10px;
        right: 0;
        left: -10px;
    }
    .content_block .icon.orange{
        background-color: $sys_brand_color_op;
        color: $sys_brand_color;
    }
    .content_block .icon.orange::after{
        background-color: $sys_brand_color_opc
    }
    .content_block .icon.violet{
        background-color: $violet_op;
        color: $violet;
    }
    .content_block .icon.violet::after{
        background-color: $violet_opc;
    }
    .content_block .icon.yellow{
        background-color: $yellow_op;
        color: $yellow;
    }
    .content_block .icon.yellow::after{
        background-color: $yellow_opc;
    }
    .content_block .icon.green{
        background-color: $green_op;
        color: $green;
    }
    .content_block .icon.green::after{
        background-color: $green_opc;
    }
</style>