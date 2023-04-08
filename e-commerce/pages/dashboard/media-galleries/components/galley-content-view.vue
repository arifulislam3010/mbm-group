<template>
    <div class="gallery_content_view_block">
        <div v-if="content_loader" class="row">
            <div class="col-md-12">
                <div class="info_block">
                    <FormBlockLoader :cols="1" :height="35" :r1="false" :r2="true" :r1_w="0" :r2_w="100" :r1_h="0" :r2_h="15" />
                    <div class="row">
                        <div v-for="(n,i) in 4" :key="i" class="col-md-3">
                            <FormBlockLoader class="mt-2" :cols="1" :height="285" :r1="true" :r2="true" :r1_w="100" :r2_w="80" :r1_h="225" :r2_h="40" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="tab_list">
                <span :class="{active:sel_tab_index==0}" @click="sel_tab_index=0"><i class="fa fa-images"></i> All Photos</span>
                <span :class="{active:sel_tab_index==1}" @click="sel_tab_index=1"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>

            <template v-if="sel_tab_index==0">
                <GalleryContentList :data="contents" />

                <div v-if="pagination_show" class="row mt-3">
                    <div class="col-md-12 mb-3" align="center">
                        <span class="total_record_block">Total records: {{ pagination_config.data.total }}</span>
                    </div>
                    <div class="col-md-12">
                        <Pagination :config="pagination_config" />
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="m-4">
                    <EntryFormBlock :external_req="true" />
                </div>
            </template>
        </div>        
    </div>
</template>
<script>
import GalleryContentList from './gallery-content-list'
import EntryFormBlock from '../content-entry-form'
import { mapActions } from 'vuex'
export default {
    name: "GalleryContentViewBlock",
    props: ['content_type','selected_arr','view_others'],
    components: {
        GalleryContentList,
        EntryFormBlock
    },
    data(){
        return {
            sel_tab_index: 0,
            content_loader: false,
            pagination_show: false,
            contents: [],
            category_list: [],
            srch_keyword: '',
            srch_cat_id: '',
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: this.load_data
            },
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 12,
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
        },
        selected_item_ids(){
            let obj = {};
            this.selected_arr.forEach(v => {
                obj[v.id] = true
            });

            return obj;
        }
    },
    mounted(){
        // alert(this.view_others)
        this.load_categories();
        this.load_data();
    },
    methods: {
        ...mapActions({        
            mediaGallerySubmit: 'media_gallery_info/DATA_SUBMIT'
        }),
        async load_categories(){
            let url = '/api/media-categories';

            this.category_list = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                this.category_list = response.data.data
            }).catch(e => {
                this.$toast.error('Category load failed!!!', {icon: "error_outline"})
            });
        },
        async load_data(pg=this.cur_page){
            let url = '/api/media-galleries'+ (this.limit>0?'?limit=' + this.limit:'') + (pg>1?'&page=' + pg:'') + (this.view_others=='0'?'&own_result=true':'') + (this.srch_keyword.trim()!=''?'&keyword=' + this.srch_keyword:'') + (this.srch_cat_id!=''?'&cat_id=' + this.srch_cat_id:'');

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
        },        
        select_item: function(data){
            if(!this.selected_item_ids[data.id]) {
                this.$parent.add_content(data)
                this.$parent.popup_close();
                this.$toast.success('Photo has been added successfully', {icon: "Success"});
            }
        }
    }
}
</script>
<style scoped>
    .tab_list{
        display: block;
        margin-top: 15px;
        border-bottom: 1px solid #ddd;
        padding: 0 15px;
    }
    .tab_list > span{
        display: inline-block;
        padding: 2px 12px; font-size: 12px;
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 25px;
        margin: 15px 5px;
        cursor: pointer
    }
    .tab_list > span.active{
        background-color: #CD0000; color: #fff;
        border-color: #CC0000
    }
    .info_block{
        margin: 25px 20px 20px 20px;
    }
</style>