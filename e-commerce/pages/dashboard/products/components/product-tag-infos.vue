<template>
    <div class="get_product_list_block">
        <span><b>Tags : </b></span>
        <template v-for="(item,index) in tag_info">
            <template v-if="index>0">,</template>
            <template>{{ item.tags_data_info.tag_title }}</template>
        </template>
    </div>
</template>
<script>
export default {
    name: 'GetProductListBlock',
    props: ['tag_info']
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