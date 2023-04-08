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
                        <label>User E-mail</label>
                        <div><input type="text" placeholder="Enter user e-mail" class="form-control" v-model="formData.email" ref="email" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Name</label>
                        <div><input type="text" placeholder="Enter user full name" class="form-control" v-model="formData.user_info.full_name" /></div>
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
                    <div class="item_block mb-4">
                        <label>User Role</label>
                        <select v-model="formData.role_info.role_id" class="form-control parent-dropdown">
                            <!-- <option value="">Select One</option> -->
                            <option v-for="(item,index) in user_role_list" :key="index" :value="item.id">{{ item.role_title }}</option>                            
                        </select>
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
            user_role_list: [],
            form_loader: false,
            formData: {
                email: '',                
                password: '',
                user_type: 1,
                verified: 0,
                status: true,
                user_info: {
                    full_name: ''
                },
                role_info: {
                    role_id: ''
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

        // load admin user roles
        this.load_admin_user_roles();
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        async load_admin_user_roles(){            
            let url = '/api/user-role-infos';
            
            this.user_role_list = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                console.log(response)
                this.user_role_list = response.data.data
            }).catch(e => {
                this.$toast.error('User role load failed!!!', {icon: "error_outline"})
            });
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/users/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                let getRoleInfo = {
                    id: ''
                };

                if(getData.role_info && getData.role_info.id){
                    getRoleInfo = getData.role_info
                }

                this.formData = {
                    email: getData.email,
                    password: '',
                    verified: getData.verified,
                    status: getData.status,
                    user_info: getData.user_info,
                    role_info: getRoleInfo
                }                

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

            if(this.formData.email.trim()==''){
                this.$toast.error('Please enter email', {icon: "Warning"});
                this.$refs.email.focus();
                return false;
            } else if(this.formData.user_info.full_name.trim()==''){
                this.$toast.error('Please enter full name', {icon: "Warning"});
                return false;
            } else if((this.pwd_change_allow || this.edit_content_id=='') && this.formData.password.trim()==''){
                this.$toast.error('Please enter password', {icon: "Warning"});
                return false;
            } else if((this.pwd_change_allow || this.edit_content_id=='') && this.formData.password.trim()!==this.confirm_password.trim()){
                this.$toast.error('Password mismatch', {icon: "Warning"});
                return false;
            } else if(this.formData.role_info.role_id==''){
                this.$toast.error('Please choose one role', {icon: "Warning"});
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
                
                await this.$parent.userSubmit(submit_data);
                
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
                password: '',
                user_type: 1,
                verified: 0,
                status: true,
                user_info: {
                    full_name: ''
                },
                role_info: {
                    role_id: ''
                }
            },
            this.confirm_password = ''            
        }
    }
}
</script>
<style lang="scss" scoped>
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
        margin: 0; padding: 0 8px;
        :deep(option){
            padding: 10px
        }
    }
</style>