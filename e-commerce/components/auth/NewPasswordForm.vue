<template>
    <div id="new_password_setup_form" class="eng_font">
        <h4 v-if="!success_msg" class="caption">Check Your Inbox</h4>
        <p v-if="!success_msg" class="info">A 6-digit confirmation code has been sent to your email.</p>
        <div class="form_block">
            <div v-if="success_msg" class="success_msg">
                <span class="icon"><i class="fa fa-check-circle fa-4x"></i></span>
                <p>Your new password has been updated successfully. You can login with your new password. <span class="auth_btn" @click="$parent.sel_form_block(1)">Back to Login</span></p>
            </div>
            <div v-else class="row">            
                <div class="col-md-6">
                    <div class="form-input mt-3">
                        <label>Enter Code</label>
                        <div class="code_block clearfix">
                            <input :class="{'active':form_data.code[i]!==''}" v-for="(n,i) in 6" :key="'code-'+i" v-model="form_data.code[i]" class="form-control" maxlength="1" @keyup="onKeyup" @paste="onPaste" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-input mt-3">
                        <label>New Password Set</label>
                        <input type="password" v-model="form_data.password" class="form-control" placeholder="Enter new password" @keyup.enter="form_submit" ref="new_password_form_input" />
                    </div>
                </div>                
            </div>
        </div>
        <div v-if="!success_msg" class="form_footer">
            <div class="row">
                <div class="col-md-12">
                    <div :class="['msg',{err : submit_status=='error'}]" v-html="submit_msg"></div>
                </div>
                <div class="col-md-12 clearfix">
                    <div class="submit_btn float-right" @click="form_submit"><i class="fa fa-check-circle"></i> Change your password <i class="fas fa-sign-in-alt"></i></div>
                </div>
            </div>
            <div class="auth_info">
                <div>
                    Didn't receive the code? <span class="auth_btn" @click="get_another_code">Request another one</span>
                </div>
                <div class="mt-2">
                    <span class="auth_btn" @click="$parent.sel_form_block(1)">Back to Login</span>
                </div>                
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ForgotPasswordFormBlock',
    data(){
        return {
            form_data: {
                password: '',
                code: {
                    0: '',
                    1: '',
                    2: '',
                    3: '',
                    4: '',
                    5: ''
                },
                email: this.$parent.forgot_password_email
            },
            submit_status: '',
            submit_msg: '',
            success_msg: false
        }
    },
    mounted(){
        // this.$refs['code_0'].focus()
    },
    methods: {
        code_check: function(index){
            if(!this.isNumber(this.form_data.code[index])) this.form_data.code[index] = ''
            // else this.$refs['code-' + (index+1)].$el.focus();
        },
        isNumber: function(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },
        onPaste(event){
            console.log(event)
        },
        onKeyup(event){
            event.target.value = event.target.value.replace(/[^0-9]/g,'')
            // console.log(event.keyCode)
            
            if((event.keyCode >= 48 && event.keyCode <= 57)||(event.keyCode >= 96&&event.keyCode<=105)){
            const next = event.target.nextElementSibling
            
            if(next === null) return
                event.target.nextElementSibling.focus()
            }
            return
        },
        async get_another_code(){
            var url = '/api/jobseeker/forgot-password';                        
            
            let request_data = {
                email: this.$parent.forgot_password_email
            }
            let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));
            
            this.$parent.req_submit = true;
            this.$axios.post(url, request_data,{
                headers: {
                    'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                this.$toast.success('Code has been sent', {icon: "success"});
                this.$parent.req_submit = false;                
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"});
                this.$parent.req_submit = false;
            });
        },
        async form_submit(){
            var url = '/api/jobseeker/set-new-password';
            this.submit_msg = ''

            var code_missing = 0;

            for(var i=0; i<6; i++){
                if(this.form_data.code[i]==''){
                    code_missing = 1; //return true;
                }
            }

            if(code_missing==1){
                this.$toast.error('Please enter your secret code', {icon: "Warning"});                
                return false;
            }else if(this.form_data.password==''){
                this.$toast.error('Please enter a new password', {icon: "Warning"});
                this.$refs.new_password_form_input.focus();
                return false;
            }            
            
            let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));
            
            this.$parent.req_submit = true;
            this.$axios.post(url, this.form_data,{
                headers: {
                    'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                // console.log('Get data',response.data)
                
                this.submit_status = response.data.status;
                
                if(this.submit_status==1){
                    this.success_msg = true;
                } else if(this.submit_status==2){
                    this.submit_msg = '<div class="err_msg"><i class="fa fa-info-circle"></i> Invalid code</div>'
                }
                else this.submit_msg = '<div class="err_msg"><i class="fa fa-check-circle"></i> Submitted E-mail not exist.</div>';

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
<style lang="scss" scoped>
    #new_password_setup_form{
        .form_block{
            padding: 0 20px 20px 20px;
        }    
        .caption{
            font-size: 14px;
            font-weight: bold;
            color: #888;
            margin: 20px 0 0 20px;
        }
        .info{
            padding: 10px 0 0 20px;
            font-size: 12px;
            color: #666;
        }
        label{
            font-size: 12px;
            font-weight: bold;
            color: #999999
        }
        input{
            font-size: 12px        
        }
    }
    .success_msg{
        display: block; text-align: center;
        padding: 15%;
        .icon{
            font-size: 20px;
            color: #4da67ccc;
            margin-bottom: 15px;
            display: block;
        }
    }
    .code_block{
        display: flex;
        flex-wrap: nowrap;
        & > input{
            margin-left: 7px;
            text-align: center; 
            padding: 5px; 
            font-size: 18px !important; 
            background-color: #eeeeee;
            &:first-child{
                margin-left: 0
            }
            &.active{
                background-color: #ffffff
            }
        }
    }    
    .new_password{
        text-align: right;        
        & > span{
            display: inline-block;
            color: #006699; cursor: pointer;
        }
    }
    .form_footer{
        border-top: 1px solid #dddddd;
        padding: 10px 20px;
        :deep(.msg){
            font-size: 12px;
            line-height: 36px;
            .succ{
                color: #007f44
            }
            .err{
                color: #CD0000
            }
        }
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
        transition: all 0.4s;        
        & > i{
            color: #737260; margin-right: 5px;
            transition: all 0.4s
        }
        &:hover{
            color: #fff;
            & > i{
                color: #fff
            }
        }
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
        &:hover{
            text-decoration: underline
        }
    }
</style>