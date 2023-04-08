<template>
    <div id="forgot_password_form" class="eng_font">        
        <template v-if="!request_send">
            <p class="info">No worries enter your login ID below and we will send you a reset code</p>
            <div class="form_block">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-input mt-3">
                            <label>Login ID</label>
                            <input v-model="form_data.login_id" class="form-control" placeholder="Enter e-mail or mobile number" @keyup.enter="form_submit" ref="login_id" />
                        </div>
                    </div>                
                </div>
            </div>
        </template>
        <template v-else>
            <p class="send_info">Your request has been sent successfully. Check your {{ forgot_password_type }} and try login again. Thank You.</p>
        </template>
        <div class="form_footer">
            <div v-if="!request_send" class="row">
                <div class="col-md-12">
                    <div :class="['msg',{err : submit_status=='error'}]" v-html="submit_msg"></div>
                </div>
                <div class="col-md-12 clearfix">
                    <div class="submit_btn" @click="form_submit"><i class="fa fa-check-circle"></i> Request Send <i class="fas fa-sign-in-alt"></i></div>
                </div>
            </div>
            <div class="auth_info">
                <div>
                    <span class="auth_btn" @click="$parent.sel_form_block(1)">Back to Login</span>
                </div>                
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
export default {
    name: 'ForgotPasswordFormBlock',
    data(){
        return {
            request_send: false,
            forgot_password_type: '',
            form_data: {
                login_id: ''
            },
            submit_status: '',
            submit_msg: ''
        }
    },
    methods: {
        ...mapActions({
            forgotPassword: 'auth_info/REQ_FORGOT_PASSWORD'
        }),
        async form_submit(){
            let getLoginId = this.form_data.login_id;            
            if(getLoginId.trim()==''){
                this.$toast.error('Please enter login ID', {icon: "Warning"});
                this.$refs.login_id.focus();
                return false;
            }else if(!this.$validateEmail(getLoginId.trim()) && !this.$validateContactNumber(getLoginId.trim())){
                this.$toast.error('Please valid login id', {icon: "Warning"});
                this.$refs.login_id.focus();
                return false;
            }
            
            this.$parent.req_submit = true;
            
            let obj = {
                formData: this.form_data,
                getTokenData: this.$store.state.oauth_token.data
            }
            await this.forgotPassword(obj);

            if(this.$store.state.auth_info.login_err_msg) this.$toast.error(this.$store.state.auth_info.login_err_msg, {icon: ""});
            else if(this.$store.state.auth_info.forgot_password_data && this.$store.state.auth_info.forgot_password_data.status){
                this.request_send = true
                this.forgot_password_type = this.$store.state.auth_info.forgot_password_data.login_type
            }            

            this.submit_status = '';
            this.$parent.req_submit = false;
        }
    }
}
</script>
<style lang="scss" scoped>
    #forgot_password_form .form_block{
        padding: 0 20px 20px 20px;
        text-align: left;
    }
    #forgot_password_form .caption{
        font-size: 14px;
        font-weight: bold;
        color: #888;
        margin: 20px 0 0 20px;
    }
    #forgot_password_form .info{
        padding: 10px 0 0 20px;
        margin: 0;
        font-size: 12px;        
        text-align: -webkit-auto;
        color: #ccc;
    }
    #forgot_password_form label{
        font-size: 12px;
        font-weight: bold;
        color: #eee
    }    
    #forgot_password_form input{
        font-size: 12px;
        background-color: #3d46a7;
        border: 1px solid #3d46a7;
        color: #fff;
        padding: 5px 10px;
        height: auto;
    }
    .forgot_password{
        text-align: right;        
    }
    .forgot_password > span{
        display: inline-block;
        color: #006699; cursor: pointer;
    }
    .form_footer{
        /* border-top: 1px solid #dddddd; */
        padding: 10px 20px;
    }
    .form_footer .msg{
        font-size: 12px;
        line-height: 36px;
    }
    .form_footer .msg .succ{
        color: $auth_succ_txt_color
    }
    .form_footer .msg .err{
        color: $auth_err_txt_color
    }
    .submit_btn{
        line-height: 30px;
        display: block;
        padding: 0 15px;
        color: #eee;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        background-color: #f05a24;
        border-radius: 25px;
        /* border-left: 1px solid #fbcd41; */
        cursor: pointer;
        transition: all 0.4s;
    }
    .submit_btn:hover{
        color: #fff;
        background-color: #CD0000;
    }
    .submit_btn > i{
        color: #eee; margin-right: 5px;
        transition: all 0.4s
    }
    .submit_btn:hover > i{
        color: #fff
    }
    .send_info{
        font-size: 17px;
        text-align: left;
    }
    .auth_info{
        display: block;
        text-align: right;
        font-size: 11px;
        margin-top: 10px
    }
    .auth_info .auth_btn{
        color: $yellow_text_color;
        cursor: pointer;
    }
    .auth_info .auth_btn:hover{
        text-decoration: underline
    }
    
    ::-webkit-input-placeholder { /* Edge */
        color: #bbbbbb;
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color: #bbbbbb;
    }

    ::placeholder {
        color: #bbbbbb;
    }
</style>