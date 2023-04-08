<template>
    <div class="checkout_page_block">
        <div class="container">
            <div class="row">
                <div class="col-md-7 mt-2">
                    <div class="label">
                        <!-- <i class="fa fa-info-circle"></i> -->
                        <span>Order Items</span>
                    </div>
                    <div class="content-wrapper-block">
                        <CartItemListView />
                    </div>
                </div>
                <div class="col-md-5 mt-2">
                    <div class="label">
                        <!-- <i class="fa fa-info-circle"></i> -->
                        <span>Shipping &amp; Billing</span>
                    </div>
                    <div class="content-wrapper-block">
                        <template v-if="address_loader">
                            <div class="p-3">
                                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                            </div>
                        </template>
                        <template v-else>
                            <ShippingBillingInfoSetup :data="shipping_billing_info" />
                        </template>
                    </div>

                    <div class="label mt-4">
                        <!-- <i class="fa fa-info-circle"></i> -->
                        <span>Order Summary</span>
                    </div>
                    <div class="content-wrapper-block pt-3 pb-3">
                        <OrderSummaryInfo />
                    </div>
                    <template v-if="$store.state.cart_info.total_cart_items>0">
                        <div class="content-wrapper-block mt-2 pt-3 pb-3">
                            <ExtraInstructionInfo />
                        </div>
                        <div class="content-wrapper-block mt-2 pt-3 pb-3">                        
                            <ChoosePaymentType />
                            
                            <hr style="border: 1px dashed #ddd" />
                            
                            <div v-if="order_submit_loader" class="confirm_btn pre_loader mt-3">
                                <i class="fa fa-cog fa-spin"></i>
                                <span class="ml-1">Order submitting...wait</span>
                            </div>
                            <div v-else class="confirm_btn mt-3" @click="order_submit">
                                <i class="far fa-check-circle"></i>
                                <span class="ml-1">Order Confirmed</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ShippingBillingInfoSetup from './components/ShippingBillingInfoSetup'
import OrderSummaryInfo from './components/OrderSummaryInfo'
import ChoosePaymentType from './components/ChoosePaymentType'
import ExtraInstructionInfo from './components/ExtraInstructionInfo'
import CartItemListView from '~/components/content_display/CartItemListView'
import { mapState, mapMutations } from 'vuex'
export default {
    name: 'CheckoutPageBlock',
    components: {
        CartItemListView,        
        ShippingBillingInfoSetup,
        OrderSummaryInfo,
        ChoosePaymentType,
        ExtraInstructionInfo
    },
    data() {
        return {            
            address_loader: false,
            order_submit_loader: false,
            addr_same: false,
            shipping_billing_info: {
                shipping_address: '',
                billing_address: '',                
                contact_no: '',
                email: this.$store.state.auth_info?this.$store.state.auth_info.user_data.email:''
            },
            vat_amount: 0,
            discount: 0,
            delivery_fee_loader: false,
            delivery_fee: 0,
            extra_instruction: '',
            choose_payment_type: 1,
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
        if(this.$store.state.auth_info.loggedIn && this.$store.state.auth_info.user_data.user_type==this.$store.state.customer_user_type_id) this.load_default_address();
        else {
            this.$notify.info({
                title: 'Information',
                message: 'Unauthorized. Please login.',
                timeout: 2500
            })

            this.$router.push('/');
        }
    },
    methods: {        
        ...mapMutations({
            timeline_sse_status: 'ORDER_TIMELINE_SSE_REQ_STATUS',
            get_cart_items: 'cart_info/GET_CART_ITEMS'
        }),
        get_full_address(v, delivery_fee=false){
            // console.log(v)
            if(delivery_fee) {
                let getAreaID = v.area_id;
                console.log('Get Shipping Area ID', getAreaID)

                let url = `/api/areas/edit/${getAreaID}`;

                this.delivery_fee_loader = true                
                this.$axios.get(url, this.header_config).then( (response) => {
                    let getData = response.data
                    // console.log('Get Data', getData)
                    if(getData.delivery_charge) this.delivery_fee = parseFloat(getData.delivery_charge)
                    this.delivery_fee_loader = false
                }).catch(e => {
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.delivery_fee_loader = false
                });
            }
            return '<div class="contact_person">'+ v.full_name +'</div><div class="address_dtl"><span class="address_lbl ' + (v.label_id==1?'home':'office') + '"><em>'+ this.$store.state.shipping_addr_labels[v.label_id].icon +'</em> '+ this.$store.state.shipping_addr_labels[v.label_id].title +'</span><span>'+ v.address +'</span></div>';
        },
        load_default_address: function(){            
            let url = '/api/manage-address/default';

            this.address_loader = true            
            this.default_address = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                let getData = response.data.data
                // console.log(getData)

                if(getData.length>0){
                    if(getData.length==1) this.addr_same = true
                    getData.forEach(v => {
                        if(v.default_shipping_address){
                            this.shipping_billing_info.shipping_address = this.get_full_address(v, true);
                            this.shipping_billing_info.contact_no = v.contact_no

                            if(this.addr_same) this.shipping_billing_info.billing_address = this.get_full_address(v);
                        }
                        if(v.default_billing_address){
                            this.shipping_billing_info.billing_address = this.get_full_address(v);

                            if(this.shipping_billing_info.contact_no=='') this.shipping_billing_info.contact_no = v.contact_no

                            if(this.addr_same && this.shipping_billing_info.shipping_address=='') this.shipping_billing_info.shipping_address = this.get_full_address(v, true);
                        }
                    })
                }

                this.address_loader = false
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.address_loader = false
            });
        },
        order_submit: function(){
            let url = '/api/my-orders/store';            

            if(this.shipping_billing_info.shipping_address==''){
                this.$toast.error('Please choose shipping address', {icon: "Warning"});
                return false
            }else if(this.shipping_billing_info.billing_address==''){
                this.$toast.error('Please choose billing address', {icon: "Warning"});
                return false
            }else if(this.shipping_billing_info.contact_no==''){
                this.$toast.error('Please enter contact number', {icon: "Warning"});
                return false
            }

            let getCartListObj = this.$store.state.cart_info.cart_items
            let getCartItemInfos = []
            for(var pid in getCartListObj){
                for(var tid in getCartListObj[pid]){
                    for(var ptid in getCartListObj[pid][tid]){
                        let obj = {
                            product_id: pid,
                            product_title: getCartListObj[pid][tid][ptid].product_title,
                            product_price_type_id: getCartListObj[pid][tid][ptid].price_type_id,
                            product_price_type: getCartListObj[pid][tid][ptid].price_type,
                            product_type_infos: JSON.stringify(getCartListObj[pid][tid][ptid].product_type_info_values),
                            custom_product_design_id: getCartListObj[pid][tid][ptid].custom_product_design && getCartListObj[pid][tid][ptid].custom_product_design.design_id?getCartListObj[pid][tid][ptid].custom_product_design.design_id:'',
                            custom_product_design_price: getCartListObj[pid][tid][ptid].custom_product_design && getCartListObj[pid][tid][ptid].custom_product_design.price?getCartListObj[pid][tid][ptid].custom_product_design.price:'',
                            upload_product_image: getCartListObj[pid][tid][ptid].custom_product_design && getCartListObj[pid][tid][ptid].custom_product_design.upload?getCartListObj[pid][tid][ptid].custom_product_design.upload:'',
                            price: getCartListObj[pid][tid][ptid].price,
                            qty: getCartListObj[pid][tid][ptid].quantity
                        }

                        getCartItemInfos.push(obj)
                    }
                }
            }

            let formData = {
                cart_item_infos: getCartItemInfos,
                shipping_billing_info: this.shipping_billing_info,
                order_infos: {                    
                    total_amount: this.$store.state.cart_info.total_cart_amount,
                    vat_amount: this.vat_amount,
                    discount: this.discount,
                    delivery_fee: this.delivery_fee,
                    extra_instruction: this.extra_instruction,
                    choose_payment_type: this.choose_payment_type,
                    order_from: 'web'
                }
            }                    

            if(confirm('Are you sure to order it?')){
                this.order_submit_loader = true

                this.$axios.post(url, formData, this.header_config).then( async response => {
                    this.order_submit_loader = false
                    if(response.data.status){
                        this.timeline_sse_status = true
                        
                        await this.$swal("Thank you!", "Your order has been submitted.", "success")

                        localStorage.removeItem('cart_items')
                        this.get_cart_items()

                        this.$router.push('/user/my-orders')
                    }else{
                        this.$toast.error(response.data.msg, {icon: "error_outline"})
                        this.order_submit_loader = false
                    }
                }).catch(e => {
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.order_submit_loader = false
                });
            }
        }
    }
}
</script>
<style scoped>
    .checkout_page_block{
        display: block;
    }
    .label{
        display: block;
        padding: 0 15px;
        height: 50px;
        line-height: 50px;
        font-size: 16px;
        background-color: #fafafa;
        border-bottom: 1px solid #eee;
    }
    .content-wrapper-block{
        display: block;
        background-color: #fff;        
        box-shadow: 0 0 25px #ddd;
    }
    .confirm_btn{
        display: block;
        margin: 0 15px;
        padding: 8px 15px;
        font-size: 14px;
        font-weight: bold;
        background-color: #ff6600;
        color: #fff;
        cursor: pointer;
        text-align: center;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .confirm_btn:hover{
        background-color: #cd5200;
    }
    .pre_loader,
    .pre_loader:hover{
        background-color: #666;
    }
</style>