<template>
    <div class="login_user_info">
        <div class="img" @click="getDashboardNavigation">
            <img v-if="$store.state.auth_info.user_data.user_info.icon!==undefined" :src="$imageUrl('user-profile-icon' + '/' + $store.state.auth_info.user_data.user_info.icon)" />
            <img v-else-if="$store.state.auth_info.user_data.user_info.profile_photo!==undefined" :src="$imageUrl('user-profile-photo' + '/' + $store.state.auth_info.user_data.user_info.profile_photo)" />            
            <Gravatar v-else :email="$store.state.auth_info.user_data.email" :size="40" />
        </div>
        <div class="user_info">            
            <div v-if="$store.state.auth_info.user_data" class="user_name" @click="getDashboardNavigation">
                <span v-if="$store.state.auth_info.user_data.user_info.full_name" class=" text_overflow max_one_line_allow">{{ $store.state.auth_info.user_data.user_info.full_name }}</span>
                <span v-else>{{ $store.state.auth_info.user_data.mobile }}</span>
            </div>
            <div class="action_btn">
                <span v-if="$store.state.auth_info.user_data.user_type=='1'" class="dashboard_btn"><a :href="base_url + '/' + this.$store.state.dashboard.prefix">Go Dashboard</a></span>
                <span v-if="$store.state.auth_info.user_data.user_type=='1'" class="logout_btn_short" @click="$parent.user_logout"><i class="fa fa-power-off"></i></span>
                <span v-else class="logout_btn" @click="$parent.user_logout">Logout</span>
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
    methods: {
        getDashboardNavigation: function(){
            if(this.$store.state.auth_info.user_data.user_type!==this.$store.state.admin_user_type_id) this.$parent.open_navigation=true
        }
    }
}
</script>
<style lang="scss" scoped>
    .login_user_info{
        display: flex;
        align-self: center;
        .img{
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
            :deep(img){
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            :deep(i){
                font-size: 30px;
                color: #ccc;
                line-height: 50px;
            }
        }
        .user_info{
            align-self: center;
            width: calc(100% - 50px);
            margin-left: 10px;
            height: 40px;     
            .user_name{
                display: block;
                min-width: 115px;
                font-size: 12px;
                color: #19475e;
                text-align: left;
                cursor: pointer;
                transition: all 0.4s;
                &:hover{
                    color: #5e195e;
                }
            }
            .action_btn{
                display: flex; height: 18px; margin-top: 2px; text-align: left;
                & > span{
                    align-self: center;
                }
                & > .dashboard_btn > a,
                & > .logout_btn{
                    display: inline-block; background-color: darkorange; color: #fff;
                    padding: 1px 10px; font-size: 10px; border-radius: 25px; transition: all 0.4s;
                }
                & > .dashboard_btn > a{
                    background-color: #006699;
                    &:hover{
                        background-color: #007bcd; cursor: pointer;
                    }
                }
                & > .logout_btn:hover{
                    background-color: #CD0000; cursor: pointer;
                }
            }
        }
    }
    .logout_btn_short{
        font-size: 12px; margin-left: 5px; color: #d64f00; cursor: pointer; transition: all 0.4s;
        &:hover{
            color: #CD0000; font-size: 13px;
        }
    }
</style>