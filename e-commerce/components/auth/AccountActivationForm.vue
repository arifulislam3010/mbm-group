<template>
    <div id="account_activation_form" class="eng_font">
        <h4 v-if="!success_msg"  class="caption">Already you have created an account.</h4>
        <p v-if="!success_msg"  class="info">Just one click, you can activate your account now.</p>  

        <div class="form_block">
            <div v-if="success_msg" class="success_msg">
                <span class="icon"><i class="fa fa-check-circle fa-4x"></i></span>
                <p>Your account has been activated successfully. <span class="auth_btn" @click="$parent.sel_form_block(1)">Back to Login</span></p>
            </div>
            <div v-else class="row">
                <div class="col-md-12">
                    <div class="form-input mt-3">
                        <label>E-mail</label>
                        <input v-model="form_data.email" class="form-control" placeholder="Enter E-mail address" @keyup.enter="form_submit" ref="email_form_input" />
                    </div>
                </div>                
            </div>
        </div>
        <div v-if="!success_msg"  class="form_footer">
            <div class="row">
                <div class="col-md-12">
                    <div :class="['msg',{err : submit_status=='error'}]" v-html="submit_msg"></div>
                </div>
                <div class="col-md-12 clearfix">
                    <div class="submit_btn float-right" @click="form_submit"><i class="fa fa-check-circle"></i> Click here to activate your account <i class="fas fa-sign-in-alt"></i></div>
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
import { mapMutations } from 'vuex'
export default {
    name: 'AccountActivationFormBlock',
    data(){
        return {
            form_data: {
                email: '',
                token: this.$route.query.token
            },
            submit_status: '',
            submit_msg: '',
            success_msg: false
        }
    },
    methods: {
        ...mapMutations({
            auth_form_open_status: 'AUTH_FORM_OPEN_STATUS'
        }),
        async form_submit(){
            var url = '/api/jobseeker/account-activation';
            this.submit_msg = ''
            
            if(this.form_data.email==''){
                this.$toast.error('Please enter email address', {icon: "Warning"});
                this.$refs.email_form_input.focus();
                return false;
            }
            
            // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));
            let getTokenData = this.$store.state.oauth_token.data
            
            this.$parent.req_submit = true;
            this.$axios.post(url, this.form_data,{
                headers: {
                    'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                // console.log('Get data',response.data)
                
                this.submit_status = response.data.status;
                
                if(this.submit_status==1){
                    let query = Object.assign({}, this.$route.query);
                    delete query['action']; delete query['token'];
                    this.$router.replace({ query });
                    this.success_msg = true;
                } else if(this.submit_status==2){
                    this.submit_msg = '<div class="err_msg"><i class="fa fa-info-circle"></i> E-mail didn\'t exist.</div>'
                } else if(this.submit_status==3){
                    this.submit_msg = '<div class="err_msg"><i class="fa fa-info-circle"></i> Invalid token submitted.</div>'
                } else if(this.submit_status==4){
                    this.submit_msg = '<div class="err_msg"><i class="fa fa-info-circle"></i> Data didn\'t updated. Please try later.</div>'
                }
                else this.submit_msg = '<div class="err_msg"><i class="fa fa-check-circle"></i> Data not found.</div>';

                this.$parent.req_submit = false;                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"});
                this.submit_msg = '';
                this.submit_status = '';
                this.$parent.req_submit = false;
            });
        }
    }
}
</script>
<style scoped>
    #account_activation_form .form_block{
        padding: 0 20px 20px 20px;
    }
    .success_msg{
        display: block; text-align: center;
        padding: 15%
    }
    .success_msg .icon{
        font-size: 20px;
        color: #4da67ccc;
        margin-bottom: 15px;
        display: block;
    }
    #account_activation_form .caption{
        font-size: 14px;
        font-weight: bold;
        color: #888;
        margin: 20px 0 0 20px;
    }
    #account_activation_form .info{
        padding: 10px 0 0 20px;
        font-size: 12px;
        color: #666;
    }
    #account_activation_form label{
        font-size: 12px;
        font-weight: bold;
        color: #999999
    }
    #account_activation_form input{
        font-size: 12px        
    }
    .activate_account{
        text-align: right;        
    }
    .activate_account > span{
        display: inline-block;
        color: #006699; cursor: pointer;
    }
    .form_footer{
        border-top: 1px solid #dddddd;
        padding: 10px 20px;
    }
    .form_footer :deep(.msg){
        font-size: 12px;
        line-height: 36px;
    }
    .form_footer :deep(.msg) .succ{
        color: #007f44
    }
    .form_footer :deep(.msg) .err{
        color: #CD0000
    }
    .submit_btn{
        line-height: 37px;
        display: inline-block;
        padding: 0 15px;
        color: #444;
        font-size: 14px;
        font-weight: 600;
        background-color: #fbcd41;
        border-left: 1px solid #fbcd41;
        cursor: pointer;
        transition: all 0.4s
    }
    .submit_btn:hover{
        color: #fff
    }
    .submit_btn > i{
        color: #737260; margin-right: 5px;
        transition: all 0.4s
    }
    .submit_btn:hover > i{
        color: #fff
    }
    .auth_info{
        display: block;
        text-align: right;
        font-size: 10px;
        margin-top: 10px
    }
    .auth_btn{
        color: #006699;
        cursor: pointer;
    }
    .auth_btn:hover{
        text-decoration: underline
    }
</style>