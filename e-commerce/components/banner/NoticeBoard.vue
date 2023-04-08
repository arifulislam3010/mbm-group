<template>
    <div class="notice_board">
        <div class="notice_title">
            সর্বশেষ আপডেট
        </div>
        <div class="notice_cont">
            <ul class="pre_loading_block" v-if="pre_loader">
                <li v-for="(n,i) in 3" :key="i">
                    <div class="row">
                        <div class="col-md-3 pr-2">
                            <FormBlockLoader :cols="1" :height="280" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="280" :r2_h="0" />
                        </div>

                        <div class="col-md-9 pl-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="30" :r2_h="0" />
                                </div>

                                <div class="col-md-12">
                                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="20" :r2_h="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <marquee v-else width="100%" direction="up" onmouseover="this.stop();" onmouseout="this.start();" scrolldelay="300">                
                <ul>
                    <li v-for="(item,index) in $store.state.latest_content_info.data" :key="index">
                        <a :href="item.view_link_type=='1'?item.content_link:'/show-video-content/' + item.id" :target="item.view_link_type=='1'?'_blank':'_self'">
                            <div class="media">
                                <div class="left_media align-self-center mr-2 ">
                                    <!-- <img src="~/assets/images/10-Min-School.png" class="cover_photo img-fluid" alt="cover photo"> -->
                                    <img :src="$imageUrl('video-content/partner-'+ item.partner_info.id +'/'+ item.cover_photo_url)" class="cover_photo img-fluid" alt="cover photo" />
                                </div>
                                <div class="media-body">
                                    <h5 class="mb-1">{{ item.content_title }}</h5>
                                    <div class="bot_cont">
                                        <!-- <img src="~/assets/images/10-Min-School-logo.png" class="mr-2 d-inline" alt="partner"> -->
                                        <span class="logo">
                                            <img v-if="item.partner_info.icon!==null" :src="$imageUrl('user-profile-icon' + '/' + item.partner_info.icon)" class="mr-2 d-inline" :alt="item.partner_info.name" />
                                            <i v-else class="fa fa-university"></i>
                                        </span>
                                        <h6 class="d-inline mb-0">{{ item.partner_info.name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>                    
                </ul>
            </marquee>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
export default {
    name: 'NoticeBoard',
    data(){
        return {
            pre_loader: false,
            limit: 10
        }
    },
    mounted(){        
        if(this.$store.state.latest_content_info.data.length==0) this.load_content();
    },
    methods: {
        ...mapActions({
            latestContentList: 'latest_content_info/GET_DATA'
        }),
        async load_content(){
            let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));

            this.pre_loader = true

            // data request setup
            let request_data = {
                access_token: getTokenType.access_token,
                limit: this.limit
            }

            await this.latestContentList(request_data);

            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    .notice_board{
        margin-right: 20px;
        padding: 0px 11px 25px 11px;
        height: 235px;
        background: url('~assets/images/notice-bg.png') no-repeat;
        background-size: 100% 100%;
    }
    .notice_title{
        color: #892c85;
        padding: 5px 20px;
        cursor: pointer;
        display: block;
        margin: 0 auto;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #dddddd;
    }
    .notice_cont{ padding:0 10px 10px 10px; box-shadow: 0 0 15px #333;}
    .notice_cont ul.pre_loading_block{
        padding: 15px 5px;
        height: 175px;
    }
    .notice_cont ul{ margin:0; padding:0}
    .notice_cont ul li{
        list-style: none;
        margin-bottom: 7px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 7px;
    }
    .notice_cont ul li:last-child{ margin-bottom: 0px;}
    .notice_cont marquee{ height:170px}
    .notice_cont ul li b{ border-bottom:1px solid darkcyan; display: block;color:darkcyan;margin-bottom:6px; line-height: 24px;}
    .notice_cont ul li a{ color: purple;}
    .notice_cont ul li span{ display: block; margin-bottom:5px}
    .notice_cont ul li a:hover{ color:green}
    .notice_cont .media .left_media{ width: 60px; height: 40px; border: 1px solid #dddddd; border-radius: 3px; overflow: hidden;}
    .notice_cont .media .left_media .cover_photo{ width: 100%; height: 100%; object-fit: contain;}
    .notice_cont .media .media-body h5{ font-size: 14px; color: purple; line-height: 18px;}
    .notice_cont .media .media-body .bot_cont > span{ display:inline-block; width: 20px; height: 20px;}
    .notice_cont .media .media-body .bot_cont > span img{
        width: 100%; height: 100%; object-fit: contain;
    }
    .notice_cont .media .media-body .bot_cont h6{ font-size: 14px; color: #666;}
    @media (max-width: 575.98px) {
        .notice_board{
            margin: 10px 10px 15px 10px;
            padding: 0px 11px 25px 11px;
        }
    }
</style>