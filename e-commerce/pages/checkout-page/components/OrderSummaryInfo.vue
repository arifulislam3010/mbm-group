<template>
    <div class="order_summary_info_block">
        <div class="fee_info">
            <span>Subtotal ({{ $store.state.cart_info.total_cart_items }} items)</span>
            <span class="amount"><PriceViewBlock :data="$store.state.cart_info.total_cart_amount.toFixed(2)" /></span>
        </div>
        <template v-if="$store.state.cart_info.total_cart_items>0">
            <div class="fee_info mt-2">
                <span>VAT ({{ $parent.vat_amount }}%)</span>
                <span class="amount"><PriceViewBlock :data="total_vat.toFixed(2)" /></span>
            </div>
            <!-- <div class="discount_info mt-2">
                <span>Discount</span>
                <span class="amount"><PriceViewBlock :data="'-' + delivery_fee.toFixed(2)" /></span>
            </div> -->
            <div v-if="$parent.delivery_fee_loader" class="fee_info mt-2">
                <FormBlockLoader :cols="1" :height="25" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="20" :r2_h="0" />
            </div>
            <div v-else-if="$parent.delivery_fee" class="fee_info mt-2">
                <span>Delivery fee</span>
                <span class="amount"><PriceViewBlock :data="$parent.delivery_fee.toFixed(2)" /></span>
            </div>
            <hr style="border: 1px dashed #ddd" />
            <div class="fee_info total_info">
                <span>Total</span>
                <span class="amount">
                    <PriceViewBlock :data="($store.state.cart_info.total_cart_amount + total_vat + $parent.discount + $parent.delivery_fee).toFixed(2)" />
                </span>
            </div>
            <div align="right" class="pr-3"><small>*Total price can be change</small></div>
        </template>
        <template v-else>
            <hr style="border: 1px dashed #ddd" />
            <div class="fee_info total_info">
                <span>Total</span>
                <span class="amount">
                    <PriceViewBlock :data="'0.00'" />
                </span>
            </div>
        </template>
    </div>
</template>
<script>
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
export default {
    name: 'OrderSummaryInfoBlock',
    components: {
        PriceViewBlock
    },
    computed: {
        total_vat: function(){
            return (this.$store.state.cart_info.total_cart_amount * this.$parent.vat_amount)/100
        }
    }
}
</script>
<style scoped>
    .fee_info{
        display: flex;
        padding: 0 15px;        
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
    .total_info > span.amount{
        font-size: 22px;
        color: #cd5200;
    }
</style>