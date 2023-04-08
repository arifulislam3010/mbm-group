<template>
    <div class="info_content_block">
        <div class="tab_items">
            <carousel
                :paginationEnabled="false"
                :perPage="4"
                :perPageCustom="[[360, 2], [990, 4]]"
                :minSwipeDistance="8"
                :loop="true">
                <template v-for="(item,index) in data">
                    <slide v-if="item.product_info_type_data" :key="index">
                        <div :class="['tab_item',{active:sel_info_index==index}]" @click="sel_info_index=index">{{ item.product_info_type_data.type_title }}</div>
                    </slide>
                </template>
            </carousel>            
        </div>
        <div class="tab_item_details mt-3">
            <div class="content_block" v-html="data[sel_info_index].content"></div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'InfoContentBlock',
    props: {
        data: Array
    },
    data(){
        return {
            sel_info_index: 0
        }
    }
}
</script>
<style lang="scss" scoped>
    $line_height: 40;
    .info_content_block{
        .tab_items{
            display: flex;
            height: #{$line_height}px;
            gap: 30px;
            align-items: center;
            border-bottom: 1px solid #ddd;
            color: #999;
            .tab_item{
                line-height: #{$line_height}px;
                font-weight: 600;
                transition: all 0.4s;
                cursor: pointer;
                border-bottom: 1px solid #ccc;
                &:hover,&.active{
                    color: $sys_brand_color;
                    border-bottom: 3px solid $sys_brand_color;
                }
            }
        }

        .tab_item_details{
            display: flex;
            margin: 64px 0;
        }
    }
    .content_block{
        font-size: 18px;
        :deep(h2){      
            font-family: 'Aller Bold';
            font-size: 42px;
            line-height: 52px;
            margin-bottom: 20px;
            & > strong{
                font-weight: 800;
            }
        }
        :deep(h4){
            font-size: 22px;
            line-height: 32px;      
        }
    }
    strong{
        font-weight: 600;
    }
</style>
