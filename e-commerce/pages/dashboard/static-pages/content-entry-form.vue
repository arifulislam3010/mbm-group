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
                        <label>Page Title</label>
                        <div><input type="text" placeholder="Enter page title" class="form-control" v-model="formData.page_title" @keyup="slug_config" ref="page_title" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Slug</label>
                        <div><input type="text" placeholder="Enter slug" class="form-control" v-model="formData.slug" readonly /></div>
                    </div>
                    <div class="mb-4">
                        <label>Details</label>
                        <vue-editor :editorToolbar="customToolbar" v-model="formData.details" />
                    </div>
                    <div class="mb-4">
                        <PhotoGallery ref="photo_list" />
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
                        <label>Display On</label>
                        <div class="mb-3">
                            <select v-model="formData.display_on" class="form-control">
                                <option value="">Choose One</option>
                                <option v-for="(item,index) in $store.state.display_on_list" :key="index" :value="index">{{ item }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>Google Map Display</label>
                        <SwithcBtn :status="formData.google_map_display" :index="'google_map_display'" />
                        <input type="hidden" v-model="formData.google_map_display" />
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
import PhotoGallery from './components/photo-gallery/index'
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
import Search from '../../index/search.vue'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        PhotoGallery,
        SwithcBtn,
        Search
    },    
    data(){
        return {
            form_loader: false,            
            formData: {
                page_title: '',                
                slug: '',                
                details: '',
                photo_infos: [],
                display_on: 1,
                google_map_display: false,
                status: true
            },
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                ["bold", "italic", "underline", "strike"], // toggled buttons
                [
                    { align: "" },
                    { align: "center" },
                    { align: "right" },
                    { align: "justify" }
                ],
                ["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                // ["link", "image", "video"],
                ["link"],
                ["clean"] // remove formatting button
            ],
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
        slug_config(){            
            this.formData.slug = this.$strSlug(this.formData.page_title)
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/static-page-infos/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data.data;

                /**
                 * Photo infos
                 */
                let getPhotoInfos = [];
                getData.photo_infos.forEach((v,i) => {
                    if(v.page_photo_data) getPhotoInfos.push(v.page_photo_data);                    
                });

                this.formData = {
                    page_title: getData.page_title,                
                    slug: getData.slug,
                    details: getData.details,
                    photo_infos: getPhotoInfos,
                    display_on: getData.display_on,
                    google_map_display: getData.google_map_display,
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

            if(this.formData.page_title.trim()==''){
                this.$toast.error('Please enter page name', {icon: "Warning"});
                this.$refs.page_title.focus();
                return false;
            } else if(this.formData.details.trim()==''){
                this.$toast.error('Please enter details content', {icon: "Warning"});
                return false;
            } else if(this.formData.display_on==''){
                this.$toast.error('Please choose display on', {icon: "Warning"});
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
                
                await this.$parent.staticPageSubmit(submit_data);
                
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
                page_title: '',                
                slug: '',                
                details: '',
                photo_infos: [],
                display_on: 1,
                google_map_display: false,
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
</style>