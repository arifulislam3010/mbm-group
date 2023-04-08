<template>
    <div class="get_product_list_block">
        <span><b>Product Type Names : </b></span>
        <template v-for="(item,index) in product_type_name_info">
            <template v-if="index>0">,</template>
            <template>{{ item.product_type_name_data.type_name }}</template>
        </template>
    </div>
</template>
<script>
export default {
    name: 'GetProductTypeNameListBlock',
    props: ['product_type_name_info']
}
</script>
<style scoped>
    .get_product_list_block{
        display: block; font-size: 12px;
    }
    .get_product_list_block > span{
        display: inline-block;
        padding: 1px 0;
        margin: 1px 0;
    }
</style>