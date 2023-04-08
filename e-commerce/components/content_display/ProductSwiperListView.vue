<template>
    <div class="product_swiper_list_view_block">
        <div v-if="loader" class="row">            
            <div v-for="(n,i) in 4" :key="i" class="col-md-3">                
                <FormBlockLoader :cols="1" :height="165" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="150" :r2_h="0" />
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </div>
        <div v-else class="list">            
            <swiper :class="['swiper',{mobile:$device.isMobile}]" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" class="item mb-4" :key="index">
                    <MobileCardViewProduct :item="item" />                    
                </swiper-slide>
                <!-- <div class="swiper-pagination" slot="pagination"></div>
                <div class="swiper-button-prev" slot="button-prev"></div>
                <div class="swiper-button-next" slot="button-next"></div> -->
            </swiper>
        </div>
    </div>
</template>
<script>
import MobileCardViewProduct from './MobileCardViewProduct'
export default {
    name: 'ProductSwiperListViewBlock',
    props: ['loader','data'],
    components: {
        MobileCardViewProduct
    },
    data(){
        return {
            swiperOption: {
                // slidesPerView: 2,
                slidesPerGroup: 1,
                centeredSlides: false,                
                // loop: true,
                spaceBetween: 15,
                slidesPerView:'auto',
                visibilityFullFit: true,
                autoResize: false,
                freeMode: true,
                grabCursor: true
                // slidesPerGroup: 5,
                // loop: true,
                // loopFillGroupWithBlank: true,
                // pagination: {
                //     el: '.swiper-pagination',
                //     clickable: true
                // },
                // navigation: {
                //     nextEl: '.swiper-button-next',
                //     prevEl: '.swiper-button-prev'
                // }
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .swiper{
        .item{
            display: block;
            width: 250px;
            
            a{
                display: block;
            }
        }

        &.mobile{
            .item{
                width: 200px
            }        
        }
    }
</style>