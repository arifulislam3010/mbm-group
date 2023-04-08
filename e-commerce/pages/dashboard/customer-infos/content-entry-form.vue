<template>
    <div class="content_entry_form_block">
        <RainbowLoader v-if="req_submit" />
        <div class="row">
            <div class="col-md-8">
                <div v-if="form_loader" class="row">
                    <div class="col-md-12">
                        <div class="form_block">
                            <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                        </div>
                    </div>
                </div>
                <div v-else class="form_block">                    
                    <div class="mb-4">
                        <label>Name</label>
                        <div><input type="text" placeholder="Enter customer full name" class="form-control" v-model="formData.user_info.full_name" ref="full_name" /></div>
                    </div>
                    <div class="mb-4">
                        <label>E-mail</label>
                        <div><input type="text" placeholder="Enter customer e-mail" class="form-control" v-model="formData.email" ref="email" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Mobile</label>
                        <div><input type="text" placeholder="Enter customer mobile number" class="form-control" v-model="formData.mobile" ref="mobile" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Password <sup><i class="fa fa-lock"></i></sup></label>
                        <div v-if="edit_content_id>0" class="change_action_btn mb-2">
                            <i v-if="pwd_change_allow" class="far fa-check-circle" @click="pwd_change_allow=false"></i>
                            <i v-else class="far fa-circle" @click="pwd_change_allow=true"></i>
                            <span>Want to change</span>
                        </div>
                        <!-- {{ pwd_change_allow + ' ' + edit_content_id }} -->
                        <div v-if="pwd_change_allow || edit_content_id=='' || edit_content_id==null"><input type="password" placeholder="Enter password" class="form-control" v-model="formData.password" /></div>
                    </div>
                    <div v-if="pwd_change_allow || edit_content_id=='' || edit_content_id==null" class="mb-4">
                        <label>Confirm Password</label>
                        <div><input type="password" placeholder="Confirm password" class="form-control" v-model="confirm_password" /></div>
                    </div>                    
                </div>
            </div>
            <div class="col-md-4">
                <div v-if="form_loader" class="row">
                    <div class="col-md-12">
                        <div class="info_block">
                            <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                        </div>
                    </div>
                </div>
                <div v-else class="info_block">                    
                    <div class="mb-4">
                        <label>Choose Gender</label>
                        <div class="mb-4">                            
                            <select v-model="formData.user_info.gender_id" class="form-control">
                                <option value="">Select One</option>
                                <option v-for="(gender_name,id) in $store.state.gender_list" :key="id" :value="id">{{ gender_name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>Verify</label>
                        <SwithcBtn :status="formData.verified" :index="'verified'" />
                        <input type="hidden" v-model="formData.verified" />
                    </div>
                    <div>
                        <label>Status</label>
                        <SwithcBtn :status="formData.status" :index="'status'" />
                        <input type="hidden" v-model="formData.status" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        SwithcBtn
    },    
    data(){
        return {            
            form_loader: false,
            formData: {
                email: '',
                mobile: '',
                password: '',                
                verified: 0,
                status: true,
                user_info: {
                    full_name: '',                    
                    gender_id: 1
                }
            },
            confirm_password: '',
            pwd_change_allow: false,
            req_submit: false,
            form_action: 'save'            
        }
    },
    computed: {
        form_submit_status () {
            return this.$store.state.form_submit_status        
        }
    },
    watch: {
        edit_content_id (val) {
            this.load_req_data(val);
        },
        form_submit_status (status) {
            if(status) this.formSubmit();        
        }
    },
    mounted(){
        if(this.edit_content_id) this.load_req_data(this.edit_content_id);
        if(this.$store.state.form_submit_status) this.formSubmit();
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },        
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/customer-infos/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    email: getData.email,
                    mobile: getData.mobile,
                    password: '',
                    verified: getData.verified,
                    status: getData.status,
                    user_info: getData.user_info?getData.user_info:this.formData.user_info
                }
                                
                if(this.formData.email==null) this.formData.email = ''
                if(this.formData.mobile==null) this.formData.mobile = ''
                if(this.formData.user_info.full_name==null) this.formData.user_info.full_name = ''
                if(this.formData.user_info.gender_id==null) this.formData.user_info.gender_id = ''

                this.form_loader = false;
                this.form_action = 'update';
            }).catch(e => {
                console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.form_loader = false;
            });
        },
        async formSubmit(){
            this.$parent.form_submit_state(false)

            if(this.formData.user_info.full_name.trim()==''){
                this.$toast.error('Please enter full name', {icon: "Warning"});
                this.$refs.full_name.focus();
                return false;
            }else if(this.formData.email.trim()=='' && this.formData.mobile.trim()==''){
                this.$toast.error('Please enter email or mobile must', {icon: "Warning"});
                this.$refs.email.focus();
                return false;
            } else if(this.formData.email.trim()!=='' && !this.$validateEmail(this.formData.email.trim())){
                this.$toast.error('Please valid email address', {icon: "Warning"});
                this.$refs.email.focus();
                return false;
            } else if(this.formData.mobile.trim()!=='' && !this.$validateContactNumber(this.formData.mobile.trim())){
                this.$toast.error('Please valid mobile number', {icon: "Warning"});
                this.$refs.mobile.focus();
                return false;
            } else if((this.pwd_change_allow || this.edit_content_id=='') && this.formData.password.trim()==''){
                this.$toast.error('Please enter password', {icon: "Warning"});
                return false;
            } else if((this.pwd_change_allow || this.edit_content_id=='') && this.formData.password.trim()!==this.confirm_password.trim()){
                this.$toast.error('Password mismatch', {icon: "Warning"});
                return false;
            } else if(this.formData.user_info.gender_id==''){
                this.$toast.error('Please choose gender', {icon: "Warning"});
                return false;
            } 

            if(confirm('Are you sure to submit it?')){
                if(this.formData.password=='') delete this.formData.password;
                // setup submitted data
                let submit_data = {
                    id: this.$parent.user_id,
                    access_token: this.$parent.user_access_token,
                    data: this.formData,
                    action: this.form_action,
                    edit_id: this.edit_content_id
                }

                // call for submit
                this.req_submit = true;
                
                await this.$parent.customerSubmit(submit_data);
                
                this.req_submit = false;
                this.$parent.load_data();                
                
                await this.$swal("Good job!", "Data has been saved successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    this.$parent.add_new_entity(false)
                }
            }            
        },
        form_reset(){
            this.formData = {
                email: '',
                mobile: '',
                password: '',                
                verified: 0,
                status: true,
                user_info: {
                    full_name: '',                    
                    gender_id: 1
                }
            },
            this.confirm_password = ''            
        }
    }
}
</script>
<style scoped>
    .form_block,.info_block{
        display: block;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 20px;
    }
    label > i{
        color: #666
    }
    .change_action_btn{
        font-size: 14px;
        color: #999
    }
    .change_action_btn > i{
        cursor: pointer;
    }
    .info_block .parent-dropdown{
        margin: 0; padding: 0 8px
    }
    .info_block .parent-dropdown :deep(option){
        padding: 10px
    }
</style>