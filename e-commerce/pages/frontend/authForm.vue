<template>
    <div class="auth_container">
        <div class="black_mask_overlay" @click="$parent.auth_form_open_status(false)"></div>
        <div class="content clearfix">
            <div class="left-section">
                <div class="background-img"></div>
            </div>
            <div class="right-section">
                <div v-if="req_submit" class="pre_loader">
                    <RainbowLoader />
                </div>                
                <div class="form_content_block">                    
                    <h4>Welcome !</h4>                    
                    <template v-if="sel_form_index=='1'">
                        <div>Sign in to your account</div>
                        <UserLoginForm :base_path='base_path' />
                    </template>
                    <template v-else-if="sel_form_index=='2'">                        
                        <div>Signup to your account</div>
                        <UserSignupForm :base_path='base_path' />
                    </template>
                    <template v-else-if="sel_form_index=='3'">                        
                        <AuthCodeConfirmForm :base_path='base_path' />
                    </template>
                    <template v-else-if="sel_form_index=='4'">
                        <div>Forgot password to your account</div>
                        <ForgotPasswordForm :base_path='base_path' />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ForgotPasswordForm from '~/components/auth/ForgotPasswordForm'
import AuthCodeConfirmForm from '~/components/auth/AuthCodeConfirmForm'
import UserLoginForm from '~/components/auth/userLoginForm'
import UserSignupForm from '~/components/auth/userSignupForm'
export default {
    name: 'AuthForm',
    components: {
        ForgotPasswordForm,
        AuthCodeConfirmForm,
        UserLoginForm,
        UserSignupForm
    },
    data() {
        return {
            base_path: '/',
            sel_form_index: 1,
            req_submit: false,
            access_token: this.$store.state.oauth_token.token,
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.access_token
                }
            };
            return obj;
        }
    },
    methods: {
        sel_form_block: function(index){
            this.sel_form_index = index
        }
    }
}
</script>
<style lang="scss" scoped>
    .auth_container{
        position: fixed;
        width: 100%; height: 100%;
        left: 0; top: 0;
        background-color: #252d4980;
        padding-bottom: 25px;
        overflow-y: auto;
        z-index: 1001;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, transparent);
    }
    .content{
        position: relative;
        background-color: #4c56c0;        
        width: 60%;
        left: 20%;
        top: 10%;        
        border-radius: 5px;
        box-shadow: 0 0 15px #000;
        /* overflow: hidden; */
    }
    .left-section{        
        float: left;
        position: relative;
        width: calc(100% - 350px);        
        /* opacity: 0.6;         */
        border-radius: 0 0 0 5px;
        .background-img{
            position: absolute; width: 100%;
            height: 450px; left: 0; top: 0;
            background-image: url(.~assets/images/login-bg-white.png);
            background-position: left center;
            background-repeat: no-repeat;
            background-size: contain;
            border-radius: 5px 0 0 5px;
        }
        .avatar_logo{
            position: relative;
            top: 15%;
            text-align: center;
            span{
                display: inline-block;        
                height: 250px;
            }
            img{        
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }
        .text{
            position: relative;
            text-align: center;        
            top: 10%;
            margin-top: 25px;
        }
    }
    .right-section{
        position: relative;
        float: right;
        width: 350px;
        min-height: 435px;
        right: 0;             
        height: 100%;
        border-radius: 0 0 5px 0;
        .tab_list{
            display: flex;
            flex-wrap: wrap;
            border-bottom: 1px solid #dddddd;        
            :deep(div){
                width: 100%;
                text-align: center;
                cursor: pointer;
                background-color: #ffffff;
                &.active{
                    background-color: #4da67c; color: #fff
                }
                span{
                    display: inline-block;
                    height: 70px;
                    line-height: 70px;
                    font-size: 20px;
                }
                &:nth-child(1)::after{
                    position: absolute;
                    right: 0;
                    width: 25px;
                    height: 100%;
                    background-color: #4da67c;
                    transform: rotate(25deg);
                }
            }
        }
    }
    .form_content_block{
        padding-right: 25px;
        & > h4{
            text-align: center;
            color: #fff;
            margin-top: 10%
        }
        & > div{
            text-align: center;
            color: #fff; margin-bottom: 15px;
        }
    }
    .pre_loader{
        position: absolute;
        background-color: #4da67c80;
        width: 100%; height: 100%;
        z-index: 10;
        :deep(div){
            left: 35%;
        }
    }

    @media(max-width: 480px){
        .content{
            width: 80%;
            left: 10%;
            top: 10%;
        }
        .left-section{
            display: none;
        }
        .right-section{
            float: none;
            width: 100%;
        }
        .form_content_block{
            padding: 15px 0;
        }
    }
</style>