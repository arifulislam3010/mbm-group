<template>
    <div :class="['manage_shipping_address_block',{mobile_device:$device.isMobile}]">        
        <div class="content_block">
            <EntryFormBlock :edit_content_id="edit_content_id" v-if="entry_form_open" ref="form_block" />            
            <template v-else>
                <ContentListMobile v-if="$device.isMobile" :data="contents" :content_loader="content_loader" ref="list_block" />        
                <ContentList v-else :data="contents" :content_loader="content_loader" ref="list_block" />

                <div v-if="!$device.isMobile && pagination_show" class="row mt-3">
                    <div class="col-md-12 mb-3" align="center">
                        <span class="total_record_block">Total records: {{ pagination_config.data.total }}</span>
                    </div>
                    <div class="col-md-12">
                        <Pagination :config="pagination_config" />
                    </div>
                </div>
            </template>
        </div>
        <hr />
        <div align="right" class="pr-3">            
            <template v-if="entry_form_open">
                <span class="action_btn cancel mr-2" @click="back_to_list_view()"><i class="fa fa-times mr-1"></i> Cancel</span>
                <span class="action_btn save" @click="submit_form"><i class="fa fa-save mr-1"></i> Save</span>
            </template>
            <template v-else>
                <div class="row">
                    <div class="col-md-7 mb-3" align="left">
                        <template v-if="contents.length>0">
                            <div class="default_setup_section">
                                <span :class="[{active:sel_default_setup_index==1},{inactive:sel_default_setup_index==2}]" @click="sel_default_setup(1)"><i class="fa fa-check-circle"></i> Setup default shipping address</span>
                            </div>
                            <div class="default_setup_section">
                                <span :class="[{inactive:sel_default_setup_index==1},{active:sel_default_setup_index==2}]" @click="sel_default_setup(2)"><i class="fa fa-check-circle"></i> Setup default billing address</span>
                            </div>
                        </template>
                    </div>
                    <div class="col-md-5">
                        <template v-if="sel_default_setup_index">
                            <span class="action_btn cancel mr-2 mb-3" @click="sel_default_setup_index=''"><i class="fa fa-times mr-1"></i> Cancel</span>
                            <span class="action_btn save mb-3" @click="$refs.list_block.submit_default_setup"><i class="fa fa-save mr-1"></i> Save</span>
                            <span v-if="req_default_setup_index && $refs.list_block.sel_address_data" class="action_btn select ml-2 mb-3" @click="$refs.list_block.select_address"><i class="fa fa-check mr-1"></i> Select</span>
                        </template>
                        <span v-else class="action_btn new" @click="form_open"><i class="fa fa-plus-circle mr-1"></i> Add new shipping address</span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script>
import ContentList from './content-list-view'
import ContentListMobile from './content-list-view-mobile'
import EntryFormBlock from './content-entry-form'
export default {
    name: 'ManageShippingAddressBlock',
    props: ['req_default_setup_index'],    
    components: {
        ContentList,
        ContentListMobile,
        EntryFormBlock
    },
    data(){
        return {
            cur_path: this.$route.path,
            entry_form_open: false,
            content_loader: false,            
            pagination_show: false,
            contents: [],
            sel_default_setup_index: '',
            // sel_address_info: '',
            edit_content_id: this.$route.query.id?parseInt(this.$route.query.id):null,
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 10,
            user_id: this.$store.state.auth_info.user_data.id,
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    watch: {
        $route(to, from){
            // this.load_data(to.query.page?to.query.page:1);
        },
        req_default_setup_index: function(val){
            if(val) this.sel_default_setup(val)
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.user_access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.user_access_token
                }
            };
            return obj;
        }
    },
    mounted(){
        if(this.edit_content_id) this.entry_form_open = true
        else this.load_data()
    },
    methods: {
        form_open: function(){
            this.entry_form_open=true;
            this.$router.push(this.cur_path + '?action=new')
        },
        submit_form: function(){
            this.$refs.form_block.submit_form()
        },
        back_to_list_view: function(){
            let query = Object.assign({}, this.$route.query)
            delete query['action']
            if(this.edit_content_id>0) delete query['id']
            this.$router.replace({ query })
            this.entry_form_open = false
            this.edit_content_id = null

            if(this.contents.length==0) this.load_data()
        },
        sel_default_setup: function(val){
            this.sel_default_setup_index = val
            this.$refs.list_block.sel_default_id = ''
            // this.$refs.list_block.formData.default_shipping_address = 0
            // this.$refs.list_block.formData.default_billing_address = 0

            if(this.contents.length>0){
                this.contents.forEach(v => {
                    if((this.sel_default_setup_index==1 && v.default_shipping_address) || (this.sel_default_setup_index==2 && v.default_billing_address)){
                        this.$refs.list_block.sel_default_id = v.id
                        this.$refs.list_block.formData.default_shipping_address = v.default_shipping_address
                        this.$refs.list_block.formData.default_billing_address = v.default_billing_address
                    }
                })
            }
        },        
        async load_data(pg=this.cur_page){
            let url = '/api/manage-address'+ (this.limit>0?'?limit=' + this.limit:'');

            this.content_loader = true
            this.pagination_show = false
            this.contents = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                if(response.data.data.length>0){
                    this.contents = response.data.data
                    this.pagination_config.data = response.data.meta                    
                    this.pagination_show = true

                    if(this.req_default_setup_index) this.sel_default_setup(this.req_default_setup_index)
                }
                this.content_loader = false                
                this.cur_page = pg
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false
            });
        }
    }
}
</script>
<style scoped>
    .manage_shipping_address_block{
        padding: 15px 15px 120px 15px
    }
    .content_block{
        display: block;
        padding: 15px;
    }
    .mobile_device .content_block{
        padding: 0
    }
    .default_setup_section{
        font-size: 12px;
    }
    .default_setup_section > span{
        display: inline-block;
        cursor: pointer;
        margin-bottom: 2px;
        border-bottom: 1px dashed #ccc;
    }
    .default_setup_section > span.active{
        color:#cd5200
    }
    .default_setup_section > span.inactive{
        color:#ccc
    }
    .action_btn{
        display: inline-block;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: center;
        outline: 0;
        color: #fff;
        border: none;
        padding: 5px 15px;
        border-radius: 25px;
        cursor: pointer;
        transition: all .4s;
    }
    .action_btn.new{
        background-color: #0097f7;
    }
    .action_btn.save{
        background-color: #336633;
    }
    .action_btn.cancel{
        background-color: #cd5200;
    }
    .action_btn.select{
        background-color: #0097f7;
    }
</style>