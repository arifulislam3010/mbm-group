<template>
    <div class="content_entry_form_block">
        <RainbowLoader v-if="req_submit" />        
        <div v-if="form_loader" class="row">
            <div class="col-md-12">
                <div class="form_block">
                    <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                    <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                    <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                </div>
            </div>
        </div>
        <template v-else>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label>Prescription title</label>
                    <div><input type="text" class="form-control" placeholder="Enter prescription title" v-model="formData.prescription_title" @keyup.enter="submit_form" ref="prescription_title" /></div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label>Patient name</label>
                            <div><input type="text" class="form-control" placeholder="Enter patient name" v-model="formData.patient_name" @keyup.enter="submit_form" ref="patient_name" /></div>
                        </div>                
                        <div class="col-md-12 mb-3">
                            <label>Patient gender</label>
                            <div><select class="form-control" v-model="formData.patient_gender_id" ref="patient_gender_id">
                                <option value="">Choose One</option>
                                <option v-for="(item,gender_id) in $store.state.gender_list" :key="gender_id" :value="gender_id">{{ item }}</option>
                            </select></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Patient age (Year)</label>
                            <div><input type="number" class="form-control" placeholder="Enter patient age" v-model="formData.patient_age" @keyup.enter="submit_form" ref="patient_age" /></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div v-if="formData.file_content" class="prescription_view_block">
                        <img :src="formData.file_content" />
                        <div class="action_btn_block">
                            <span class="remove" @click="formData.file_content=''"><i class="fa fa-times"></i> Remove</span>
                            <span class="change" @click="upload_prescription"><i class="fa fa-exchange-alt"></i> Change</span>
                        </div>
                    </div>
                    <div v-else class="upload_prescription_block" @click="upload_prescription">
                        <i class="fa fa-cloud-upload-alt fa-4x"></i>
                        <div>
                            <small>Click here to upload your prescriptin photo</small>
                        </div>
                    </div>
                    <input class="prescription_file_input" type="file" ref="input_file" @change="load_image" accept="image/*" capture />
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFormBlock',
    props: {
        edit_content_id: Number
    },    
    components: {
        SwithcBtn        
    },
    data(){
        return {
            form_loader: false,
            edit_content_load: false,
            req_submit: false,
            formData: {
                prescription_title: '',
                patient_name: '',
                patient_age: '',
                patient_gender_id: 1,
                file_name: '',
                file_content: '',
                exist_content: '',
                status: 1
            }
        }
    },
    watch: {
        edit_content_id (val) {
            if(val) this.load_req_data(val);
        }
    },
    computed: {
        form_submit_allow: function(){
            for(var index in this.edit_status){
                if(this.edit_status[index]) return true
            }
        }
    },
    async mounted(){
        if(this.edit_content_id) this.load_req_data(this.edit_content_id);
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        upload_prescription: function(){
            this.$refs.input_file.click();
        },
        load_image(e){
            var files = e.target.files || e.dataTransfer.files; 
            if (!files.length) return;
            console.log('File info', files[0]);
            this.CreateImage(files[0]);
        },
        CreateImage(file){
            var reader = new FileReader();

            reader.onload = (e) => {
                this.formData.file_content = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/my-prescriptions/edit/' + id, this.$parent.header_config).then( async (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    prescription_title: getData.prescription_title,
                    patient_name: getData.patient_name,
                    patient_age: getData.patient_age,
                    patient_gender_id: getData.patient_gender_id,
                    file_name: getData.file_name,
                    file_content: getData.file_content,
                    exist_content: getData.exist_content,
                    status: 1
                }                

                this.form_loader = false;
                this.form_action = 'update';
            }).catch(e => {
                console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.form_loader = false;
            });
        },
        async submit_form(){
            if(this.formData.prescription_title.trim()==''){
                this.$toast.error('Please enter prescription title', {icon: "Warning"});
                this.$refs.prescription_title.focus();
                return false;
            }else if(this.formData.patient_name.trim()==''){
                this.$toast.error('Please enter patient name', {icon: "Warning"});
                this.$refs.patient_name.focus();
                return false;
            }else if(this.formData.patient_age==''){
                this.$toast.error('Please enter patient age', {icon: "Warning"});
                this.$refs.patient_age.focus();
                return false;
            }else if(this.formData.patient_gender_id==''){
                this.$toast.error('Choose patient gender', {icon: "Warning"});
                this.$refs.patient_gender_id.focus();
                return false;
            }else if(this.formData.file_name=='' && this.formData.file_content==''){
                this.$toast.error('Browse prescription file', {icon: "Warning"});
                // this.$refs.file_name.focus();
                return false;
            }

            if(confirm('Are you sure to submit it?')){
                this.req_submit = true;
                
                this.$axios.post('/api/my-prescriptions/' + (this.edit_content_id?'update/' + this.edit_content_id:'store'), this.formData, this.$parent.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    await this.$swal("Good job!", "Data has been saved successfully.", "success")

                    this.$parent.load_data()
                    
                    this.$parent.entry_form_open = false                    
                    this.$router.push(this.$parent.cur_path)
                    
                    this.reset_form()
                    
                    this.req_submit = false
                }).catch(e => {
                    // console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.req_submit = false;
                });
            }
        },
        reset_form: function(){
            this.formData = {
                prescription_title: '',
                patient_name: '',
                patient_age: '',
                patient_gender_id: 1,
                file_name: '',
                file_content: '',
                exist_content: '',
                status: 1
            }
        }
    }
}
</script>
<style scoped>
    label{
        font-size: 12px;
    }
    .prescription_file_input{
        display: none;
    }
    .prescription_view_block{
        position: relative;
        background-color: #f7fff7;
        border: 1px solid #ddd;
        min-height: 200px;
    }
    .prescription_view_block > img{
        width: 100%; height: 100%;
        padding: 5px;
        object-fit: contain;
    }
    .prescription_view_block > .action_btn_block{
        position: absolute;
        width: 80%;
        left: 10%;
        bottom: 10px;
        text-align: center;
        z-index: 5;
    }
    .prescription_view_block > .action_btn_block > span{
        display: inline-block;
        padding: 2px 14px 2px 10px;
        margin: 2px;
        cursor: pointer;
        background-color: #363;
        color: #fff;
        font-size: 10px;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .prescription_view_block > .action_btn_block > span i{
        margin-right: 5px;
    }
    .prescription_view_block > .action_btn_block > span:hover{
        background-color: rgb(34, 90, 112);
    }
    .prescription_view_block > .action_btn_block > span.remove{
        background-color: #CD0000;
    }
    .prescription_view_block > .action_btn_block > span.remove:hover{
        background-color: #ac2c2c;
    }
    .upload_prescription_block{
        width: 100%;
        height: 100%;
        background-color: #eee;
        border: 1px solid #ddd;
        text-align: center;
        padding: 20% 15px;
        cursor: pointer;
        transition: all 0.4s;
    }
    .upload_prescription_block:hover{
        background-color: #f7f7f7;
    }
    .upload_prescription_block > i{
        color: #ccc; margin-bottom: 10px;
    }
    .upload_prescription_block:hover > i{
        color: #cdcdcd;
    }
</style>