<template>
    <div class="order_details_receipt_info_block">
        <!-- {{ $parent.$parent.sel_order_index }} -->
        <template v-for="(item,index) in data">
            <div v-if="item.id" :id="'item-' + index" :key="'ci-' + index" class="item">
                <ItemViewBlock :item="item" :index="index" />
            </div>
        </template>
    </div>
</template>
<script>
import ItemViewBlock from "./ItemViewBlock"
export default {
    name: 'OrderDetailsReceiptInfoBlock',
    props: ['data'],
    components: {
        ItemViewBlock
    }
}
</script>
<style scoped>
    .item{
        display: block;
        margin-bottom: 12px;
    }
    .item:last-child{
        margin-bottom: 5px;
    }
</style>