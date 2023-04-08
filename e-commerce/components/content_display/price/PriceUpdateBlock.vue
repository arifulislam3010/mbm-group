<template>
    <div class="dynamic_quantity_block">
        <div class="price_info">
            <span><PriceViewBlock :data="item.price" /> x {{ quantity }} = <PriceViewBlock :data="(item.price * quantity).toFixed(2)" :animate="true" /></span>
        </div>
        <!-- <div class="quantity">
            <QuantityActionBlock :quantity="quantity" />
        </div> -->
    </div>
</template>
<script>
import PriceViewBlock from './ViewBlock'
import QuantityActionBlock from '../QuantityActionBlock'
export default {
    name: 'DynamicQuantityBlock',
    props: ['product_id','price_type_id','item'],
    components: {
        PriceViewBlock,
        QuantityActionBlock
    },
    data() {
        return {
            min_qty: this.item.min_qty,
            max_qty: this.item.max_qty,
            quantity: this.item.quantity
        }
    },
    methods: {
        async quantity_submit(val){
            if(val<this.min_qty || isNaN(val)) this.quantity = this.min_qty
            else if(this.max_qty && val>this.max_qty) this.quantity = this.max_qty
            else this.quantity = val

            let obj = {
                id: this.product_id,
                ptid: this.price_type_id,
                quantity: this.quantity
            }
            // console.log(obj)
            await this.$parent.update_cart_item_quantity(obj)

            this.$parent.total_cart_amount()
        }
    }
}
</script>
<style scoped>
    .dynamic_quantity_block{
        display: flex;
        align-self: center;
        height: 100%;
    }
    .dynamic_quantity_block .price_info{
        align-self: center; font-size: 12px; text-align: left; margin-right: 5px;
    }
    .dynamic_quantity_block .quantity{
        align-self: center;
        margin-left: auto;
        white-space: nowrap;
    }
</style>