<template>
    <div class="content_footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="label">
                        <span class="title">Menu</span>
                        <span class="bar"></span>
                    </div>
                    <ul v-if="$store.state.static_page_info.get_data.length>0" class="mt-3">
                        <!-- <li v-for="(item,index) in $store.state.footer_nav_list" :key="'fnav-' + index">
                            <nuxt-link
                                :to="{ path: item.path }"
                                :class="['nav-item',{active:current_route==item.route}]">                                
                                <span v-html="item.name"></span>
                            </nuxt-link>
                        </li> -->
                        <template v-for="(static_item,static_index) in $store.state.static_page_info.get_data">
                            <template v-if="static_item.display_on==3">
                                <li :key="static_index">
                                    <nuxt-link :to="{path: '/page/' + static_item.slug}">{{ static_item.page_title }}</nuxt-link>
                                </li>
                            </template>
                        </template>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="label">
                        <span class="title">Contact Us</span>
                        <span class="bar"></span>
                    </div>

                    <div class="contact_info mt-3">
                        <div v-html="$store.state.site_basic_config_data.printer_line_txt"></div>
                        <!-- <div>House-171, Lane-1, Baridhara DOHS, Dhaka - 1206</div>
                        <div>Mobile: +880 1716******, +880 1673******</div>
                        <div>E-mail: <a href="mailto:support@orangebd.com">support@orangebd.com</a></div> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="label">
                        <span class="title">Connect With</span>
                        <span class="bar"></span>
                    </div>

                    <div class="connect_info mt-3">
                        <div class="social_links">
                            <a v-if="$store.state.site_basic_config_data.fb_page" :href="$store.state.site_basic_config_data.fb_page" target="_blank"><i class="fab fa-facebook"></i></a>
                            <a v-if="$store.state.site_basic_config_data.twitter_page" :href="$store.state.site_basic_config_data.twitter_page" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a v-if="$store.state.site_basic_config_data.linkedin_page" :href="$store.state.site_basic_config_data.linkedin_page" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            <a v-if="$store.state.site_basic_config_data.instagram_page" :href="$store.state.site_basic_config_data.instagram_page" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a v-if="$store.state.site_basic_config_data.youtube_page" :href="$store.state.site_basic_config_data.youtube_page" target="_blank"><i class="fab fa-youtube"></i></a>
                            <!-- <a href="#"><i class="fab fa-google-plus"></i></a> -->
                        </div>
                        <div class="app_btn clearfix">
                            <a v-if="$store.state.site_basic_config_data.ios_app_store" :href="$store.state.site_basic_config_data.ios_app_store" target="_blank" class="float-left mt-2 mr-3"><img src="@/assets/images/app-store-btn.png" title="IOS Store Button" alt="IOS Store Button" /></a>
                            <a v-if="$store.state.site_basic_config_data.android_play_store" :href="$store.state.site_basic_config_data.android_play_store" target="_blank" class="float-left mt-2"><img src="@/assets/images/google-play-btn.png" title="Google Play Button" alt="Google Play Button" /></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ContentFooterBlock',
    props: ['current_route']
}
</script>
<style lang="scss" scoped>
    .content_footer{
        display: block; padding: 50px 0;        
        background-image: linear-gradient(to right, $sys_brand_gcolor_1, $sys_brand_gcolor_2);
    }
    .content_footer .label{
        line-height: 18px;
    }
    .content_footer .label > .title{
        display: block;
        font-size: 18px; font-weight: normal;
        color: #eee
    }
    .content_footer .label > .bar{
        display: inline-block;
        width: 50px; height: 2px;
        background-color: #ffffff;
    }
    .content_footer ul{        
        list-style: none; padding: 0;
        & > li :deep(a){
            display: inline-block;
            margin: 5px 0;
            color: #ffffff;
            a:hover{
                color: #fffd89
            }
        }
    }
    .contact_info :deep(p){
        display: block;
        margin: 5px 0;
        color: #ffffff;
        & > a{
            color: #eee;
            &:hover{
                color: #fffd89;
            }
        }
    }
    .social_links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        height: 26px;
        align-items: center;
        & > a{
            // display: inline-block;
            font-size: 22px;
            color: #ffffff;
            // margin: 0 5px;
        }
    }
    .app_btn > a{
        display: inline-block;
        width: 100px;
        height: 30px;
        img{
            width: 100%; height: 100%;
            object-fit: contain;
        }
    }
</style>