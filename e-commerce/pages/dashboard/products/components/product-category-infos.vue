<template>
    <div class="get_category_list_block">
        <span><b>Categories : </b></span>
        <template v-for="(item,index) in cat_info">
            <template v-if="index>0">,</template>
            <template v-if="item.cat_data_info && item.cat_data_info.category_name">{{ item.cat_data_info.category_name }}</template>
        </template>
    </div>
</template>
<script>
export default {
    name: 'GetCategoryListBlock',
    props: ['cat_info']
}
</script>
<style scoped>
    .get_category_list_block{
        display: block; font-size: 12px;
    }
    .get_category_list_block > span{
        display: inline-block;
        padding: 1px 0;
        margin: 1px 0;        
    }
</style>