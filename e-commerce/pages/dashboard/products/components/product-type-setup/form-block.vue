<template>
    <div class="product_type_form_block">
        <div class="row">
            <div class="col-md-12">
                <label>Choose Product Type</label>
                <div class="mb-3">
                    <select v-model="$parent.$parent.product_type_arr[cur_index].id" class="form-control" @change="$parent.$parent.select_info_type(cur_index)">
                        <option value="">Select One</option>
                        <template v-if="Object.keys($parent.$parent.product_info_types).length>0">
                            <option v-for="(item,index) in $parent.$parent.product_info_types" :key="index" :value="item.id">{{ item.title }}</option>
                        </template>
                    </select>
                </div>
            </div>
            <div v-if="$parent.$parent.product_type_arr[cur_index].id > 0" class="col-md-12">
                <label>Choose Type<template v-if="$parent.$parent.product_type_arr[cur_index].open_entry"> or Enter Name</template></label>
                <div>
                    <ProductTypeNameManagement :cur_index="cur_index" :product_type_id="$parent.$parent.product_type_arr[cur_index].id" :product_open_entry="$parent.$parent.product_type_arr[cur_index].open_entry" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ProductTypeNameManagement from './ProductTypeNameManagement'
export default {
    name: 'ProductTypeFormBlock',
    props: ['cur_index'],
    components: {
        ProductTypeNameManagement,
    }
}
</script>
<style lang="scss" scoped>
    .product_type_form_block{
        display: block;
        padding: 15px; background-color: $light_ash;
        border-bottom: 1px solid $default_border_color;
        transition: all 0.4s;

        .text_editor_block{
            background-color: $white;
        }
    }
</style>
