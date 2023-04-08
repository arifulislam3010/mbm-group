<template>
    <div class="product_price_form_block">
        <div class="mb-4">            
            <div class="row">
                <div class="col-md-4">
                    <label>Choose Price Type</label>
                    <div class="mb-4">
                        <select v-model="$parent.price_arr[cur_index].index" class="form-control" @change="$parent.select_info_type(cur_index)">
                            <option value="">Select One</option>
                            <template v-if="$parent.product_info_types.length>0">
                                <option v-for="(item,index) in $parent.product_info_types" :key="index" :value="index">{{ item.title }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Price (TK)</label>
                    <div class="mb-4">
                        <input v-model="$parent.price_arr[cur_index].price" class="form-control" type="number" min="0" step="0.1" />
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Discount Price (TK)</label>
                    <div class="mb-4">
                        <input v-model="$parent.price_arr[cur_index].discount_price" class="form-control" type="number" min="0" step="0.1" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Remarks</label>
                    <div class="text_editor_block">
                        <textarea-autosize
                            placeholder="Enter price remarks"                    
                            v-model="$parent.price_arr[cur_index].remarks"
                            :min-height="30"
                            :max-height="150"                    
                        />
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Minimum Order</label>
                    <div class="mb-4">
                        <input v-model="$parent.price_arr[cur_index].min_qty" placeholder="E.g. 1" class="form-control" type="number" min="1" step="1" />
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Maximum Order</label>
                    <div class="mb-4">
                        <input v-model="$parent.price_arr[cur_index].max_qty" placeholder="E.g. 1000" class="form-control" type="number" min="1" step="1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ProductPriceFormBlock',
    props: ['data','cur_index']
}
</script>
<style lang="scss" scoped>
    .product_price_form_block{
        display: block;
        padding: 15px; background-color: #eee;
        border-bottom: 1px solid #ddd;
        transition: all 0.4s;
    }
    .text_editor_block{
        background-color: #fff;
        :deep(textarea){
            width: 100%; border: none; outline: none; padding: 10px;
        }
    }
</style>