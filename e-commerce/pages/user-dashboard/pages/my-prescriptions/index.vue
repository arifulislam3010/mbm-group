<template>
    <div :class="['my_prescriptions_block',{mobile_device:$device.isMobile}]">
        <template v-if="prescription_popup_allow">
            <div class="black_overlay">
                <div class="popup_content">
                    <img :src="sel_prescription_img" />
                </div>
                <span class="close_popup_btn" @click="close_popup_content">x</span>
            </div>
        </template>
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
                <div align="right">
                    <span class="action_btn new" @click="form_open"><i class="fa fa-plus-circle mr-1"></i> Add new prescription</span>                    
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
    name: 'MyPrescriptionsBlock',
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
            prescription_popup_allow: false,
            sel_prescription_img: '',
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
        close_popup_content: function(){
            document.body.classList.remove('popup_open')
            this.prescription_popup_allow = false;
            this.sel_prescription_img = ''
        },
        prescription_view: function(data){
            document.body.classList.add('popup_open')
            this.prescription_popup_allow = true;
            this.sel_prescription_img = data.file_content
        },
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
        async load_data(pg=this.cur_page){
            let url = '/api/my-prescriptions'+ (this.limit>0?'?limit=' + this.limit:'');

            this.content_loader = true
            this.pagination_show = false
            this.contents = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                if(response.data.data.length>0){
                    this.contents = response.data.data
                    this.pagination_config.data = response.data.meta                    
                    this.pagination_show = true                    
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
    .my_prescriptions_block{
        padding: 15px 15px 120px 15px
    }
    .popup_content{
        position: relative;
        color: #333;
        background-color: #fff;
        padding: 25px;
        text-align: center;
        width: 80%;
        height: auto;
        overflow-y: auto;
        left: 10%;
        top: 5%;
        z-index: 9999;
        box-shadow: 0 0 15px #333;
    }
    .popup_content > img{
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .close_popup_btn {
        position: fixed;
        display: inline-block;
        text-align: center;
        width: 30px;
        height: 30px;
        line-height: 30px;
        right: 25px;
        top: 3%;
        color: #CD0000;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        border: 1px solid #ddd;
        background-color: #f7f7f7;
        border-radius: 50%;
        transition: all 0.4s;
        z-index: 100000;
    }
    .close_popup_btn:hover{
        background-color: #CD0000;
        color: #fff;
    }
    .content_block{
        display: block;
        padding: 15px;
    }
    .mobile_device .content_block{
        padding: 0
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