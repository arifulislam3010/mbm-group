<template>
    <div class="manage_account_block">        
        <div class="content_block">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Full Name</label>
                    <DataInfoForm :column="'full_name'" :placeholder="'Enter full name'" :type="'text'" />
                </div>
                <div class="col-md-4 mb-3">
                    <label>E-mail <sup>*</sup></label>
                    <DataInfoForm :column="'email'" :placeholder="'Enter e-mail'" :type="'email'" />
                </div>
                <div class="col-md-4 mb-3">
                    <label>Mobile <sup>*</sup></label>
                    <DataInfoForm :column="'mobile'" :placeholder="'Enter mobile'" :type="'text'" />
                </div>
                <div class="col-md-4">
                    <label>Date of birth</label>
                    <DataInfoForm :column="'dob'" :type="'date'" />
                </div>
                <div class="col-md-4">
                    <label>Gender</label>
                    <DataInfoForm :column="'gender_id'" :type="'dropdown'" :data="$store.state.gender_list" />
                </div>
            </div>
        </div>
        <hr />
        <div v-if="form_submit_allow" align="right" class="pr-3">
            <div class="submit_btn" @click="submit_form"><i class="fa fa-save mr-1"></i> Save</div>
        </div>
    </div>
</template>
<script>
import DataInfoForm from './components/DataInfoForm'
import { mapMutations } from 'vuex'
export default {
    name: 'ManageAccountBlock',
    components: {
        DataInfoForm
    },
    data(){
        return {            
            formData: {
                email: '',
                mobile: '',
                full_name: '',
                dob: '',
                gender_id: '',
                user_info: {}
            },
            edit_status: {
                full_name: false,
                mobile: false,
                email: false,
                dob: false,
                gender_id: false
            }
        }
    },
    watch: {
        $route(to, from){
            this.get_user_data = this.$store.state.auth_info.user_data
        }
    },
    computed: {
        get_user_data: function(){
            return this.$store.state.auth_info.user_data
        },
        form_submit_allow: function(){
            for(var index in this.edit_status){
                if(this.edit_status[index]) return true
            }
        }
    },
    mounted(){
        this.formData = {
            mobile: this.get_user_data.mobile,
            email: this.get_user_data.email,
            full_name: this.get_user_data.user_info.full_name,
            dob: this.get_user_data.user_info.dob,
            gender_id: this.get_user_data.user_info.gender_id?this.get_user_data.user_info.gender_id:'',
            user_info: this.get_user_data.user_info
        }        
    },
    methods: {
        ...mapMutations({
            user_data_update: 'auth_info/ADD_USER_DATA'
        }),
        async submit_form(){
            let getContactNo = this.formData.mobile

            if(this.formData.email.trim()=='' && getContactNo.trim()==''){
                this.$toast.error('Please enter email or mobile', {icon: "Warning"});
                return false;
            }else if(getContactNo.trim()!=='' && !this.$validateContactNumber(getContactNo.trim())){
                this.$toast.error('Please valid mobile number', {icon: "Warning"});
                return false;
            }

            this.$parent.pre_loader = true;
            
            this.$axios.post('/api/users/update-profile', this.formData, this.$parent.header_config).then( async (response) => {
                console.log('Get Data', response.data)

                if(response.data.status) {
                    let update_data = Object.assign({}, this.$store.state.auth_info.user_data)
                    update_data.email = this.formData.email
                    update_data.mobile = this.formData.mobile
                    update_data.user_info.full_name = this.formData.full_name
                    update_data.user_info.dob = this.formData.dob
                    update_data.user_info.gender_id = this.formData.gender_id

                    await this.user_data_update(update_data)                    

                    localStorage.setItem('user_info', JSON.stringify(update_data))

                    this.reset_form();
                    this.$parent.pre_loader = false;

                    await this.$swal("Good job!", "Data has been updated successfully.", "success");                    
                }else this.$parent.pre_loader = false;                
            }).catch(e => {
                // console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.$parent.pre_loader = false;
            });
        },
        reset_form: function(){
            this.edit_status = {
                full_name: false,
                mobile: false,
                email: false,
                dob: false,
                gender_id: false
            }
        }
    }
}
</script>
<style scoped>
    .manage_account_block{
        position: relative;        
        height: 100%;
    }
    .content_block{
        display: block;
        padding: 15px;
    }
    label{
        font-size: 12px;
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