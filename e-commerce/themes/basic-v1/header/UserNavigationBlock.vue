<template>
    <div class="user_navigation_block">
        <div class="black_mask_overlay" @click="$parent.close_navigation_block"></div>
        <div class="content clearfix">
            <div class="user_info_header">
                <div class="img">
                    <template v-if="$store.state.auth_info.user_data.user_info && $store.state.auth_info.user_data.user_info.photo && $store.state.auth_info.user_data.user_info.photo!==undefined">
                        <img :src="getUserPhoto()" />
                    </template>
                    <img v-else-if="$store.state.auth_info.user_data.user_info && $store.state.auth_info.user_data.user_info.profile_photo!==undefined" :src="$imageUrl('user-profile-photo' + '/' + $store.state.auth_info.user_data.user_info.profile_photo)" />
                    <Gravatar v-else :email="$store.state.auth_info.user_data.email" />
                </div>
                <div class="info">
                    <div><small>Welcome</small></div>
                    <div v-if="$store.state.auth_info.user_data" class="user_name" @click="$parent.open_navigation=true">{{ $store.state.auth_info.user_data.mobile }}</div>
                    <div class="action_btn">
                        <nuxt-link :to="{path: '/' + $store.state.user_dashboard_path}">Go to Dashboard</nuxt-link>
                    </div>
                </div>
            </div>
            <div class="nav_items">
                <template v-for="(item,index) in $store.state.user_dashboard_nav_list">
                    <template v-if="item.allow || item.allow_user_type==$store.state.auth_info.user_data.user_type">                    
                        <nuxt-link :key="index" :to="{path: item.path}">
                            <span class="icon" v-html="item.icon"></span>
                            <span class="title">{{ item.name }}</span>
                        </nuxt-link>
                    </template>
                </template>
            </div>
            <div class="footer_content_info" @click="$parent.user_logout()">                
                <i class="fa fa-power-off"></i>
                <span>Logout</span>                
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'UserNavigationBlock',
    mounted(){
        document.body.classList.add('popup_open')
    },
    methods: {
        getUserPhoto: function(){            
            if(this.$store.state.auth_info.user_data.user_info.photo.search('http://')==0 || this.$store.state.auth_info.user_data.user_info.photo.search('https://')==0) return this.$store.state.auth_info.user_data.user_info.photo
            else return this.$imageUrl('user-profile-photo' + '/' + $store.state.auth_info.user_data.user_info.photo)            
        }
    }
}
</script>
<style lang="scss" scoped>
    .user_navigation_block{
        display: block;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content{
        position: fixed;
        background-color: #fff;
        width: 300px;
        height: 100%;
        overflow-y: auto;
        right: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
    }
    .user_info_header{
        display: flex;
        height: 80px; padding: 0 15px;
        border-bottom: 1px solid #ddd;
    }
    .user_info_header .img{
        width: 60px;
        height: 60px;
        line-height: 58px;
        text-align: center;
        align-self: center;        
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
    }
    .user_info_header .img :deep(img){
        width: 100%;
        height: 100%;
        object-fit: contain;
        i{
            font-size: 50px;
            color: #ccc;
            line-height: 80px;
        }
    }
    .user_info_header .info{
        align-self: center;
        width: calc(100% - 70px);
        margin-left: 10px;        
    }
    .user_info_header .info .user_name{
        display: block;        
        font-size: 12px;
        color: #19475e;
        text-align: left;
        cursor: pointer;
        transition: all 0.4s;
    }
    .user_info_header .info .user_name:hover{
        color: $sys_brand_color
    }
    .user_info_header .info .action_btn{
        display: block; text-align: left
    }
    .user_info_header .info .action_btn :deep(a){
        display: inline-block; background-color: darkorange; color: #fff;
        padding: 1px 10px; font-size: 10px; border-radius: 25px; transition: all 0.4s;
        &:hover{
            background-color: #CD0000; cursor: pointer;
        }
    }
    .nav_items :deep(a),
    .footer_content_info{
        display: flex;
        height: 46px; padding: 0 15px;
        cursor: pointer; border-bottom: 1px solid #eee;
        transition: all 0.4s;
    }    
    .nav_items :deep(a):hover,
    .footer_content_info:hover{
        background: #eee
    }
    .nav_items :deep(a) i,
    .footer_content_info > i{
        align-self: center;
        width: 30px;
        height: 30px;
        color: #bababa;
        font-size: 20px;
        /* border: 1px solid #ddd; */
        border-radius: 50%;
        text-align: center;
        line-height: 28px;
    }
    .nav_items :deep(a) span.icon{
        align-self: center;
    }
    .nav_items :deep(a) span.title,
    .footer_content_info > span{
        align-self: center;
        margin-left: 10px;
        text-align: left; 
        color: #666;       
    }
</style>