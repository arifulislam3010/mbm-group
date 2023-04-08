<template>
    <div class="order_summary_info_block">
        <!-- {{ $store.state.order_info.sel_data }} -->
        <div class="fee_info">
            <span>Subtotal ({{ data.order_items_info.length }} items)</span>
            <span class="amount"><PriceViewBlock :data="data.total_amount.toFixed(2)" /></span>
        </div>        
        <div class="fee_info mt-2">
            <span>VAT ({{ data.vat_amount }}%)</span>
            <span class="amount"><PriceViewBlock :data="((data.total_amount * data.vat_amount)/100).toFixed(2)" /></span>
        </div>
        <div class="fee_info discount_fee mt-2">
            <span>Discount</span>
            <span class="amount">- 
                <template v-if="$parent.edit_receipt"><SymbolBlock /> <input type="number" min="0" v-model="discount" @keyup="discountSubmit()" @change="discountSubmit()" /></template>
                <template v-else><PriceViewBlock :data="data.discount.toFixed(2)" /></template>
            </span>
        </div>
        <div class="fee_info mt-2">
            <span>Delivery fee</span>
            <span class="amount">
                <template v-if="$parent.edit_receipt"><SymbolBlock /> <input type="number" min="0" v-model="delivery_fee" @keyup="deliveryFeeSubmit()" @change="deliveryFeeSubmit()" /></template>
                <template v-else><PriceViewBlock :data="data.delivery_fee.toFixed(2)" /></template>
            </span>
        </div>
        <hr style="border: 1px dashed #ddd" />
        <div class="fee_info total_info">
            <span class="label">Total</span>
            <span class="amount">
                <i v-if="data.choose_payment_type==1" class="fa fa-hand-holding-usd mr-2 cash_payment"></i>
                <i v-if="data.choose_payment_type==2" class="fa fa-credit-card mr-2 digital_payment"></i>
                <PriceViewBlock :data="data.total_payable.toFixed(2)" />
            </span>
        </div>
    </div>
</template>
<script>
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import SymbolBlock from '@/components/content_display/price/SymbolBlock'
import { mapMutations } from 'vuex'
export default {
    name: 'OrderSummaryInfoBlock',
    props: ['data'],
    components: {
        PriceViewBlock,
        SymbolBlock
    },
    data(){
        return {
            discount: this.data.discount,
            delivery_fee: this.data.delivery_fee
        }
    },
    methods: {
        ...mapMutations({
            selOrderData: 'order_info/SEL_DATA',
            reconfigOrderData: 'order_info/RECONFIG_DATA'
        }),
        discountSubmit: function(){
            console.log(this.discount)
            let obj = {
                'field': 'discount',                
                'value': this.discount
            }
            this.reconfigOrderData(obj)
        },
        deliveryFeeSubmit: function(){
            console.log(this.delivery_fee)
            let obj = {
                'field': 'delivery_fee',                
                'value': this.delivery_fee
            }
            this.reconfigOrderData(obj)
        }
    }
}
</script>
<style scoped>
    .fee_info{
        display: flex;
    }
    .fee_info > span{
        align-self: center;
        font-size: 12px;
        color: #666;
    }
    .fee_info > span > input{
        display: inline-block;
        width: 50px;
        height: 20px;
        font-size: 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding-left: 5px;
    }
    .fee_info > span.amount{
        margin-left: auto;
        font-size: 14px;
        color: #333;
    }
    .discount_fee > span.amount{
        color: #CD0000
    }
    .total_info > span.label{
        font-size: 16px; font-weight: bold;
    }
    .total_info > span.amount{
        font-size: 20px;
        color: #333;
    }
    .total_info > span.amount > i.cash_payment{
        color: #a54077
    }
    .total_info > span.amount > i.digital_payment{
        color: #439ac6
    }    
</style>