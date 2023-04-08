<template>
    <div class="dashboard_block">
        <template v-if="logout_loader">
            <LogoutPopupView />
        </template>
        <div class="left_block">
            <!-- <div class="logo">
                <img src="~/assets/images/main-logo.png" />                
            </div> -->
            <div class="logo_block">
                <nuxt-link :to="{path: '/'}">
                    <Logo :type="'white'" />
                </nuxt-link>
            </div>
            <MainDashboardNav />
        </div><!--end .left_block-->
        <div class="right_block">
            <div class="header_block">
                <HeaderBlock :title="get_title()" />
            </div>
            <div class="content_block">
                <nuxt-child v-if="(role_access && role_access.view_others!==undefined) || title=='dashboard' || title=='dashboard-change-password'" :title="get_title()" />
            </div>
            <div class="footer_block">
                <FooterBlock />
            </div>
        </div><!--end .right_block-->        
    </div>
</template>
<script>
import Logo from '~/components/Logo'
import LogoutPopupView from '~/components/content_display/LogoutPopupView'
import MainDashboardNav from './main_dashboard_nav'
import HeaderBlock from './header'
import FooterBlock from './footer'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'DashboardBlock',
    components: {
        Logo,
        LogoutPopupView,
        MainDashboardNav,
        HeaderBlock,
        FooterBlock
    },
    // middleware: ['auth_access'],
    head () {
        return {            
            title: this.$route.name,
            cur_path: this.$route.path,
            meta: [
                { hid: 'description', name: 'description', content: 'Welcome to dashboard' }
            ]
        }
    },
    data(){
        return {
            logout_loader: false,
            title: this.$route.name,
            cur_path: this.$route.path,
            role_access: ''
        }
    },
    watch: {
        $route(to, from) {
            this.title = to.name
            this.cur_path = to.path
            this.entry_form_access()
        },
        get_feature_id: function(id){
            // console.log('Access List', this.$store.state.feature_access_list)
            this.role_access = this.$store.state.feature_access_list[id]
        },
        get_login_user_id: function(){}
    },
    computed: {
        get_login_user_id: function(){
            return this.$store.state.auth_info.user_data.id
        },
        get_feature_id: function(){
            let get_id = '';
            let get_obj = this.$store.state.dashboard_featured_list;
            for(let feature in get_obj){
                // console.log('Featuer', get_obj[feature]);
                for(let feature_id in get_obj[feature]){
                    // console.log('Featuer ID', get_obj[feature][feature_id]);
                    // console.log(this.$route.name,'Feature',get_obj[feature][feature_id].route)
                    if(this.$route.name==get_obj[feature][feature_id].route) return feature_id;
                }
            }

            return get_id;
        },
        loginStatus: function(){
            if(this.$store.state.auth_info.loggedIn) return true
            else return false
        }
    },
    mounted(){
        if(!this.loginStatus) this.$router.push('/')
        else if(this.$store.state.auth_info.user_data.role_info){            
            let user_role_access = this.$store.state.auth_info.user_data.role_info.role_accesses;

            this.user_role_access(user_role_access);
            this.feature_access_list();

            this.role_access = this.$store.state.feature_access_list[this.get_feature_id]
            // console.log('Access List', this.role_access)
        }else this.$router.push('/')
        this.entry_form_access()
    },
    methods: {
        ...mapActions({            
            userLogout: 'auth_info/LOGOUT_REQ',
        }),
        ...mapMutations({
            add_new_entity: 'ADD_NEW_STATUS',
            user_role_access: 'USER_ROLE_ACCESS',
            feature_access_list: 'FEATURE_ACCESS_LIST'
        }),
        get_title: function(){
            return this.title.replace(this.$store.state.dashboard.prefix + '-','').replace(/-/g,' ');
        },
        entry_form_access: function(){
            if(this.$route.query.action=='new' || this.$route.query.action=='clone' || (this.$route.query.action=='edit' && this.$route.query.id!==undefined)) this.add_new_entity(true)
            else this.add_new_entity(false)
        },
        edit_route: function(id){            
            this.$router.push(this.cur_path + '?action=edit&id=' + id)
        },
        clone_route: function(id){
            this.$router.push(this.cur_path + '?action=clone&id=' + id)
        },
        async user_logout(){
            this.logout_loader = true;
            localStorage.removeItem('user_info');
            await this.userLogout(this.$store.state.oauth_token.data)
            this.$router.push('/');
            this.logout_loader = false

            this.$notify.success({
                title: 'Logout',
                message: 'Successfully Done',
                position: 'bottomRight',
                transitionIn: 'bounceInRight',
                transitionOut: 'fadeOutLeft',
                timeout: 1500
            })
        }
    }
}
</script>
<style lang="scss" scoped>
    .dashboard_block{
        display: block;        
    }    
    .left_block{
        position: fixed;
        top: 0; left: 0;
        width: 240px; height: 100%;
        background-color: #18202e;        
    }
    .right_block{
        position: relative;
        top: 0; left: 240px; width: calc(100% - 240px);
        height: 100%; background-color: #f5f5f5;
    }
    .header_block{
        position: fixed;
        top: 0; left: 240px; width: calc(100% - 240px); padding: 0 24px;
        height: 60px; line-height: 60px; border-bottom: 1px solid #ddd;
        background-color: #fff;
        box-shadow: rgba(40, 42, 49, 0.16) 0px 1px 2px 0px;
        z-index: 99;
    }
    .footer_block{
        position: fixed;
        bottom: 0; left: 240px; width: calc(100% - 240px); padding: 0 24px;
        height: 40px; line-height: 40px; border-top: 1px solid #ddd;
        background-color: #fff;
        /* box-shadow: rgba(40, 42, 49, 0.16) 0px 1px 2px 0px; */
        z-index: 99;
    }
    .content_block{
        display: block;
        position: relative;
        margin-top: 60px;
        margin-bottom: 30px;
        padding: 24px;
    }
    .left_block :deep(.logo_block){
        width: 100%;
        height: 60px;
        padding: 5px 15px;
        background-color: #00344d;
        /* background-color: #add8e6; */
    }
    .left_block :deep(.logo){
        height: 100%
    }
    .logo > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>