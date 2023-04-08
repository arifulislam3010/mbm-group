<template>
    <div class="info_content_block">
        <div class="tab_items">
            <template v-for="(item,index) in data">
                <div v-if="item.product_info_type_data" :key="index" :class="['tab_item',{active:sel_info_index==index}]" @click="sel_info_index=index">{{ item.product_info_type_data.type_title }}</div>
            </template>
        </div>
        <div class="tab_item_details">
            <div class="content_block">
              <div class="row">
                <div class="col-md-6" v-html="data[sel_info_index].content"></div>
              </div>
            </div>
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
            font-size: 42px;
            line-height: 52px;
            margin-bottom: 20px;
            & > strong{
                font-weight: 800;
            }
        }
    }

    strong{
        font-weight: 600;
    }
</style>
