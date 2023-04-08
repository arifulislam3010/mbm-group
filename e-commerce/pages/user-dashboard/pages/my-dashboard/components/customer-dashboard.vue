<template>
    <div class="my_dashboard_block">        
        <div class="row">
            <div class="col-md-4">
                <div class="content_block">
                    <label>Total Orders</label>
                    <div v-if="pre_loader">
                        <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                    </div>
                    <div v-else class="total">
                        <!-- {{ countable_data.total_orders }} -->
                        {{ animatedTotalOrders }}
                    </div>
                    <div class="icon yellow">
                        <i class="fa fa-shopping-basket"></i>
                    </div>
                </div>
            </div>            
            <div class="col-md-4">
                <div class="content_block">
                    <label>Total Cancel Orders</label>
                    <div v-if="pre_loader">
                        <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                    </div>
                    <div v-else class="total">
                        <!-- {{ countable_data.total_cancel_orders }} -->
                        {{ animatedTotalCancelOrders }}
                    </div>
                    <div class="icon orange">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="content_block">
                    <label>Total Expenses</label>
                    <div v-if="pre_loader">
                        <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="30" :r2_h="0" />
                    </div>
                    <div v-else class="total">
                        <PriceViewBlock :data="countable_data.total_expenses" :animate="true" />
                    </div>
                    <div class="icon violet">
                        <i class="fa fa-hand-holding-usd"></i>
                    </div>
                </div>
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
            total_orders: 0,
            total_cancel_orders: 0,
            tweened_total_orders: 0,
            tweened_total_cancel_orders: 0,
            countable_data: []
        }
    },
    computed: {
        animatedTotalOrders: function() {
            return this.tweened_total_orders.toFixed(0);
        },
        animatedTotalCancelOrders: function(){
            return this.tweened_total_cancel_orders.toFixed(0);
        }
    },
    watch: {
        total_orders: function(newValue) {
            this.$gsap.to(this.$data, { duration: 0.5, tweened_total_orders: newValue });
        },
        total_cancel_orders: function(newValue) {
            this.$gsap.to(this.$data, { duration: 0.5, tweened_total_cancel_orders: newValue });
        }
    },
    mounted (){
        this.get_data()
    },
    methods: {        
        get_data: function(){
            this.pre_loader = true
            this.$axios.get('/api/data-report/my-order-history', this.$parent.$parent.header_config).then( async (response) => {
                console.log('Get Data', response.data)
                this.countable_data = response.data.data
                this.total_orders = this.countable_data.total_orders
                this.total_cancel_orders = this.countable_data.total_cancel_orders
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
        padding: 25px; height: 125px;
        box-shadow: 0 0 10px #ddd;
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
</style>