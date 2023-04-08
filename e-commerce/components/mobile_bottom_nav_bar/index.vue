<template>
    <div :class="['mobile_bottom_nav_bar_block',{overlay_block:$store.state.blackOverlayFooter}]">
        <template v-if="logout_loader">
            <LogoutPopupView />
        </template>
        <div class="nav_list">
            <div :class="{active:$route.params.slug==$store.state.user_dashboard_path && $route.params.page=='my-orders'}">
                <nuxt-link :to="{path: '/'+ $store.state.user_dashboard_path +'/my-orders'}" v-if="$store.state.auth_info.user_data.user_type!==$store.state.admin_user_type_id && $store.state.auth_info.loggedIn">
                    <i class="fa fa-notes-medical"></i>
                    <div class="title"><small>My Orders</small></div>
                </nuxt-link>
                <div v-else @click="$parent.auth_form_open_status(true); setBlackOverlayFooter(true)">
                    <i class="fa fa-notes-medical"></i>
                    <div class="title"><small>My Orders</small></div>
                </div>
            </div>
            <!-- <div :class="{active:$route.params.slug==$store.state.user_dashboard_path && $route.params.page=='my-prescriptions'}">
                <nuxt-link :to="{path: '/'+ $store.state.user_dashboard_path +'/my-prescriptions'}" v-if="$store.state.auth_info.user_data.user_type!==$store.state.admin_user_type_id && $store.state.auth_info.loggedIn">
                    <i class="fa fa-prescription"></i>
                    <div class="title"><small>My Prescriptions</small></div>
                </nuxt-link>
                <div v-else @click="$parent.auth_form_open_status(true); setBlackOverlayFooter(true)">
                    <i class="fa fa-prescription"></i>
                    <div class="title"><small>My Prescriptions</small></div>
                </div>
            </div> -->
            <div :class="{active:$route.params.slug==$store.state.user_dashboard_path && $route.params.page=='manage-address'}">
                <nuxt-link :to="{path: '/'+ $store.state.user_dashboard_path +'/manage-address'}" v-if="$store.state.auth_info.user_data.user_type!==$store.state.admin_user_type_id && $store.state.auth_info.loggedIn">
                    <i class="far fa-address-book"></i>
                    <div class="title"><small>My Addresses</small></div>
                </nuxt-link>
                <div v-else @click="$parent.auth_form_open_status(true); setBlackOverlayFooter(true)">
                    <i class="far fa-address-book"></i>
                    <div class="title"><small>Manage Address</small></div>
                </div>
            </div>
            <div class="center_button" ref="homeBtn" @click="animateRipple">
                <!-- <i class="fa fa-home"></i> -->
                <nuxt-link :to="{path: '/'}">
                    <img v-if="$store.state.logo_info_data.favicon" :src="$store.state.logo_info_data.favicon" />
                    <img v-else src="@/assets/images/favicon.png" />

                    <div class="transition-effect">
                        <transition-group>
                            <template v-for="(val, i) in ripples">
                                <span
                                    class="ripple"
                                    :ref="'ripple-' + i"
                                    :key="'ripple' + i"                                
                                    v-if="val.show === true"
                                    :style="{'top': val.y + 'px', 'left': val.x + 'px'}"
                                    @animationend="rippleEnd(i)">
                                </span>
                            </template>
                        </transition-group>
                    </div>
                </nuxt-link>                
            </div>
            <MyCartInfo ref="cart_list_info" />
            <!-- <div class="">
                <div @click="chat_open_hide">
                    <i class="fa fa-comment"></i>
                    <div class="title"><small>Chat</small></div>
                </div>
            </div> -->
            <div :class="{active:$route.params.slug==$store.state.user_dashboard_path && my_account_active}">
                <div v-if="$store.state.auth_info.user_data.user_type!==$store.state.admin_user_type_id && $store.state.auth_info.loggedIn" @click="open_navigation=true">
                    <i class="far fa-user"></i>
                    <div class="title"><small>My Account</small></div>
                </div>
                <div v-else @click="$parent.auth_form_open_status(true); setBlackOverlayFooter(true)">
                    <i class="far fa-user"></i>
                    <div class="title"><small>My Account</small></div>
                </div>
            </div>
        </div>

        <component :is="UserNavigationBlock" v-if="open_navigation" />
        
    </div>
</template>
<script>
import LogoutPopupView from '~/components/content_display/LogoutPopupView'
import MyCartInfo from './components/MyCart'
import { mapMutations, mapActions } from 'vuex'
export default {
    name: 'MobileBottomNavBarBlock',
    components: {
        LogoutPopupView,        
        MyCartInfo
    },
    data(){
        return {
            logout_loader: false,
            open_navigation: false,
            ripples: []
        }
    },
    watch: {
        $route(to,form){
            this.close_navigation_block()
        },
        my_account_active: function(){}
    },
    computed: {
        my_account_active: function(){
            if(this.$route.params.page=='my-orders' || this.$route.params.page=='manage-address') return false
            else return true
        },
        UserNavigationBlock: function(){
            return () => import(`@/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/header/UserNavigationBlock`);
        }
    },
    methods: {
        ...mapActions({            
            userLogout: 'auth_info/LOGOUT_REQ',
        }),
        ...mapMutations({
            setBlackOverlayFooter: 'BLACK_OVERLAY_FOOTER'
        }),
        chat_open_hide: function(){            
            $crisp.push(["do", "chat:open"])
            $crisp.push(["config", "hide:on:mobile", [false]])

            $crisp.push(["on", "chat:closed", function(){
                $crisp.push(["config", "hide:on:mobile", [true]])
            }])
        },
        user_logout: async function(){
            this.close_navigation_block()
            this.logout_loader = true;
            localStorage.removeItem('user_info')
            await this.userLogout()
            this.$router.push('/')
            this.logout_loader = false
            
            this.$notify.success({
                title: 'Logout',
                message: 'Successfully Done',
                position: 'bottomRight',
                transitionIn: 'bounceInRight',
                transitionOut: 'fadeOutLeft',
                timeout: 1500
            })
        },
        animateRipple: function(e) {
			let el  = this.$refs.homeBtn;
			// let pos = el.getBoundingClientRect();
			
			this.ripples.push({
				// x: e.clientX - pos.left,
				// y: e.clientY - pos.top,
                x: 33,
                y: 33,
				show: true
			});
		},
		rippleEnd: function(i) {
			// this.ripples[i].show = false;
		},        
        auth_form_open_status: function(status){
            this.$refs.cart_list_info.open_cart_list = false
            this.$parent.auth_form_open_status(status)
        },
        close_navigation_block: function(){
            this.open_navigation = false
            this.setBlackOverlayFooter(false)
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style lang="scss" scoped>
    .mobile_bottom_nav_bar_block{
        position: fixed;
        bottom: 0; left: 0;
        width: 100%;
        z-index: 1000;

        &.overlay_block{
            z-index: 1001;
        }
    }
    .nav_list{
        display: flex;
        width: 100%;
        background-color: $sys_brand_color;
        height: 50px;
    }
    .nav_list > div{
        align-self: center;
        text-align: center;
        color: #fff;
        width: 100%;
        font-size: 18px;
        transition: all 0.4s;
    }
    .nav_list > div a{
        color: $white;
    }
    .nav_list > div.active i{
        height: 65px;
        line-height: 65px;
        width: 60px;
        border-radius: 50% 50% 0 0;
        background-color: $sys_brand_color;
        color: $white;;
        font-size: 24px;
        padding-top: 5px;
    }
    .nav_list > div div.title{
        font-size: 10px; line-height: 14px; color: $white;
    }
    .center_button{
        display: block;        
    }
    .center_button a{
        position: relative;
        display: inline-block;
        width: 60px;
        height: 60px;
        background-color: $white;
        box-shadow: 0 0 15px $sys_brand_color;        
        // line-height: 60px;
        border-radius: 50%;        
        top: -20px;        
        overflow: hidden;
        user-select: none;
    }
    .center_button .transition-effect{
        position: absolute;
        width: 100%; height: 100%;
        left: 0; top: 0;
        border-radius: 50%;
        overflow: hidden;
        z-index: 5;
    }
    .center_button i,
    .center_button img{
        display: inline-block;                
        width: 100%;
        height: 100%;
        /* border: 3px solid #eee; */
        font-size: 22px;
        // border-radius: 50%;
        padding: 15px
    }
    .center_button img{
        background-color: $white;
    }
    .ripple{
        background-color: $ripple_orange;
        width: 5px;
	    height: 5px;
        animation: ripple 2000ms ease-out forwards, fade 1000ms ease-out forwards;
    }
</style>