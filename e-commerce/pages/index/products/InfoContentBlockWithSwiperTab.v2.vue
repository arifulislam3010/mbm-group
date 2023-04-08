<template>
    <div class="info_content_block">
        <div class="tab_items">
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <div :class="['tab_item',{active:sel_info_index==index}]" @click="sel_info_index=index">{{ item.product_info_type_data.type_title }}</div>
                </swiper-slide>
            </swiper>
        </div>
        <div class="tab_item_details">
            <!-- <div v-if="dtl_content.html" class="content_block" v-html="dtl_content.html"></div> -->
            <ContentViewBlock :data="dtl_content" />
        </div>
    </div>
</template>
<script>
import ContentViewBlock from './ContentViewBlock'
export default {
    name: 'InfoContentBlock',
    props: {
        data: Array
    },
    components: {
        ContentViewBlock
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
            },
            dtl_content: {}
        }
    },
    watch: {
        data: function(items){
            this.getHtmlWithCss()
        },
        sel_info_index: function(index){
            this.getHtmlWithCss()
        }
    },
    mounted(){
        this.getHtmlWithCss()
    },
    methods: {
        getHtmlWithCss: function(){            
            let v = this.data[this.sel_info_index]
            let getContent = typeof JSON.parse(v.content) == 'object'?JSON.parse(v.content):v.content
            
            console.log('',getContent)
            
            // let obj = {
            //     html: getContent.html?getContent.html:getContent,
            //     css: getContent.css?getContent.css:''
            // }

            // this.dtl_content = obj

            // if(this.dtl_content.css){
            //     const style = document.createElement('style')
            //     style.innerHTML = this.dtl_content.css
            //     document.head.appendChild(style)
            // }            

            this.dtl_content = getContent
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
        margin: 25px 0;

        & > .content_block{
            overflow: auto;

            :deep(img){
                max-width: 100%;
                object-fit: contain;
            }
        }
    }
</style>
