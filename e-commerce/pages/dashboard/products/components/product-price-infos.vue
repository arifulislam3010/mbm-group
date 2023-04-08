<template>
    <div class="get_product_price_list_block">        
        <div v-for="(item,index) in price_info" :key="index">
            <div class="label"><b>{{ item.product_price_type_data.type_title }}: </b></div>
            <div class="mt-1">
                <div>Regular: <em>Tk.{{ item.price }}</em></div>
                <div v-if="item.discount_price">Discount: <em>Tk.{{ item.discount_price }}</em></div>
            </div>            
        </div>
    </div>
</template>
<script>
export default {
    name: 'GetProductPriceListBlock',
    props: ['price_info']
}
</script>
<style lang="scss" scoped>
    .get_product_price_list_block{
        display: block;
    }
    .get_product_price_list_block > div{
        display: block;
        padding: 1px 0;
        margin: 1px 0;
        font-size: 12px;

        & > .label{
            border-bottom: 1px solid #ccc;
        }
    }
</style>