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
                        <label>Category Name</label>
                        <div><input type="text" placeholder="Enter category name" class="form-control" v-model="formData.category_name" @keyup="slug_config" ref="category_name" /></div>
                    </div>
                    <div class="mb-4">
                        <label>Slug</label>
                        <div><input type="text" placeholder="Enter slug" class="form-control" v-model="formData.slug" readonly /></div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-md-4 mb-3">
                            <label>Display on navigation</label>
                            <SwithcBtn :status="formData.display_on_nav" :index="'display_on_nav'" />
                            <input type="hidden" v-model="formData.display_on_nav" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Display on Body</label>
                            <SwithcBtn :status="formData.display_on_body" :index="'display_on_body'" />
                            <input type="hidden" v-model="formData.display_on_body" />
                        </div> -->
                        <div class="col-md-4 mb-3">
                            <label>Status</label>
                            <SwithcBtn :status="formData.status" :index="'status'" />
                            <input type="hidden" v-model="formData.status" />
                        </div>                        
                    </div>
                    <!-- <div class="mb-4">
                        <label>Banner for category page</label>
                        <BannerInputBlock />
                    </div> -->
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
                        <label>Parent category</label>
                        <select v-model="formData.parent_id" class="form-control parent-dropdown" size="7">
                            <option value="null" :selected="{selected:formData.parent_id}">No parent</option>
                            <template v-for="(item,index) in category_list" >
                                <option v-if="item.id!==edit_content_id" :key="index" :value="item.id">{{ item.category_name }}</option>
                            </template>
                        </select>
                    </div>
                    <!-- <div class="item_block">
                        <label>Icon</label>
                        <IconInputBlock />
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import BannerInputBlock from './components/banner_input_block'
import IconInputBlock from './components/icon_input_block'
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        SwithcBtn,
        BannerInputBlock,
        IconInputBlock
    },    
    data(){
        return {
            form_loader: false,
            formData: {
                category_name: '',                
                slug: '',
                parent_id: null,
                // display_on_nav: false,
                // display_on_body: false,
                banner_image: null,
                exist_banner_image: null,
                icon: null,
                exist_icon: null,
                status: true
            },
            req_submit: false,
            form_action: 'save'            
        }
    },
    computed: {
        form_submit_status () {
            return this.$store.state.form_submit_status        
        },
        category_list () {
            return this.$parent.contents
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
            this.formData.slug = this.$strSlug(this.formData.category_name)
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/media-categories/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    category_name: getData.category_name,
                    slug: getData.slug,
                    parent_id: getData.parent_id,
                    // display_on_nav: getData.display_on_nav=='1'?true:false,
                    // display_on_body: getData.display_on_body=='1'?true:false,
                    banner_image: getData.banner_image,
                    exist_banner_image: getData.exist_banner_image,
                    icon: getData.icon,
                    exist_icon: getData.exist_icon,
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

            if(this.formData.category_name.trim()==''){
                this.$toast.error('Please enter category name', {icon: "Warning"});
                this.$refs.category_name.focus();
                return false;
            } else if(this.formData.slug.trim()==''){
                this.$toast.error('Please enter slug', {icon: "Warning"});
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
                
                await this.$parent.mediaCategorySubmit(submit_data);
                
                // console.log('Response data', this.$store.state.media_category_info.get_data)
                
                this.req_submit = false;

                //console.log(this.$store.state.media_category_info)
                if(this.$store.state.media_category_info.msg){
                    this.$toast.error(this.$store.state.media_category_info.msg, {icon: "Warning"});
                    this.$refs.category_name.focus();
                    return false;
                }

                this.$parent.load_data();

                await this.$swal("Good job!", "Data has been "+ (this.form_action == 'save'?'inserted':'updated') +" successfully.", "success");

                if(this.form_action == 'save'){
                    this.form_reset();
                    this.$parent.add_new_entity(false)
                }else{
                    this.formData.exist_icon = this.$store.state.media_category_info.get_data['icon']
                }
            }
        },
        form_reset(){
            this.formData = {
                category_name: '',                
                slug: '',
                parent_id: null,
                // display_on_nav: false,
                // display_on_body: false,
                banner_image: null,
                exist_banner_image: null,
                icon: null,
                exist_icon: null,
                status: true
            }            
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
    .info_block .parent-dropdown{
        margin: 0; padding: 0;
        :deep(option){
            padding: 10px
        }
    }
</style>