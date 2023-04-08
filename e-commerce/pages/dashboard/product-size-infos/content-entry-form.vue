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
                        <label>Size</label>
                        <div><input type="text" placeholder="Enter tag name" class="form-control" v-model="formData.size_title" ref="size_title" /></div>
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
                    <!-- <div class="item_block mb-4">
                        <label>Choose Categories</label>
                        <div class="list_block" ref="category_block">
                            <CategoryList v-if="$parent.category_list && $parent.category_list.length>0" :items="$parent.category_list" :cat_parent_ids="cat_parent_ids" :cat_ids="formData.cat_id" />
                        </div>
                    </div> -->
                    <div class="item_block">
                        <label>Size Type</label>
                        <select v-model="formData.size_type_id" class="form-control parent-dropdown" size="3" ref="size_type">
                            <option value="">Choose One</option>
                            <template v-if="$store.state.product_size_types">
                                <option v-for="(title,id) in $store.state.product_size_types" :key="'psk-' + id" :value="id">{{ title }}</option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
// import CategoryList from './components/category-list'
export default {
    name: 'ContentEntryFromBlock',
    props: {
        edit_content_id: Number
    },
    components: {
        SwithcBtn,
        // CategoryList
    },
    data(){
        return {
            form_loader: false,
            formData: {
                size_title: '',
                size_type_id: '',
                status: true,
                cat_id: {},
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

        $(document).on('click', '.item_block.active', function(){
            $(this).find('i').removeClass('fa-check-square active').addClass('fa-square');
            $(this).removeClass('active');
            $(this).addClass('inactive');
        });

        $(document).on('click','.item_block.inactive', function(){
            $(this).find('i').removeClass('fa-square').addClass('fa-check-square active');
            $(this).removeClass('inactive');
            $(this).addClass('active');
        });
    },
    methods: {
        switch_data(index,status){
            this.formData[index] = status
        },
        async load_req_data(id){
            this.form_loader = true;

            this.$axios.get('/api/product-size-info/edit/' + id, this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data)
                let getData = response.data;

                /**
                 * Selected Categories
                 */
                let getCatIds = {};
                getData.cat_ids.forEach((v,i) => {
                    getCatIds[v.product_cat_id] = true
                });

                this.formData = {
                    size_title: getData.size_title,
                    size_type_id: getData.size_type_id,
                    status: getData.status,
                    cat_id: getCatIds,
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

            if(this.formData.size_title.trim()==''){
                this.$toast.error('Please enter size title', {icon: "Warning"});
                this.$refs.size_title.focus();
                return false;
            }else if(Object.keys(this.formData.cat_id).length==0){
                this.$toast.error('Please choose category', {icon: "Warning"});
                this.$refs.category_block.focus();
                return false;
            }else if(this.formData.size_type_id==''){
                this.$toast.error('Please choose size type', {icon: "Warning"});
                this.$refs.size_type.focus();
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

                await this.$parent.sizeSubmit(submit_data);

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
                size_title: '',
                size_type_id: '',
                cat_id: {},
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
    .list_block{
        border: 1px solid #dddddd;
        overflow-y: auto;
        height: 140px;
        padding: 5px;
        border-radius: 5px
    }
    .info_block .parent-dropdown{
        margin: 0; padding: 0
    }
    .info_block .parent-dropdown :deep(option){
        padding: 10px
    }
</style>
