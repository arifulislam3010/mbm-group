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
                            <div class="sb_info_row shipping_addr_info pt-3 pb-3">
                                <div class="icon"><i class="fa fa-shipping-fast"></i></div>
                                <div class="info" v-html="shipping_billing_info.shipping_address"></div>
                                <div class="action"><i class="fa fa-edit"></i></div>
                            </div>
                            <div class="sb_info_row billing_addr_info pb-3">
                                <div class="icon"><i class="fa fa-book"></i></div>
                                <div v-if="addr_same" class="info">Bill to the same address</div>
                                <div v-else class="info" v-html="shipping_billing_info.billing_address"></div>
                                <div class="action"><i class="fa fa-edit"></i></div>
                            </div>
                            <div :class="['sb_info_row','contact_info',{edit_mode:contact_no_edit},'pb-3']">
                                <div class="icon"><i class="fa fa-phone-alt"></i></div>
                                <div class="info">
                                    <template v-if="contact_no_edit">
                                        <input type="text" v-model="new_contact_no" placeholder="Enter contact number" ref="new_contact_no" />
                                    </template>
                                    <template v-else>{{ shipping_billing_info.contact_no }}</template>
                                </div>
                                <div class="action">
                                    <template v-if="contact_no_edit">
                                        <i class="fa fa-times mr-1 red_color" @click="contact_no_edit=false"></i>
                                        <i class="fa fa-save green_color" @click="change_contact_no"></i>
                                    </template>
                                    <template v-else>
                                        <i class="fa fa-edit" @click="contact_no_edit=true"></i>
                                    </template>
                                </div>
                            </div>
                            <div :class="['sb_info_row','email_info',{edit_mode:email_edit},'pb-3']">
                                <div class="icon"><i class="fa fa-envelope"></i></div>
                                <div class="info">
                                    <template v-if="email_edit">
                                        <input type="text" v-model="new_email" placeholder="Enter E-mail" ref="new_email" />
                                    </template>
                                    <template v-else>{{ shipping_billing_info.email }}</template>    
                                </div>
                                <div class="action">
                                    <template v-if="email_edit">
                                        <i class="fa fa-times mr-1 red_color" @click="email_edit=false"></i>
                                        <i class="fa fa-save green_color" @click="change_email"></i>
                                    </template>
                                    <template v-else>
                                        <i class="fa fa-edit" @click="email_edit=true"></i>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="label mt-4">
                        <!-- <i class="fa fa-info-circle"></i> -->
                        <span>Order Summary</span>
                    </div>
                    <div class="content-wrapper-block pt-3 pb-3">
                        <div class="fee_info">
                            <span>Subtotal ({{ $store.state.cart_info.total_cart_items }} items)</span>
                            <span class="amount"><PriceViewBlock :data="$store.state.cart_info.total_cart_amount.toFixed(2)" /></span>
                        </div>
                        <template v-if="$store.state.cart_info.total_cart_items>0">
                            <div class="fee_info mt-2">
                                <span>VAT ({{ vat_amount }}%)</span>
                                <span class="amount"><PriceViewBlock :data="(($store.state.cart_info.total_cart_amount*vat_amount)/100).toFixed(2)" /></span>
                            </div>
                            <!-- <div class="discount_info mt-2">
                                <span>Discount</span>
                                <span class="amount"><PriceViewBlock :data="'-' + delivery_fee.toFixed(2)" /></span>
                            </div> -->
                            <div class="fee_info mt-2">
                                <span>Delivery fee</span>
                                <span class="amount"><PriceViewBlock :data="delivery_fee.toFixed(2)" /></span>
                            </div>
                            <hr style="border: 1px dashed #ddd" />
                            <div class="fee_info total_info">
                                <span>Total</span>
                                <span class="amount">
                                    <PriceViewBlock :data="($store.state.cart_info.total_cart_amount + vat_amount + discount + delivery_fee).toFixed(2)" :animate="true" />
                                </span>
                            </div>
                            <div align="right" class="pr-3"><small>*Total price can be change</small></div>
                        </template>
                        <template v-else>
                            <hr style="border: 1px dashed #ddd" />
                            <div class="fee_info total_info">
                                <span>Total</span>
                                <span class="amount">
                                    <PriceViewBlock :data="'0.00'" />
                                </span>
                            </div>
                        </template>
                    </div>
                    <template v-if="$store.state.cart_info.total_cart_items>0">
                        <div class="content-wrapper-block mt-2 pt-3 pb-3">
                            <div class="extra_instruction_block">
                                <textarea-autosize
                                    :placeholder="'Enter your extra instruction'"                    
                                    v-model="extra_instruction"
                                    :min-height="30"
                                    :max-height="450"
                                />
                                <div><small>Example: Deliver with extra protection</small></div>
                            </div>
                        </div>
                        <div class="content-wrapper-block mt-2 pt-3 pb-3">                        
                            <div class="choose_payment_type_block">
                                <div @click="choose_payment_type=1" :class="{active:choose_payment_type==1}">
                                    <i v-if="choose_payment_type=='1'" class="far fa-check-circle active"></i>
                                    <i v-else class="far fa-circle"></i>
                                    <i class="fa fa-hand-holding-usd ml-2"></i>
                                    <span class="ml-2">Cash Payment</span>
                                </div>
                                <div @click="choose_payment_type=2" :class="{active:choose_payment_type==2}">
                                    <i v-if="choose_payment_type=='2'" class="far fa-check-circle active"></i>
                                    <i v-else class="far fa-circle"></i>
                                    <i class="fa fa-credit-card ml-2"></i>
                                    <span class="ml-2">Digital Payment</span>
                                </div>
                            </div>
                            
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
import CartItemListView from '~/components/content_display/CartItemListView'
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
import { mapMutations } from 'vuex'
export default {
    name: 'CheckoutPageBlock',
    components: {
        CartItemListView,
        PriceViewBlock
    },
    data() {
        return {
            contact_no_edit: false,
            email_edit: false,
            new_contact_no: '',
            new_email: '',
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
            delivery_fee: 20,
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
            get_cart_items: 'cart_info/GET_CART_ITEMS'
        }),
        change_contact_no: function(){
            let getContactNo = this.new_contact_no;
            if(getContactNo.trim()==''){
                this.$toast.error('Please enter contact number', {icon: "Warning"});
                this.$refs.new_contact_no.focus();
                return false;
            }else if(getContactNo.trim()!=='' && !this.$validateContactNumber(getContactNo.trim())){
                this.$toast.error('Please valid contact number', {icon: "Warning"});
                return false;
            }

            this.contact_no_edit = false;
            this.shipping_billing_info.contact_no = this.new_contact_no
        },
        change_email: function(){
            let getEmail = this.new_email;
            if(getEmail.trim()==''){
                this.$toast.error('Please enter email', {icon: "Warning"});
                this.$refs.new_email.focus();
                return false;
            }else if(getEmail.trim()!=='' && !this.$validateEmail(getEmail.trim())){
                this.$toast.error('Please valid email', {icon: "Warning"});
                return false;
            }

            this.email_edit = false;
            this.shipping_billing_info.email = this.new_email
        },
        get_full_address(v){
            // console.log(v)
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
                            this.shipping_billing_info.shipping_address = this.get_full_address(v);
                            this.shipping_billing_info.contact_no = v.contact_no
                        }
                        if(v.default_billing_address){
                            this.shipping_billing_info.billing_address = this.get_full_address(v);

                            if(this.shipping_billing_info.contact_no=='') this.shipping_billing_info.contact_no = v.contact_no
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

            let getCarListObj = this.$store.state.cart_info.cart_items
            let getCartItemInfos = []
            for(var pid in getCarListObj){
                let obj = {
                    product_id: pid,
                    product_title: getCarListObj[pid].product_title,
                    product_price_type_id: getCarListObj[pid].price_type_id,
                    product_price_type: getCarListObj[pid].price_type,
                    price: getCarListObj[pid].price,
                    qty: getCarListObj[pid].quantity                    
                }

                getCartItemInfos.push(obj)
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
                    choose_payment_type: this.choose_payment_type
                }
            }

            if(confirm('Are you sure to order it?')){
                this.order_submit_loader = true

                this.$axios.post(url, formData, this.header_config).then( async response => {
                    this.order_submit_loader = false
                    if(response.status){
                        await this.$swal("Thank you!", "Your order has been submitted.", "success")

                        localStorage.removeItem('cart_items')
                        this.get_cart_items()

                        this.$router.push('/user/my-orders')
                    }else{
                        this.$toast.error('Failed!!!', {icon: response.msg})
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
<style lang="scss" scoped>
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
    .sb_info_row{
        display: flex;
        padding: 0 15px;
        &.edit_mode > div{
            align-self: center;
        } 
        & > div.icon{
            position: relative;
            top: 2px; width: 20px;
            font-size: 12px;
            color: #5e97b3;
            margin-right: 8px;
        }
        & > div.info{
            width: 100%;        
            :deep(input){
                width: 100%;
                padding: 3px 10px;
                font-size: 14px;
                border: 1px solid #ddd;
                border-radius: 3px;
            }
            :deep(.contact_person){
                font-size: 14px;
                font-weight: bold;
                color: #333;
            }
            :deep(.address_dtl){
                display: flex;
                margin-top: 5px;
                font-size: 12px;
                .address_lbl{
                    display: inline-block;
                    white-space: nowrap;
                    padding: 1px 10px;
                    height: 17px;
                    margin-right: 10px;
                    background-color: #666;
                    color: #fff;
                    border-radius: 25px;
                    font-size: 10px;
                    & > em{
                        margin-right: 2px;
                    }
                    &.home{
                        background-color: #006699;
                    }
                    &.office{
                        background-color: #CD0000;
                    }
                }
            }
        }
        & > div.action{
            margin-left: auto;
            padding-left: 10px;
            font-size: 12px;
            cursor: pointer;
            color: #666;
            white-space: nowrap;
        }
    }
    .red_color{
        color: #a72222
    }
    .green_color{
        color: #007000
    }
    .fee_info{
        display: flex;
        padding: 0 15px;        
        & > span{
            align-self: center;
            font-size: 12px;
            color: #666;
            &.amount{
                margin-left: auto;
                font-size: 14px;
                color: #333;
            }
        }
    }
    .total_info > span.amount{
        font-size: 22px;
        color: #cd5200;
    }
    .extra_instruction_block,
    .choose_payment_type_block{
        display: block;
        margin: 0 15px;
        & > textarea{
            padding: 10px; border: 1px solid #ddd; border-radius: 3px;
            align-self: baseline;
            width: 100%;
        }
        & > div{
            display: flex;
            height: 30px;
            cursor: pointer;
            &.active{
                color: #cd5200
            }
            & > i,
            & > span{
                align-self: center;
            }
            & > i:first-child{
                font-size: 18px; color: #ccc;
                cursor: pointer;
                transition: all 0.4s;
                &.active,
                &:hover{
                    color: #cd5200;
                }
            }
        }
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
        &:hover{
            background-color: #cd5200;
        }
    }
    .pre_loader,
    .pre_loader:hover{
        background-color: #666;
    }
</style>