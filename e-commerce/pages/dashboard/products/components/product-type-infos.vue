<template>
    <div class="get_product_type_list_block">
        <template v-for="(item,key) in data">
            <div v-if="item.product_type_data_info" :key="'label-' + key">
              <span><b>{{ item.product_type_data_info.type_title }} : </b></span>
              <template v-for="(info,index) in item.product_type_names_info">
                  <template v-if="index>0">,</template>
                  <template>{{ info.product_type_names_data_info.type_name }}</template>
              </template>
            </div>
        </template>
        <!-- <template v-for="(item,index) in cat_info">
            <template v-if="index>0">,</template>
            <template>{{ item.cat_data_info.category_name }}</template>
        </template> -->
    </div>
</template>
<script>
export default {
    name: 'GetProductTypeListBlock',
    props: ['data']
}
</script>
<style lang="scss" scoped>
    .get_product_type_list_block{
        display: block; font-size: 12px;

        & > div > span{
            display: inline-block;
            padding: 1px 0;
            margin: 1px 0;
        }
    }
</style>
