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
                        <label>Content Title</label>
                        <div><input type="text" placeholder="Enter content title" class="form-control" v-model="formData.content_title"  ref="content_title" /></div>
                    </div>
                    <div class="item_block">
                        <label>Browse Media Item</label>
                        <PhotoInputBlock />
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
                        <label>Choose Categories</label>
                        <div class="list_block category_list_block" ref="category_block">                            
                            <div><select v-model="formData.cat_id" class="form-control">
                                <option value="">None</option>
                                <option v-for="(item,index) in $parent.category_list" :value="item.id" :key="index">{{ item.category_name }}</option>                                
                            </select></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>Choose Content Type</label>
                        <div><select v-model="formData.content_type" class="form-control">
                            <!-- <option value="">Select One</option> -->
                            <option value="1">Photo</option>
                            <!-- <option value="2">Video</option>     -->
                        </select></div>
                    </div>
                    <div class="mb-4">
                        <label>Allow Watermark</label>
                        <SwithcBtn :status="formData.watermark" :index="'watermark'" />
                        <input type="hidden" v-model="formData.watermark" />
                    </div>
                    <div v-if="formData.watermark" class="mb-4">
                        <label>Watermark Position</label>
                        <select v-model="formData.watermark_pos" class="form-control">
                            <option value="center">Center</option>
                            <option value="top-left">Top Left</option>
                            <option value="top-right">Top Right</option>
                            <option value="bottom-left">Bottom Left</option>
                            <option value="bottom-right">Bottom Right</option>
                        </select>
                    </div>
                    <div>
                        <label>Status</label>
                        <SwithcBtn :status="formData.status" :index="'status'" />
                        <input type="hidden" v-model="formData.status" />
                    </div>                    
                </div>

                <div v-if="external_req" class="submit-btn-block mt-4">
                    <button type="button" class="save_btn" @click="formSubmit">
                        <i class="far fa-check-circle"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
import PhotoInputBlock from './components/photo_input_block'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number,
        external_req: Boolean
    },
    components: {
        SwithcBtn,
        PhotoInputBlock
    },    
    data(){
        return {
            form_loader: false,
            formData: {                
                content_title: '',
                cat_id: '',
                content_type: 1,
                content: null,
                exist_content: null,
                watermark: false,
                watermark_pos: 'center',
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
            
            this.$axios.get('/api/media-galleries/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    content_title: getData.content_title,
                    cat_id: getData.cat_id?getData.cat_id:'',
                    content_type: getData.content_type,
                    content: getData.content,
                    exist_content: getData.exist_content,
                    watermark: getData.watermark,
                    watermark_pos: getData.watermark_pos,
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
            if(!this.external_req) this.$parent.form_submit_state(false)

            if(this.formData.content_title.trim()==''){
                this.$toast.error('Please enter type title', {icon: "Warning"});
                this.$refs.content_title.focus();
                return false;
            } else if(this.formData.content_type==''){
                this.$toast.error('Please choose content type', {icon: "Warning"});
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
                
                await this.$parent.mediaGallerySubmit(submit_data);
                
                this.req_submit = false;
                this.$parent.load_data();                
                
                await this.$swal("Good job!", "Data has been "+ (this.form_action == 'save'?'inserted':'updated') +" successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    if(!this.external_req) this.$parent.add_new_entity(false)
                }else{
                    this.formData.exist_content = this.$store.state.media_gallery_info.get_data['content']
                }
            }            
        },
        form_reset(){
            this.formData = {
                content_title: '',
                cat_id: '',
                content_type: '',
                content: null,
                exist_content: null,
                watermark: false,
                watermark_pos: 'center',
                status: true
            }            
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
    .submit-btn-block{
        text-align: right;
    }
    .submit-btn-block > button{                
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: center;
        outline: 0px;
        color: #fff;
        border: none;
        padding: 5px 15px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.4s;
    }
    .submit-btn-block > button i{
        margin-right: 5px;
    }
    .submit-btn-block > button.add_btn{
        background-color: #0097f7;
    }
    .submit-btn-block > button.add_btn:hover{
        background-color: #006699;
    }
    .submit-btn-block > button.cancel_btn{
        background-color: #CD0000;
    }
    .submit-btn-block > button.cancel_btn:hover{
        background-color: darkorange;
    }
    .submit-btn-block > button.save_btn{
        background-color: green;
    }
    .submit-btn-block > button.save_btn:hover{
        background-color: lightseagreen;
    }
    .submit-btn-block.sticky{
        position: fixed;
        z-index: 100;
        right: 0px;
        top: 0;
        background-color: #fff;
        height: 59px;
        padding: 13px 25px 0 0;
    }
</style>