<template>
    <div class="info_content_block">
        <div class="tab_items">
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <div :class="['tab_item',{active:sel_info_index==index}]" @click="sel_info_index=index">{{ item.product_info_type_data.type_title }}</div>
                </swiper-slide>
            </swiper>
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
            sel_info_index: 0,
            swiperOption: {
                slidesPerGroup: 1,
                centeredSlides: false,
                spaceBetween: 15,
                slidesPerView:'auto',
                visibilityFullFit: true,
                autoResize: false,
                freeMode: true,
                grabCursor: true
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    $line_height: 40;
    .tab_items{
        border-bottom: 2px solid #ccc;
        height: #{$line_height}px;
        .swiper-slide{
            width: 165px;
        }
        .tab_item{
            text-align: center;
            padding: 0px 15px;
            height: #{$line_height}px;
            line-height: #{$line_height}px;
            border-bottom: 2px solid #ccc;
            &:hover,&.active{
                color: $sys_brand_color;
                border-bottom-color: $sys_brand_color;
            }
        }
    }
    .tab_item_details{
        display: block;

        & > .content_block{
            overflow: auto;

            :deep(img){
                max-width: 100%;
                object-fit: contain;
            }
        }
    }
</style>
