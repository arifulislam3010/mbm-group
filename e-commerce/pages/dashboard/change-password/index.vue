<template>
    <div class="change_password_block">
        <div class="row">
            <div class="col-md-6">
                <label>Current Password</label>
                <div class="mb-4">
                    <div class="pwd_block">
                        <input :type="pwd_field_type" v-model="formData.current_password" placeholder="Enter your current password" class="form-control" ref="cur_password" />
                        <span @click="password_input_setup">
                            <i v-if="password_show" class="fa fa-eye-slash"></i>
                            <i v-else class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>New Password</label>
                <div class="mb-4">
                    <input type="password" v-model="formData.new_password" placeholder="Enter new password" class="form-control" ref="new_password" />
                </div>
            </div>
            <div class="col-md-6">
                <label>Confirm Password</label>
                <div class="mb-4">
                    <input type="password" v-model="con_password" class="form-control" placeholder="Confirm new password" />
                </div>
            </div>
        </div>

        <hr />
        <div align="right" class="pr-3">
            <div v-if="pre_loader"><i class="fa fa-cog fa-spin"></i> Submitting...wait</div>
            <div v-else class="submit_btn" @click="submit_form"><i class="fa fa-save mr-1"></i> Save</div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ChangePasswordBlock',
    data(){
        return {
            pre_loader: false,
            formData: {
                current_password: '',
                new_password: ''
            },
            con_password: '',
            password_show: false,
            pwd_field_type: 'password',
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.user_access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.user_access_token
                }
            };
            return obj;
        }
    },
    methods: {
        password_input_setup: function(){
            if(this.password_show){
                this.pwd_field_type = 'password'
                this.password_show = false
            }else{
                this.pwd_field_type = 'input'
                this.password_show = true
            }
        },
        submit_form: function(){
            if(this.formData.current_password.trim()==''){
                this.$toast.error('Please enter current password', {icon: "Warning"});
                this.$refs.cur_password.focus();
                return false;
            }else if(this.formData.new_password.trim()==''){
                this.$toast.error('Please enter new number', {icon: "Warning"});
                this.$refs.new_password.focus();
                return false;
            }else if(this.formData.new_password.trim()==this.formData.current_password.trim()){
                this.$toast.error('New password same to current password. Change it', {icon: "Warning"});
                this.$refs.new_password.focus();
                return false;
            }else if(this.formData.new_password.trim().length<8){
                this.$toast.error('Please enter at leaset 8 characters', {icon: "Warning"});
                this.$refs.new_password.focus();
                return false;
            }else if(this.formData.new_password.trim()!==this.con_password.trim()){
                this.$toast.error('New password mismatch', {icon: "Warning"});
                this.$refs.new_password.focus();
                return false;
            }

            if(confirm('Are you sure to submit it?')){

                this.pre_loader = true;
                
                this.$axios.post('/api/users/change-password', this.formData, this.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    if(response.data.status) {
                        this.reset_form();
                        this.pre_loader = false;

                        await this.$swal("Good job!", "Password has been changed successfully.", "success");                    
                    }else{
                        this.$toast.error(response.data.msg, {icon: "error_outline"})
                        this.pre_loader = false;                
                    }
                }).catch(e => {
                    // console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.pre_loader = false;
                });
            }
        },
        reset_form: function(){
            this.formData = {
                current_password: '',
                new_password: ''
            }
            this.con_password = ''
        }
    }
}
</script>
<style lang="scss" scoped>
    .change_password_block{
        padding: 25px 0
    }
    label{
        font-size: 12px;
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
    .submit_btn{        
        display: inline-block;
        padding: 3px 15px;
        font-size: 13px;
        font-weight: bold;
        background-color: #ff6600;
        color: #fff;
        cursor: pointer;
        text-align: center;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .submit_btn:hover{
        background-color: #cd5200;
    }
</style>