<template>
    <div class="content_view_layout mt-4">
        <!-- {{ data }} -->
        <div v-for="(element,elm_index) in data" class="block relative element_layer_block" :key="elm_index">
            <div class="grid grid-cols-12 relative grid_cols_block">
                <div :class="['block','col-span-' + (column.size?column.size:6),'relative','rounded-md']" v-for="(column,col_index) in element.column" :key="col_index">
                    <template v-if="column.data.length > 0">
                        <div :class="['block','relative','content_layer_block']" v-for="(item,index) in column.data" :key="index">
                            <div v-if="item.type=='rich_txtarea' || item.type=='embed'" v-html="item.content"></div>
                            <div v-else-if="item.type=='image'">
                                <img :src="item.content" :alt="'image'" />
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ContentViewBlock',
    props: {
        data: Array
    },
}
</script>
<style lang="scss" scoped>
    .grid_cols_block{
        gap: 15px;

        .content_layer_block{
            margin-top: 15px;
            &:first-child{
                margin-top: 0;
            }
            :deep(p){
                margin: 0; padding: 0
            }

            img{
                width: 100%; height: 100%; object-fit: contain;
            }
        }
    }
</style>