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
                        <label>Design Title</label>
                        <div><input type="text" placeholder="Enter design title" class="form-control" v-model="formData.design_title" ref="design_title" /></div>
                    </div>

                    <div class="mb-4">
                        <ProductPhotoGallery :product_photo_infos="formData.photos" ref="photo_list" />
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
                        <label>Choose category</label>
                        <select v-model="formData.cat_id" class="form-control parent-dropdown" size="7">
                            <template v-for="(item,index) in category_list" >
                                <option :key="index" :value="item.id">{{ item.category_name }}</option>
                            </template>
                        </select>
                    </div>
                    <div class="item_block mb-4">
                        <label>Price ({{ $store.state.currency_info.title }})</label>
                        <div><input type="number" placeholder="i.e 100" min="1" class="form-control" v-model="formData.price" ref="price" /></div>
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
import ProductPhotoGallery from './components/product-photo-gallery/index'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {        
        SwithcBtn,
        ProductPhotoGallery
    },    
    data(){
        return {
            category_list: [],
            form_loader: false,
            formData: {
                design_title: '',
                cat_id: null,
                price: 0,
                photos: [],
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

        // load categories
        this.load_categories();
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        async load_categories(){            
            let url = '/api/categories';
            
            this.category_list = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                console.log(response)
                this.category_list = response.data.data
            }).catch(e => {
                this.$toast.error('Category load failed!!!', {icon: "error_outline"})
            });
        },
        async load_req_data(id){
            this.form_loader = true;
            
            this.$axios.get('/api/custom-product-designs/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data.data)
                let getData = response.data.data;

                this.formData = {
                    design_title: getData.design_title,
                    cat_id: getData.cat_info?getData.cat_info.id:'',
                    price: getData.price,
                    photos: getData.photo_infos?getData.photo_infos:[],
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

            if(this.formData.design_title.trim()==''){
                this.$toast.error('Please enter design title', {icon: "Warning"});
                this.$refs.design_title.focus();
                return false;
            } else if(this.formData.cat_id==''){
                this.$toast.error('Please choose category', {icon: "Warning"});
                return false;
            } else if(this.formData.price<=0){
                this.$toast.error('Please enter valid amount of price', {icon: "Warning"});
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
                
                await this.$parent.customProductDesignSubmit(submit_data);
                
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
                design_title: '',
                cat_id: null,
                price: 0,
                photos: [],
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
    .info_block .parent-dropdown{
        margin: 0; padding: 0
    }
    .info_block .parent-dropdown :deep(option){
        padding: 10px
    }
</style>