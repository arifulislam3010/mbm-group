<template>
    <div :class="['header_block',{mobile_device:$device.isMobile}]">
        <template v-if="logout_loader">
            <LogoutPopupView />
        </template>
        <div class="container">
            <div class="main_block">                
                <template v-if="$device.isMobile">
                    <div class="mobile_header_left_block">
                        <span class="menu_bar" @click="open_category_list=true">
                            <i class="fa fa-align-left"></i>
                        </span>                        
                    </div>
                </template>
                <template v-else>
                    <nuxt-link :to="{path: '/'}">
                        <Logo />
                        <!-- <SvgLogo /> -->
                    </nuxt-link>
                </template>

                <SearchInputBlock />
                
                <div v-if="!$device.isMobile" class="header_right_block">
                    <span class="cart" @click="open_cart_list=true">
                        <i class="fa fa-cart-plus"></i>
                        <sup>{{ $store.state.cart_info.total_cart_items }}</sup>
                    </span>
                    <span class="notification">
                        <i class="fa fa-bell"></i>
                        <sup>0</sup>
                    </span>                    
                    <LoginUserInfo v-if="$store.state.auth_info.loggedIn" />
                    <span v-else class="user_info" @click="$parent.auth_form_open_status(true)"><i class="fa fa-user"></i></span>                    
                </div>                
            </div>
        </div>
        <UserNavigationBlock v-if="open_navigation" />
        <CartListItemBlock v-else-if="open_cart_list" />
        <CategoryListItemBlock v-else-if="open_category_list" />
        <!-- <AddCartSound /> -->
    </div>
</template>
<script>
import Logo from '~/components/Logo'
import SvgLogo from '~/components/svg-logo'
import LogoutPopupView from '~/components/content_display/LogoutPopupView'
// import AddCartSound from '~/components/sounds/AddCartSound'
import SearchInputBlock from './search-input-block'
import LoginUserInfo from './LoginUserInfo'
import UserNavigationBlock from './UserNavigationBlock'
import CartListItemBlock from './CartListBlock'
import CategoryListItemBlock from './CategoryListBlock'
import { mapState, mapActions } from 'vuex'
export default {
    name: 'HeaderBlock',    
    components: {
        Logo,
        SvgLogo,
        LogoutPopupView,
        // AddCartSound,
        SearchInputBlock,
        LoginUserInfo,
        UserNavigationBlock,
        CartListItemBlock,
        CategoryListItemBlock
    },
    data(){
        return {
            logout_loader: false,
            open_category_list: false,
            open_navigation: false,
            open_cart_list: false
        }
    },
    watch: {
        $route(to, from){
            this.close_navigation_block()
            this.close_cart_list_block()
            this.close_category_list_block()
        },
        total_cart_items: function(){}
    },
    methods: {
        ...mapActions({            
            userLogout: 'auth_info/LOGOUT_REQ',
        }),
        user_logout: async function(){
            this.logout_loader = true;
            localStorage.removeItem('user_info')
            await this.userLogout()
            this.$router.push('/')
            this.logout_loader = false
            this.close_navigation_block()
            this.close_cart_list_block()
            this.close_category_list_block()

            this.$notify.success({
                title: 'Logout',
                message: 'Successfully Done',
                position: 'bottomRight',
                transitionIn: 'bounceInRight',
                transitionOut: 'fadeOutLeft',
                timeout: 1500
            })
        },
        close_navigation_block: function(){
            this.open_navigation = false
            document.body.classList.remove('popup_open')
        },
        close_cart_list_block: function(){
            this.open_cart_list = false
            document.body.classList.remove('popup_open')
        },
        close_category_list_block: function(){
            this.open_category_list = false
            document.body.classList.remove('popup_open')
        }
    }    
}
</script>
<style lang="scss" scoped>
    .header_block{
        position: fixed;
        left: 0; top: 0;
        width: 100%;
        height: 60px;
        background-color: $white;
        box-shadow: 0 0 15px #333;
        z-index: 999;
    }
    .header_block.mobile_device{
        background-color: $sys_brand_color;
    }    
    .main_block{
        display: flex;
        width: 100%; height: 60px;
    }
    .main_block > a,
    .main_block > div{
        align-self: center;
    }    
    .mobile_header_left_block{
        display: flex;
    }
    .menu_bar{
        display: inline-block;        
        font-size: 22px;        
        color: #eee;
        cursor: pointer;
    }
    .header_right_block{
        display: flex; text-align: right; margin-left: auto;
    }
    .header_right_block > span{
        position: relative;
        display: table;
        align-self: center;
        margin: 0 5px; cursor: pointer;
        width: 40px; height: 40px;
        border: 1px solid $white;
        line-height: 38px; text-align: center;
        background-color: #eee;
        border-radius: 50%;
        transition: all 0.4s;
    }
    .mobile_device .header_right_block > span{
        width: 36px; height: 36px; line-height: 34px;
    }
    .mobile_device .seach_input_block{
        height: 38px;
        margin: 0 0 0 15px;
    }
    .header_right_block > span:first-child{
        margin-left: auto;
    }
    .header_right_block > span:last-child{
        margin-right: 0;
    }
    .header_right_block > span:hover{
        background-color: $sys_brand_color;
        color: $white;
    }
    .header_right_block > span.notification:hover{
        background-color: rgb(105, 0, 204);
    }
    .notification > sup,
    .cart > sup{
        position: absolute;
        top: 0;
        width: 18px;
        height: 18px;
        background-color: $sys_brand_color;
        color: $white;
        border-radius: 50%;
        margin: 0;
        padding: 0;
        line-height: 16px;
        border: 1px solid $white;
        font-size: 10px;
    }
    .notification > sup{
        background-color: blueviolet;
    }
    .mobile_device .content{
        width: 300px;        
    }
</style>