<template>
    <div class="login_user_info">
        <div class="img" @click="getDashboardNavigation">            
            <template v-if="$store.state.auth_info.user_data.user_info && $store.state.auth_info.user_data.user_info.photo && $store.state.auth_info.user_data.user_info.photo!==undefined">
                <img :src="getUserPhoto()" :title="profile_display_name" :alt="profile_display_name" />
            </template>
            <img v-else-if="$store.state.auth_info.user_data.user_info && $store.state.auth_info.user_data.user_info.profile_photo!==undefined" :src="$imageUrl('user-profile-photo' + '/' + $store.state.auth_info.user_data.user_info.profile_photo)" :title="profile_display_name" :alt="profile_display_name" />            
            <Gravatar v-else :email="$store.state.auth_info.user_data.email" :size="40" :title="profile_display_name" :alt="profile_display_name" />
        </div>
        <div class="user_info">            
            <div v-if="$store.state.auth_info.user_data" class="user_name" @click="getDashboardNavigation">
                <span class="text_overflow max_one_line_allow">{{ profile_display_name }}</span>
                <!-- <span v-if="$store.state.auth_info.user_data.user_info.full_name" class=" text_overflow max_one_line_allow">{{ $store.state.auth_info.user_data.user_info.full_name }}</span>
                <span v-else-if="$store.state.auth_info.user_data.mobile">{{ $store.state.auth_info.user_data.mobile }}</span>
                <span v-else>{{ $store.state.auth_info.user_data.email }}</span> -->
            </div>
            <div class="action_btn">
                <span v-if="$store.state.auth_info.user_data.user_type==$store.state.admin_user_type_id" class="dashboard_btn"><a :href="base_url + '/' + this.$store.state.dashboard.prefix">Go Dashboard</a></span>
                <span v-else class="dashboard_btn"><nuxt-link :to="{path: '/' + $store.state.user_dashboard_path}">Go to Dashboard</nuxt-link></span>
                <span class="logout_btn_short" @click="$parent.user_logout"><i class="fa fa-power-off"></i></span>
                <!-- <span v-if="$store.state.auth_info.user_data.user_type==$store.state.admin_user_type_id" class="logout_btn_short" @click="$parent.user_logout"><i class="fa fa-power-off"></i></span>
                <span v-else class="logout_btn" @click="$parent.user_logout">Logout</span> -->
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LoginUserInfoBlock',
    data(){
        return {
            base_url: process.env.BASE_URL
        }
    },
    computed: {
        profile_display_name: function(){
            if(this.$store.state.auth_info.user_data.user_info && this.$store.state.auth_info.user_data.user_info.full_name) return this.$store.state.auth_info.user_data.user_info.full_name
            else if(this.$store.state.auth_info.user_data.mobile) return this.$store.state.auth_info.user_data.mobile
            else return this.$store.state.auth_info.user_data.email
        }
    },
    methods: {
        getDashboardNavigation: function(){
            if(this.$store.state.auth_info.user_data.user_type!==this.$store.state.admin_user_type_id) this.$parent.open_navigation=true
        },
        getUserPhoto: function(){            
            if(this.$store.state.auth_info.user_data.user_info.photo.search('http://')==0 || this.$store.state.auth_info.user_data.user_info.photo.search('https://')==0) return this.$store.state.auth_info.user_data.user_info.photo
            else return this.$imageUrl('user-profile-photo' + '/' + $store.state.auth_info.user_data.user_info.photo)            
        }
    }
}
</script>
<style lang="scss" scoped>
    .login_user_info{
        display: flex;
        align-self: center;
    }
    .login_user_info .img{
        width: 40px;
        height: 40px;
        line-height: 38px;
        text-align: center;
        margin-left: 5px;
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
    }
    .login_user_info .img img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .login_user_info .img i{
        font-size: 30px;
        color: #ccc;
        line-height: 50px;
    }
    .login_user_info .user_info{
        align-self: center;
        width: calc(100% - 50px);
        margin-left: 10px;
        height: 40px;     
    }
    .login_user_info .user_info .user_name{
        display: block;
        min-width: 115px;
        font-size: 12px;
        color: $login_user_txt_color;
        text-align: left;
        cursor: pointer;
        transition: all 0.4s;
    }
    .login_user_info .user_info .user_name:hover{
        color: $login_user_txt_hcolor
    }
    .login_user_info .user_info .action_btn{
        display: flex; height: 18px; margin-top: 2px; text-align: left
    }
    .login_user_info .user_info .action_btn > span{
        align-self: center;
    }
    .login_user_info .user_info .action_btn > .dashboard_btn > a,
    .login_user_info .user_info .action_btn > .logout_btn{
        display: inline-block; background-color: $logout_btn_color; color: #fff;
        padding: 1px 10px; font-size: 10px; border-radius: 25px; transition: all 0.4s;
    }
    .logout_btn_short{
        font-size: 12px; margin-left: 5px; color: $logout_sbtn_color; cursor: pointer; transition: all 0.4s;
    }
    .logout_btn_short:hover{
        color: $logout_sbtn_hcolor; font-size: 13px;
    }
    .login_user_info .user_info .action_btn > .dashboard_btn > a{
        background-color: $user_dashboard_btn;
    }
    .login_user_info .user_info .action_btn > .dashboard_btn > a:hover{
        background-color: $user_dashboard_btn_hover; cursor: pointer;
    }
    .login_user_info .user_info .action_btn > .logout_btn:hover{
        background-color: $logout_btn_hcolor; cursor: pointer;
    }
</style>