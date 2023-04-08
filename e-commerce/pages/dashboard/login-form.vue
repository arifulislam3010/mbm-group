<template>
    <div class="login_container">
        <div class="black_mask_overlay"></div>
        <div class="content">
            <div class="left-section">                        
                <!-- <div class="avatar_logo">
                    <span><img src="~/assets/images/pharmacy.png" /></span>
                </div>
                <div class="text">
                    <h4>DASHBOARD LOGIN</h4>
                    <p>Only authorized user can login</p>
                </div> -->
            </div>
            <div class="right-section">
                <div v-if="req_submit" class="pre_loader">
                    <RainbowLoader />
                </div>
                <!-- <div class="tab_list">
                    <div class="active">
                        <span><i class="fas fa-sign-in-alt"></i> Login Form</span>    
                    </div>
                </div> -->
                <div class="form_content_block">
                    <h4>Welcome !</h4>
                    <div>Sign in to your account</div>
                    <AdminLoginForm :base_path='base_path' />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import AdminLoginForm from '~/components/auth/adminLoginForm'
export default {
    name: 'LoginForm',
    props: ['base_path'],
    components: {
        AdminLoginForm
    },
    data() {
        return {
            req_submit: false
        }
    }
}
</script>
<style lang="scss" scoped>
    .login_container{
        position: fixed;
        width: 100%; height: 100%;
        left: 0; top: 0;
        /* background-color: #4c56c0; */
        background-image: url(.~assets/images/login-body-bg.jpg);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center bottom;
        padding-bottom: 25px;
        overflow-y: auto;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, transparent);
    }
    .content{
        position: relative;
        background-color: #4c56c0cc;
        /* background-image: linear-gradient(to top, #4c56c0cc, #ffffff); */
        width: 60%;
        left: 20%;
        top: 10%;
        /* top: 10%; */
        /* height: 80%; */
        /* min-height: 400px; */
        border-radius: 5px;
        box-shadow: 0 0 15px #000;
        overflow: hidden;
    }
    .left-section{        
        float: left;
        position: relative;
        width: calc(100% - 325px);
        height: 425px; left: -10px;
        /* background-image: url('.~assets/images/auth-bg.jpg');
        background-repeat: no-repeat;
        background-size: cover; */
        background-image: url(.~assets/images/login-bg-white.png);
        background-position: left center;
        background-repeat: no-repeat;
        background-size: cover;
        opacity: 0.6;
        /* background-color: #ffffffdd; */
        border-radius: 0 0 0 5px;
    }
    .left-section .avatar_logo{
        position: relative;
        top: 15%;
        text-align: center;
    }
    .left-section .avatar_logo span{
        display: inline-block;
        /* width: 120px; */
        height: 250px;
    }
    .left-section .avatar_logo img{
        /* border: 5px solid #ffffff;
        background-color: #ffffff; */
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* border-radius: 50%; */
    }
    .left-section .text{
        position: relative;
        text-align: center;
        /* color: #fff; */
        top: 15%;
        margin-top: 25px;
    }
    .right-section{
        position: relative;
        float: left;
        width: 325px;
        right: 0;     
        /* background-color: #4c56c0cc; */
        height: 100%;
        border-radius: 0 0 5px 0;
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
        }
        div :deep(span){
            display: inline-block;
            height: 70px;
            line-height: 70px;
            font-size: 20px;
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
    .form_content_block{
        padding-right: 25px;
        & > h4{
            text-align: center;
            color: #fff;
            margin-top: 15%
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
            padding: 0;
        }
    }
</style>