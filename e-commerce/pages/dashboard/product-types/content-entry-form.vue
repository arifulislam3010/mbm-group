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
                        <label>Type Title</label>
                        <div><input type="text" placeholder="Enter type title" class="form-control" v-model="formData.type_title" @keyup="slug_config" ref="type_title" /></div>
                        <div class="mt-1"><small>Slug: <em>{{ formData.slug }}</em></small></div>
                    </div>
                    <!-- <div class="mb-4">
                        <label>Slug</label>
                        <div><input type="text" placeholder="Enter slug" class="form-control" v-model="formData.slug" readonly /></div>
                    </div> -->
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <label>Open Entry</label>
                            <SwithcBtn :status="formData.open_entry" :index="'open_entry'" />
                            <input type="hidden" v-model="formData.open_entry" />
                        </div> -->
                        <div class="col-md-6 mb-4">
                            <label>Status</label>
                            <SwithcBtn :status="formData.status" :index="'status'" />
                            <input type="hidden" v-model="formData.status" />
                        </div>
                    </div>
                    <div class="item_block">
                        <label>Icon</label>
                        <IconInputBlock />
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
                            <option value="">For All</option>
                            <template v-for="(item,index) in category_list" >
                                <option v-if="item.category_name" :key="index" :value="item.id">{{ item.category_name }}</option>
                                <!-- Sub Category -->
                                <template v-if="item.sub_categories && item.sub_categories.length>0">
                                    <template v-for="(sub_item,sub_index) in item.sub_categories" >
                                        <option v-if="sub_item.id!==edit_content_id" :key="sub_index" :value="sub_item.id">--- {{ sub_item.category_name }}</option>

                                        <!-- Sub2 Category -->
                                        <template v-if="sub_item.sub_categories && sub_item.sub_categories.length>0">
                                            <template v-for="(sub2_item,sub2_index) in sub_item.sub_categories" >
                                                <option v-if="sub2_item.id!==edit_content_id" :key="sub2_index" :value="sub2_item.id">--- --- {{ sub2_item.category_name }}</option>
                                            </template>
                                        </template>
                                    </template>
                                </template>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import IconInputBlock from './components/icon_input_block'
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        IconInputBlock,
        SwithcBtn
    },
    data(){
        return {
            category_list: [],
            form_loader: false,
            formData: {
                type_title: '',
                slug: '',
                cat_id: '',
                icon: null,
                exist_icon: null,
                icon_type: null,
                open_entry: true,
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
            this.load_req_data(val);
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
        slug_config(){
            this.formData.slug = this.$strSlug(this.formData.type_title)
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

            this.$axios.get('/api/product-types/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                this.formData = {
                    type_title: getData.type_title,
                    slug: getData.slug,
                    cat_id: getData.cat_id?getData.cat_id:'',
                    icon: getData.icon,
                    exist_icon: getData.exist_icon,
                    icon_type: getData.icon_type,
                    open_entry: getData.open_entry,
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

            if(this.formData.type_title.trim()==''){
                this.$toast.error('Please enter type title', {icon: "Warning"});
                this.$refs.type_title.focus();
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

                await this.$parent.productTypeSubmit(submit_data);

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
                type_title: '',
                slug: '',
                cat_id: '',
                icon: null,
                exist_icon: null,
                icon_type: null,
                open_entry: true,
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

