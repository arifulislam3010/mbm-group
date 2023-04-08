<template>
    <div id="partner_auth_modal_block">
        <div class="black_overlay">
            <div class="white_overlay">                
                <span v-if="!req_submit" class="close_btn" @click="popup_close">x</span>
                <div class="content">
                    <div class="left-section">                        
                        <div class="avatar_logo">
                            <span><img src="~/assets/images/teacher.png" /></span>
                        </div>
                        <div class="text">
                            <h4>PARTNER LOGIN</h4>
                            <p>Only registered partner can login</p>
                        </div>
                    </div>
                    <div class="right-section">
                        <div v-if="req_submit" class="pre_loader">
                            <RainbowLoader />
                        </div>
                        <div class="tab_list">
                            <div :class="{'active':sel_tab_index==1}" @click="sel_form_block(1)">
                                <span>Login</span>    
                            </div>
                            <!-- <div :class="{'active':sel_tab_index==2}"  @click="sel_form_block(2)">
                                <span>Register</span>
                            </div> -->
                        </div>
                        <div class="form_content_block">
                            <div v-if="sel_tab_index==1">
                                <LoginForm />
                            </div>
                            <div v-else-if="sel_tab_index==2">
                                <RegisterForm />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import RainbowLoader from '~/components/loader/RainbowLoader'
import LoginForm from '~/components/auth/partnerLoginForm'
import RegisterForm from '~/components/auth/partnerRegisterForm'
export default {
    name: 'partnerAuthModalBlock',
    components: {
        RainbowLoader,
        LoginForm,
        RegisterForm
    },
    data(){
        return {
            sel_tab_index: 1,
            forgot_password_email: '',
            req_submit: false
        }
    },
    mounted(){
        document.body.classList.add('popup_open')
    },
    methods:{
        sel_form_block: function(sel_index){
            this.sel_tab_index = sel_index
        },
        popup_close: function(){
            this.$parent.partner_auth_modal=false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style lang="scss" scoped>
    #partner_auth_modal_block{
        position: relative;
        height: 100%;
    }
    .white_overlay{
        width: 70%;
        left: 15%;
        top: 10%;
        height: 450px;
        max-height: 100%;
    }
    .content{
        display: block;
        width: 100%;
        height: 100%;
    }
    .left-section{        
        float: left;
        width: 350px;
        height: 100%;
        background-size: cover;
    }
    .left-section .avatar_logo{
        position: relative;
        top: 25%;
        text-align: center;
    }
    .left-section .avatar_logo span{
        display: inline-block;
        width: 120px;
        height: 120px;
    }
    .left-section .avatar_logo img{
        border: 5px solid #ffffff;
        background-color: #ffffff;
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }
    .left-section .text{
        position: relative;
        text-align: center;
        color: #fff;
        top: 25%;
        margin-top: 25px;
    }
    .right-section{
        position: relative;
        float: left;
        width: calc(100% - 350px);        
        background-color: #f7f7f7;
        height: 100%;
    }
    .right-section .tab_list{
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
    .pre_loader{
        position: absolute;
        background-color: #4da67c80;
        width: 100%; height: 100%;
        z-index: 10;
        :deep(div){
            left: 40%;
        }
    }
</style>