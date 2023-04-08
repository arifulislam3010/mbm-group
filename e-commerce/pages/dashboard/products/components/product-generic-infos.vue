<template>
    <div class="get_product_list_block">
        <span><b>Generics : </b></span>
        <template v-for="(item,index) in generic_info">
            <template v-if="index>0">,</template>
            <template>{{ item.generics_data_info.generic_name }}</template>
        </template>
    </div>
</template>
<script>
export default {
    name: 'GetProductListBlock',
    props: ['generic_info']
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