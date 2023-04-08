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
                        <label>Color Title</label>
                        <div><input type="text" placeholder="Enter color title" class="form-control" v-model="formData.color_title" ref="color_title" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Color Code</label>
                        <div class="color_input_block">
                            <span :style="{'background-color': (formData.color_code?formData.color_code:'inherit')}"></span>
                            <input type="text" placeholder="Enter color code" class="form-control" v-model="formData.color_code" ref="color_code" />
                        </div>
                    </div>                    
                    <div>
                        <label>Status</label>
                        <SwithcBtn :status="formData.status" :index="'status'" />
                        <input type="hidden" v-model="formData.status" />
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
                color_title: '',
                color_code: '',
                status: true
            },
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
            if(val) this.load_req_data(val);
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
            
            this.$axios.get('/api/product-color-info/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    color_title: getData.color_title,
                    color_code: getData.color_code,
                    status: getData.status
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

            if(this.formData.color_title.trim()==''){
                this.$toast.error('Please enter color title', {icon: "Warning"});
                this.$refs.color_title.focus();
                return false;
            }else if(this.formData.color_code.trim()==''){
                this.$toast.error('Please enter color code', {icon: "Warning"});
                this.$refs.color_code.focus();
                return false;
            }

            if(confirm('Are you sure to submit it?')){
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
                
                await this.$parent.colorSubmit(submit_data);
                
                this.req_submit = false;
                this.$parent.load_data();                
                
                await this.$swal("Good job!", "Data has been "+ (this.form_action == 'save'?'inserted':'updated') +" successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    this.$parent.add_new_entity(false)
                }
            }            
        },
        form_reset(){
            this.formData = {
                color_title: '',
                status: true
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .form_block,.info_block{
        display: block;
        background-color: $white;
        border: 1px solid $default_border_color;
        padding: 20px;
    }
    .color_input_block{
        position: relative;

        & > input{
            padding-left: 40px
        }

        & > span{
            position: absolute;
            left: 8px;
            top: 8px;
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 1px solid $default_border_color;
            border-radius: 3px;
        }
    }
</style>