<template>
    <div class="content_list_view table-responsive">
        <div class="filter_option_block">
            <span>Filters</span>
            <ul>
                <li>
                  <div class="show_date_range_btn" @click="$parent.showDateRange()">
                    <i v-if="$parent.show_date_range" class="far fa-check-circle"></i>
                    <i v-else class="far fa-circle"></i>
                    <span>Show Date Range</span>
                  </div>
                </li>
                <li v-if="$parent.show_date_range">
                  <label>Choose Date Range</label>
                  <div>
                    <date-range-picker v-model="$parent.filter.date_range" format="YYYY/MM/DD" :options="range_options" />
                  </div>
                </li>
                <li>
                    <label>Status</label>
                    <select v-model="$parent.filter.status" @change="filter_by_type('status')">
                        <option value="">All</option>
                        <option value="4">Pending</option>
                        <option value="3">Running</option>
                        <option value="1">Completed</option>
                        <option value="2">Canceled</option>
                    </select>
                </li>
            </ul>
        </div>
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Order Info</th>                    
                    <th><i class="fa fa-money"></i> Total Payable</th>
                    <th>Current Status</th>
                    <th width="120"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>
                            <div class="order_info">
                                <div class="order_id">ID #{{ item.order_id }}</div>
                                <div class="ordered_at"><small>Placed on {{ item.created_at }}</small></div>
                                <div class="total_items"><small>Total Items {{ item.order_items_info.length }}</small></div>
                            </div>
                        </td>
                        <!-- <td>
                            <div class="shipping_info">
                                <div class="label">Shipping Address</div>
                                <div v-html="item.shipping_address"></div>
                                <div class="label mt-3">Billing Address</div>
                                <div v-html="item.billing_address"></div>
                            </div>
                        </td> -->
                        <td>
                            <div>
                                <i v-if="item.choose_payment_type==1" class="fa fa-hand-holding-usd mr-2 cash_payment" title="Cash on delivery"></i>
                                <i v-if="item.choose_payment_type==2" class="fa fa-credit-card mr-2 digital_payment" title="Digital payment"></i>
                                <PriceViewBlock :data="item.total_payable.toFixed(2)" />
                            </div>
                        </td>                        
                        <td align="center">
                            <template v-if="item.status==2">
                                <div class="status cancel_status">
                                    <i class="far fa-check-circle"></i>
                                    <span>The order is canceled</span>
                                </div>
                            </template>
                            <template v-else>
                                <div class="status">
                                    <i class="far fa-check-circle"></i>
                                    <template v-if="item.delivery_timeline_info.length>0">
                                        <span>{{ $store.state.order_timeline_list[item.delivery_timeline_info[0].timeline_id] }}</span>
                                    </template>
                                    <template v-else-if="item.status=='0'">
                                        <span>{{ $store.state.order_init_status_label }}</span>
                                    </template>                                
                                </div>
                                <div v-if="!item.delivery_person_info" class="assign_delivery_person_btn">
                                    <span @click="open_delivery_person_list=true; sel_order_id=item.id"><i class="fa fa-biking"></i> Assign Delivery Person</span>
                                </div>
                            </template>
                        </td>
                        <td align="center">
                            <div class="action_block">
                                <div @click="view_details(index,item)"><i class="fa fa-eye"></i> View Details</div>
                                <div @click="view_invoice(index,item)" class="invoice_btn mt-2"><i class="fa fa-receipt"></i> Invoice</div>
                                <template v-if="($parent.$parent.$parent.role_access.edit_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.edit))">
                                    <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                </template>
                                <!-- <template v-if="($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
                                    <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>
                                </template> -->
                            </div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr><td colspan="5" align="center"><i class="fa fa-info-circle"></i> There is no data</td></tr>
                </template>
            </tbody>
        </table>

        <OrderDetailsDrawerView v-if="open_order_details" />
        <OrderInvoiceDrawerView v-if="open_order_invoice" />
        <DeliveryPersonsListView v-if="open_delivery_person_list" :order_id="sel_order_id" />
    </div>
</template>
<script>
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import OrderDetailsDrawerView from './details-drawer-view'
import OrderInvoiceDrawerView from './invoice-drawer-view'
import DeliveryPersonsListView from './delivery-persons-list'
import { mapActions, mapMutations } from 'vuex'
import moment from 'moment'
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    components: {
        PriceViewBlock,
        OrderDetailsDrawerView,
        OrderInvoiceDrawerView,
        DeliveryPersonsListView
    },
    data(){
        return {
            open_order_details: false,
            open_order_invoice: false,
            open_delivery_person_list: false,
            sel_order_data: '',
            sel_order_id: '',
            sel_order_index: '',
            range_options: {
                // singleDatePicker: true,
                // showDropdowns: true,
                minYear: 2021,
                maxYear: +moment().format("YYYY"),
                ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }
        }
    },
    methods: {
        ...mapMutations({
            selOrderData: 'order_info/SEL_DATA'
        }),
        filter_by_type: function(type){
            if(this.$route.query.page) delete this.$route.query['page']

            if(this.$parent.filter[type]==''){
                let query = Object.assign({}, this.$route.query);
                delete query[type];
                this.$router.replace({ query });
            }else{
                let obj = {}; obj[type] = this.$parent.filter[type];
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            }
        },
        view_details: function(index,data){
            this.open_order_details = true
            this.sel_order_data = data
            this.sel_order_index = index
            this.selOrderData(data)
        },
        view_invoice: function(index,data){
            this.open_order_invoice = true
            this.sel_order_data = data
            this.sel_order_index = index
            this.selOrderData(data)
        },
        edit_item: function(val){            
            this.$parent.edit_content_id = val
            this.$parent.add_new_entity(true)
            this.$parent.$parent.$parent.edit_route(val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/order-infos/delete/' + id, this.$parent.header_config).then( (response) => {
                    console.log('Get Data', response.data)
                    this.$swal("Good job!", "Data has been deleted successfully.", "success");
                    this.$parent.load_data();
                }).catch(e => {
                    console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})                
                });
            }
        },
        close_order_dtl_block: function(){
            this.open_order_details = false
            document.body.classList.remove('popup_open')
        },
        close_order_invoice_block: function(){
            this.open_order_invoice = false
            document.body.classList.remove('popup_open')
        },
        close_delivery_list_block: function(){
            this.open_delivery_person_list = false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style scoped>
    .filter_option_block{
        display: flex;
        height: 60px;
        border: 1px solid #ddd;
        background-color: #eee;
        margin-bottom: 15px;
        padding: 0 15px;
        border-radius: 3px;
    }
    .filter_option_block > span,
    .filter_option_block > ul{
        align-self: center;
    }
    .filter_option_block > span{
        padding-right: 15px; color: #444; font-size: 14px; border-right: 1px solid #ccc;
    }
    .filter_option_block > ul{
        display: flex;
        list-style: none;
        margin: 0; padding: 0;
        margin-left: auto;
        height: 30px
    }
    .filter_option_block > ul > li{
        /* display: flex; */
        align-self: center;
        /* height: 100%; */
        font-size: 12px;
        cursor: pointer;
        user-select: none;
        margin-left: 10px
    }
    .filter-option_block > ul > li > .show_date_range_btn{
        cursor: pointer;
    }
    .filter_option_block > ul > li label{
        margin-bottom: 3px
    }
    .filter_option_block > ul > li select{
        display: block;
        border: 1px solid #ddd;
        padding: 1px 5px; border-radius: 3px;
        max-width: 185px; outline: none;
    }
    .label{
        font-size: 13px; font-weight: bold; color: #888;
        margin-bottom: 10px;
    }
    .content_list_view > table{
        border: 1px solid #ddd
    }
    .assign_delivery_person_btn{
        display: block; text-align: left; margin-top: 15px;
    }
    .assign_delivery_person_btn > span{
        display: inline-block;
        background-color: chocolate;
        color: #fff; padding: 3px 15px;
        border-radius: 25px; cursor: pointer;
        transition: all 0.4s;
    }
    .assign_delivery_person_btn > span:hover{
        background-color: crimson;
    }
    /* .shipping_info :deep(.contact_person){
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    .shipping_info :deep(.address_dtl){
        display: flex;
        margin-top: 5px;
        font-size: 12px;
    }
    .shipping_info :deep(.address_dtl) .address_lbl{
        display: inline-block;
        white-space: nowrap;
        padding: 1px 10px;
        height: 17px;
        margin-right: 10px;
        background-color: #666;
        color: #fff;
        border-radius: 25px;
        font-size: 10px;
    }
    .shipping_info :deep(.address_dtl) .address_lbl > em{
        margin-right: 2px;
    }
    .shipping_info :deep(.address_dtl) .address_lbl.home{
        background-color: #006699;
    }
    .shipping_info :deep(.address_dtl) .address_lbl.office{
        background-color: #CD0000;
    } */
    /* .status > i{
        font-size: 18px;
        color: #ccc
    }
    .status > i.active{
        color: #5dad5d
    } */
    .status{
        display: flex;
        height: 30px;
    }    
    .status > i,
    .status > span{
        align-self: center;
        font-weight: bold;        
    }
    .status > i{
        margin-right: 10px;
        font-size: 16px;
        color: rgb(77 210 77);
    }
    .cancel_status > i,
    .cancel_status > span{
        color: #CD0000
    }
    .action_block > div{
        display: block;
        margin: 0 5px;
        cursor: pointer;        
        white-space: nowrap;
        background-color: #666;
        color: #fff;
        font-size: 10px;
        padding: 2px 10px;
        border-radius: 25px;
        transition: all 0.4s
    }
    .action_block > div.invoice_btn{
        background-color: darkolivegreen;
    }
    .action_block > div:hover{
        background-color: #006699;
    }
    .action_block > div.invoice_btn:hover{
        background-color: #336633
    }
</style>