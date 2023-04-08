<template>
    <div id="login_form" class="eng_font">
        <div class="form_block">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-input mt-2">
                        <label>Login ID</label>
                        <input v-model="form_data.login_id" class="form-control" placeholder="Enter e-mail or mobile number" @keyup.enter="form_submit" ref="login_id" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-input mt-2">
                        <label>Password</label>
                        <div class="pwd_block">
                            <input :type="pwd_field_type" v-model="form_data.password" class="form-control" maxlength="60" placeholder="Enter password" @keyup.enter="form_submit" ref="password" />
                            <span @click="password_input_setup">
                                <i v-if="password_show" class="fa fa-eye-slash"></i>
                                <i v-else class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="forgot_password" @click="$parent.sel_form_block(4)">
                        <span>Forgot Password?</span>    
                    </div>
                </div>
            </div>
        </div>
        <div class="form_footer">
            <div class="row">
                <div class="col-md-12">
                    <div :class="['msg',{err : submit_status=='error'}]" v-html="submit_msg"></div>
                    <div v-if="$store.state.auth_info.user_status=='2'" class="need_verify_msg"><span @click="$parent.sel_form_index=3">Click here</span> to verify account</div>
                </div>
                <div class="col-md-12 clearfix">
                    <div class="button_item_block">
                        <div class="submit_btn" @click="form_submit"><i class="fa fa-check-circle"></i> Login <i class="fas fa-sign-in-alt"></i></div>
                        <div class="divider ml-2 mr-2">OR</div>
                        <FBLoginBlock />
                        <GLoginBlock />
                    </div>
                </div>
            </div>
            <div class="auth_info">
                <div>By signing up, you agree to our <a class="auth_btn" href="/page/terms-&-conditions">Terms of Service.</a></div>
                <div>You have no account? <span class="auth_btn" @click="$parent.sel_form_block(2)">Register</span></div>
            </div>
        </div>
    </div>
</template>
<script>
import FBLoginBlock from '@/components/auth/FBLogin'
import GLoginBlock from '@/components/auth/GLogin'
import { mapState, mapActions } from 'vuex'
export default {
    name: 'UserLoginFormBlock',
    props: ['base_path'],
    components: {
        FBLoginBlock,
        GLoginBlock
    },
    data(){
        return {
            form_data: {
                login_id: '',
                password: ''
            },
            social_login: {
                data: '',
                type: ''
            },
            password_show: false,
            pwd_field_type: 'password',
            submit_status: '',
            submit_msg: ''
        }
    },
    watch: {
        social_login: function(data){            
            if(data.social_id_info.type=='Google'){
                // console.log(data)
                this.social_login_submit()
            }else if(data.social_id_info.type=='Facebook'){
                // console.log(data)                
                this.social_login_submit()
            }
        }
    },
    methods: {
        ...mapActions({
            userLogin: 'auth_info/USER_LOGIN_REQ',
            socialUserLogin: 'auth_info/SOCIAL_USER_LOGIN_REQ'
        }),
        password_input_setup: function(){
            if(this.password_show){
                this.pwd_field_type = 'password'
                this.password_show = false
            }else{
                this.pwd_field_type = 'input'
                this.password_show = true
            }
        },
        async social_login_submit(){
            this.$parent.req_submit = true;
            
            let obj = {
                formData: this.social_login,
                getTokenData: this.$store.state.oauth_token.data
            }

            if(this.social_login.login_id){

                await this.socialUserLogin(obj);

                if(this.$store.state.auth_info.user_data && (this.$store.state.auth_info.user_data.length>0 || this.$store.state.auth_info.user_data.id)){
                    var user_data = this.$store.state.auth_info.user_data
                    localStorage.setItem('user_info', JSON.stringify(user_data))
                    
                    this.$parent.req_submit = false;
                    this.$parent.$parent.auth_form_open_status(false);
                    
                    if(this.$store.state.auth_info.user_data.user_type==this.$store.state.delivery_user_type_id) this.$router.push('/' + this.$store.state.user_dashboard_path);
                    else if(this.$store.state.auth_info.user_data.user_type==this.$store.state.admin_user_type_id) this.$router.push('/' + this.$store.state.dashboard.prefix);

                    this.$notify.success({
                        title: 'Login',
                        message: 'Successfully Done',
                        position: 'bottomRight',
                        transitionIn: 'bounceInRight',
                        transitionOut: 'fadeOutLeft',
                        timeout: 1500
                    })
                }else{
                    this.submit_msg = this.$store.state.auth_info.login_err_msg;
                    this.submit_status = '';
                    this.$parent.req_submit = false;
                }
            }else{
                this.$toast.error('Didn\'t get email or phone', {icon: "Warning"});
                this.$refs.login_id.focus();
                this.submit_status = '';
                this.$parent.req_submit = false;
            }
        },
        async form_submit(){
            let getLoginId = this.form_data.login_id;
            let getPassword = this.form_data.password;
            if(getLoginId.trim()==''){
                this.$toast.error('Please enter login ID', {icon: "Warning"});
                this.$refs.login_id.focus();
                return false;
            }else if(!this.$validateEmail(getLoginId.trim()) && !this.$validateContactNumber(getLoginId.trim())){
                this.$toast.error('Please valid login id', {icon: "Warning"});
                this.$refs.login_id.focus();
                return false;
            }else if(getPassword==''){
                this.$toast.error('Please enter password', {icon: "Warning"});
                this.$refs.password.focus();
                return false;
            } 

            this.$parent.req_submit = true;
            
            let obj = {
                formData: this.form_data,
                getTokenData: this.$store.state.oauth_token.data
            }
            await this.userLogin(obj);

            if(this.$store.state.auth_info.user_data && (this.$store.state.auth_info.user_data.length>0 || this.$store.state.auth_info.user_data.id)){
                var user_data = this.$store.state.auth_info.user_data
                localStorage.setItem('user_info', JSON.stringify(user_data))
                
                this.$parent.req_submit = false;
                this.$parent.$parent.auth_form_open_status(false);
                
                if(this.$store.state.auth_info.user_data.user_type==this.$store.state.delivery_user_type_id) this.$router.push('/' + this.$store.state.user_dashboard_path);
                else if(this.$store.state.auth_info.user_data.user_type==this.$store.state.admin_user_type_id) this.$router.push('/' + this.$store.state.dashboard.prefix);

                this.$notify.success({
                    title: 'Login',
                    message: 'Successfully Done',
                    position: 'bottomRight',
                    transitionIn: 'bounceInRight',
                    transitionOut: 'fadeOutLeft',
                    timeout: 1500
                })
            }else{
                this.submit_msg = this.$store.state.auth_info.login_err_msg;
                this.submit_status = '';
                this.$parent.req_submit = false;
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    #login_form .form_block{
        padding: 0 20px 20px 20px;
        text-align: left;
    }
    #login_form label{
        font-size: 12px;
        font-weight: bold;
        color: #eee
    }
    #login_form input{
        font-size: 12px;
        background-color: #3d46a7;
        border: 1px solid #3d46a7;
        color: #fff;
        padding: 5px 10px;
        height: auto;
    }
    .pwd_block{
        position: relative;
        & > span{
            position: absolute;
            top: 8px;
            right: 10px;
            color: #ddd;
            cursor: pointer
        }
    }
    .forgot_password{
        text-align: right;
        margin-top: 3px;
    }
    .forgot_password > span{
        display: inline-block;
        color: $yellow_text_color; cursor: pointer;
        font-size: 11px
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
    .need_verify_msg{
        display: block;
        font-size: 12px;
        margin-top: -8px;
        margin-bottom: 5px;
        color: #ffdbdb;
        text-align: center;
    }
    .button_item_block{
        display: flex;
        height: 40px;
    }
    .button_item_block > div{
        align-self: center; margin: 0 3px
    }
    .divider{
        font-size: 10px; color: #ccc
    }
    .submit_btn{
        line-height: 30px;
        display: block;
        width: 70%;
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
    .auth_info{
        display: block;
        text-align: right;
        font-size: 11px;
        margin-top: 10px
    }
    .need_verify_msg > span,
    .auth_info .auth_btn{
        color: $yellow_text_color;
        cursor: pointer;
    }
    .need_verify_msg > span:hover,
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
