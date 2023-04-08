<template>
    <div :class="['banner_block',{mobile_device:$device.isMobile}]">
        <div class="background-mask"></div>        
        <template v-if="$device.isMobile">
            <template v-if="pre_loader">
                <div class="banner_img_block">
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                </div>
            </template>
            <template v-else-if="data.length>0">
                <swiper class="swiper" :options="swiperOption">
                    <template v-for="(banner,index) in data">
                        <template v-if="banner.display_type=='1'">
                            <swiper-slide :key="index">
                                <div class="banner_img_block">
                                    <!-- <div class="background-img"></div> -->
                                    <nuxt-link v-if="banner.promotional_link" :key="index" :to="{path: banner.promotional_link }">
                                        <img :src="banner.mobile_banner_image" :title="banner.banner_title" :alt="banner.banner" />
                                    </nuxt-link>
                                    <img v-else :key="index" :src="banner.mobile_banner_image" :title="banner.banner_title" :alt="banner.banner_title" />
                                </div>
                            </swiper-slide>
                        </template>
                    </template>
                    <div class="swiper-pagination"></div>
                </swiper>
            </template>
        </template>
        <template v-else>
            <template v-if="pre_loader">
                <div class="banner_img_block">
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                </div>
            </template>
            <carousel v-else
                :paginationActiveColor="'#f05a24'"
                :paginationColor="'#CCC'"
                :paginationPadding="3"
                :paginationPosition="'top-overlay'"
                :perPage="1"
                :minSwipeDistance="16"
                :loop="true">
                <template v-for="(banner,index) in data">
                    <template v-if="banner.display_type=='1'">
                        <slide :key="index">
                            <div class="banner_img_block">
                                <!-- <div class="background-img"></div> -->
                                <nuxt-link v-if="banner.promotional_link" :key="index" :to="{path: banner.promotional_link }">
                                    <img :src="banner.desktop_banner_image" :title="banner.banner_title" :alt="banner.banner_title" />
                                </nuxt-link>
                                <img v-else :key="index" :src="banner.desktop_banner_image" :title="banner.banner_title" :alt="banner.banner_title" />
                            </div>
                        </slide>
                    </template>
                </template>
            </carousel>
        </template>
    </div>
</template>
<script>
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'BannerBlock',
    data(){
        return {
            swiperOption: {                
                slidesPerView: 1,
                centeredSlides: true,
                grabCursor: true,
                loop: true,
                spaceBetween: 15,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                }
            },
            pre_loader: false,
            data: [],
            user_access_token: this.$store.state.oauth_token.token,
            limit: 8
        }
    },
    mounted(){
        if($nuxt.isOnline){
            if(this.$store.state.promotional_banner_info.get_data.length==0) this.load_data();
            else this.data = this.$store.state.promotional_banner_info.get_data;
        }else{
            let getCacheData = localStorage.getItem('banner_list')
            this.data = JSON.parse(getCacheData)
        }
    },
    methods: {
        ...mapActions({        
            getPromotionalBanners: 'promotional_banner_info/GET_DATA'
        }),
        async load_data(){
            // setup submitted data
            let config_data = {                
                access_token: this.user_access_token,
                limit: this.limit                
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getPromotionalBanners(config_data)

            // get products list
            this.data = this.$store.state.promotional_banner_info.get_data

            // cache store
            localStorage.setItem('banner_list', JSON.stringify(this.data))

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style lang="scss" scoped>
    .banner_block{
        display: block;
        position: relative;
        z-index: 99;
        & > .background-mask{
            position: absolute;
            width: 100%;
            height: 30%;
            bottom: 0;
            background-image: linear-gradient(to top, #00000080, transparent);
            z-index: 5;        
        }
        &.mobile_device{
            & > .background-mask{
                display: none;
            }
            :deep(.banner_img_block){
                height: 120px;
            }
        }
        :deep(.banner_img_block){
            position: relative;
            width: 100%;
            height: 375px;
        }
        :deep(.background-img){
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url(.~assets/images/main-banner.jpg);
            background-position: bottom center;
            background-size: cover;
        }
        :deep(img){
            position: absolute;
            width: 100%;
            height: 100%;        
            object-fit: cover;
        }
    }
    .swiper-slide {
        display: flex;
        width: 60%;        
        justify-content: center;
        align-items: center;
    }    
</style>