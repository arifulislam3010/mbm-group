<template>
    <div class="my_orders_block">
        <div v-if="$device.isMobile" class="drop_down_list">
            <select v-model="sel_tab_index" class="form-control" @change="load_data()">
                <option v-for="(item,index) in data_type_list" :key="index" :value="index">{{ item.name }}</option>
            </select>
        </div>
        <div v-else class="tab_list">
            <span v-for="(item,index) in data_type_list" :key="index" :class="['text_overflow','max_one_line_allow',{active: sel_tab_index==index}]" @click="select_order_category(index)"><i :class="item.icon"></i> {{ item.name }}</span>            
        </div>
        <div class="content_block">
            <ContentCardView :data="contents" :per_view="$device.isMobile?2:5" :content_loader="content_loader" ref="list_block" />
            <!-- <ContentListView v-else :data="contents" :content_loader="content_loader" ref="list_block" /> -->

            <div v-if="pagination_show" class="row mt-3">
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
import ContentListView from './content-list-view'
import ContentCardView from './content-card-view'
import OrderDetailsDrawerView from './details-drawer-view'
import OrderTimelinessDrawerView from '~/components/content_display/OrderTimelinesDrawerView'
export default {
    name: 'MyOrdersBlock',
    components: {
        ContentListView,
        ContentCardView,
        OrderDetailsDrawerView,
        OrderTimelinessDrawerView
    },
    data(){
        return {
            cur_path: this.$route.path,            
            content_loader: false,            
            pagination_show: false,
            open_order_details: false,
            open_order_timelines: false,
            sel_tab_index: 0,
            sel_order_data: [],
            sel_timelines_data: [],
            contents: [],
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            data_type_list: [
                {
                    name: 'Pending',
                    icon: 'fa fa-stopwatch'
                },{
                    name: 'Completed',
                    icon: 'fa fa-check-circle'
                },{
                    name: 'Cancelation',
                    icon: 'fa fa-times-circle'
                }
            ],
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 5,
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
        this.load_data();
    },
    methods: {
        select_order_category: function(val){
            if(this.sel_tab_index!==val){
                this.sel_tab_index = val

                this.load_data();
            }
        },
        view_details_order: function(data){
            this.open_order_details = true
            this.sel_order_data = data
        },
        view_order_timelines: function(data){
            this.open_order_timelines = true
            this.sel_timelines_data = data
        },
        async load_data(pg=this.cur_page){
            let url = '/api/manage-orders'+ (this.limit>0?'?type='+ this.sel_tab_index +'&limit=' + this.limit:'');

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
        close_order_dtl_block: function(){
            this.open_order_details = false
            document.body.classList.remove('popup_open')
        },
        close_order_timelines_block: function(){
            this.open_order_timelines = false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style scoped>
    .my_orders_block{
        padding: 15px
    }
    .drop_down_list{
        display: block;
        margin-bottom: 15px;
    }
    .drop_down_list > select{
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .tab_list{
        display: flex;
        border-bottom: 1px solid #ddd;
        padding: 0 15px;        
        height: 40px;
        margin-bottom: 15px;
    }
    .tab_list > span{
        align-self: center;
        margin-left: 15px;
        height: 40px;
        line-height: 40px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.4s;
    }
    .tab_list > span:first-child{
        margin-left: 0;
    }
    .tab_list > span:hover,
    .tab_list > span.active{
        border: 1px solid #ddd;
        border-bottom: none;
        background-color: #fff;
        border-radius: 5px 5px 0 0;
        margin-bottom: -1px;        
        font-size: 14px;
        font-weight: bold;
        color: #c21a75;        
        padding: 0 15px;
    }
</style>