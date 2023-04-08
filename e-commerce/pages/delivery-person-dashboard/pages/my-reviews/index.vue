<template>
    <div :class="['my_reviews_block',{mobile_device:$device.isMobile}]">
        <div class="content_block">
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
        </div>

        <OrderDetailsDrawerView v-if="open_order_details" :data="sel_order_data" />
        <OrderTimelinessDrawerView v-if="open_order_timelines" :data="sel_timelines_data" />
    </div>
</template>
<script>
import ContentList from './content-list-view'
import ContentListMobile from './content-list-view-mobile'
import OrderDetailsDrawerView from '../manage-orders/details-drawer-view'
import OrderTimelinessDrawerView from '~/components/content_display/OrderTimelinesDrawerView'
export default {
    name: 'MyReviewsBlock',
    components: {
        ContentList,
        ContentListMobile,
        OrderDetailsDrawerView,
        OrderTimelinessDrawerView
    },
    data(){
        return{
            cur_path: this.$route.path,
            content_loader: false,
            pagination_show: false,
            open_order_details: false,
            open_order_timelines: false,            
            sel_order_data: [],
            sel_timelines_data: [],
            contents: [],
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
        this.load_data()
    },
    methods: {
        view_details_order: function(data){
            this.open_order_details = true
            this.sel_order_data = data
        },
        view_order_timelines: function(data){
            this.open_order_timelines = true
            this.sel_timelines_data = data
        },
        close_order_dtl_block: function(){
            this.open_order_details = false
            document.body.classList.remove('popup_open')
        },
        close_order_timelines_block: function(){
            this.open_order_timelines = false
            document.body.classList.remove('popup_open')
        },
        async load_data(pg=this.cur_page){
            let url = '/api/my-reviews'+ (this.limit>0?'?limit=' + this.limit:'');

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
    .my_reviews_block{
        padding: 15px
    }
    .content_block{
        display: block;
        padding: 15px;
    }
    .mobile_device .content_block{
        padding: 0
    }
</style>