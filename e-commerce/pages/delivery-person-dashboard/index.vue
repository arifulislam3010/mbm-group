<template>
    <div class="user_dashboard_stat">
        <div v-if="loginStatus" class="container clearfix">
            <div v-if="!$device.isMobile" class="left-side float-left">
                <NavListBlock />
            </div>
            <div :class="['right-side',{'mobile_view':$device.isMobile},{'float-left':!$device.isMobile}]">
                <ContentWrapperBlock />
            </div>
        </div>
    </div>
</template>
<script>
import NavListBlock from './nav-list'
import ContentWrapperBlock from './content-wrapper'
export default {
    name: 'UserDashboardStat',
    components: {
        NavListBlock,
        ContentWrapperBlock
    },
    computed: {
        loginStatus: function(){
            if(this.$store.state.auth_info.loggedIn) return true
            else return false
        }
    },
    mounted(){
        if(!this.loginStatus){
            this.$notify.warning({
                title: 'Message',
                message: 'Unauthorized. Please login and try again.',
                timeout: 2500
            })

            this.$router.push('/')            
        }
    }
}
</script>
<style scoped>
    .left-side{
        position: relative;
        width: 250px;        
    }
    .right-side{
        position: relative;
        width: calc(100% - 250px);
        min-height: 350px;
        background-color: #fff;
        /* background-image: linear-gradient(90deg, #fff, transparent); */
        /* box-shadow: 0 15px 25px #ccc; */
        border-left: 1px solid #ddd;
    }
    .mobile_view{
        width: 100%;
        margin-top: 15px;
    }
</style>