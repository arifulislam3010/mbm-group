<template>
    <div class="order_summary_info_block">
        <div class="fee_info">
            <span>Subtotal ({{ $parent.data.order_items_info.length }} items)</span>
            <span class="amount"><PriceViewBlock :data="$parent.data.total_amount.toFixed(2)" /></span>
        </div>        
        <div class="fee_info mt-2">
            <span>VAT ({{ $parent.data.vat_amount }}%)</span>
            <span class="amount"><PriceViewBlock :data="(($parent.data.total_amount * $parent.data.vat_amount)/100).toFixed(2)" /></span>
        </div>
        <div class="fee_info discount_fee mt-2">
            <span>Discount</span>
            <span class="amount">- <PriceViewBlock :data="$parent.data.discount.toFixed(2)" /></span>
        </div>
        <div class="fee_info mt-2">
            <span>Delivery fee</span>
            <span class="amount"><PriceViewBlock :data="$parent.data.delivery_fee.toFixed(2)" /></span>
        </div>
        <hr style="border: 1px dashed #ddd" />
        <div class="fee_info total_info">
            <span class="label">Total</span>
            <span class="amount">
                <i v-if="$parent.data.choose_payment_type==1" class="fa fa-hand-holding-usd mr-2 cash_payment"></i>
                <i v-if="$parent.data.choose_payment_type==2" class="fa fa-credit-card mr-2 digital_payment"></i>
                <PriceViewBlock :data="$parent.data.total_payable.toFixed(2)" />
            </span>
        </div>
    </div>
</template>
<script>
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
export default {
    name: 'OrderSummaryInfoBlock',
    components: {
        PriceViewBlock
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