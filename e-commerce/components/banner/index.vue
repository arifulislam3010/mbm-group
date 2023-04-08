<template>
    <div id="banner">
        <div class="banner_full">
            <div class="container">
                <div class="d-none d-md-block">
                    <div class="row">
                        <div class="col-12 col-md-7 col-lg-8">
                            <Slogan />
                            <UserSelection v-if="$route.name=='index'" />
                        </div>
                        <div class="col-12 col-md-5 col-lg-4">
                            <!-- <NoticeBoard v-if="$device.isDesktop" /> -->
                            <div :class="['banner_bg_image','pt-3',{'inner_page':$route.name!=='index'}]">
                                <img src="~/assets/images/banners/banner-bg-image-01.png" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-block d-md-none">
                    <Slogan />
                    <div :class="['banner_bg_image','pt-0',{'inner_page':$route.name!=='index'}]">
                        <img src="~/assets/images/banners/banner-bg-image-01.png" />
                    </div>
                    <UserSelection v-if="$route.name=='index'" />
                    <!-- <NoticeBoard v-if="$device.isDesktop" /> -->
                </div>
            </div>
        </div>
        <div class="bottom_bg_overlay"></div>
    </div>
</template>
<script>
import Slogan from './Slogan'
import UserSelection from './UserSelection'
// import NoticeBoard from './NoticeBoard'
// import coronaAdviceBlock from '~/components/ads_banner/coronaAdviceBlock'
export default {
    name: "BannerBlock",
    components: {
        Slogan,
        UserSelection,
        // NoticeBoard,
        // coronaAdviceBlock
    }
}
</script>
<style lang="scss" scoped>
    .banner_full{ width: 100%; height: 100%; position:relative; left: 0px; top: 0px;}
    #banner{
        display: block;
        position: relative;
        /* background-color: #00b5a2; */
        background-color: #35ce64;
        /* background-image: linear-gradient(45deg, #35ce64, transparent); */
        background-image: url(.~assets/images/banners/banner-bg-full-image.png);
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center right;
        width: 100%; margin-top: 60px; padding-bottom: 50px;
        transition: all 0.4s;
    }
    #banner .banner_bg_image{
        display: inline-block;
        height: 270px;
        transition: all 0.4s;
    }
    #banner .banner_bg_image.inner_page{
        height: 130px;
    }
    #banner :deep(img){
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .bottom_bg_overlay{
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100%;
        height: 70px;
        background-image: url(.~assets/images/top-banner-footer-bg.png);
        background-repeat: repeat-x;
        background-size: contain;
        background-position: center;
    }
    @media (max-width: 480px) {
        #banner .banner_bg_image {
            height: auto;
        }
        .bottom_bg_overlay{ height:32px;}
        #banner{ padding-bottom: 15px;}
    }
    @media (min-width: 768px) {        
        .bottom_bg_overlay{ height:50px;}
    }
    @media (min-width: 1024px) {        
        .bottom_bg_overlay{ height:60px;}
    }
    @media (min-width: 1200px) {        
        .bottom_bg_overlay{ height:70px;}
    }
</style>