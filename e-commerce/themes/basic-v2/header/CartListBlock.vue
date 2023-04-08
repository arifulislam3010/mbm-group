<template>
    <div class="cart_list_item_block">
        <div class="black_mask_overlay" @click="$parent.close_cart_list_block"></div>
        <div class="content clearfix">
            <div class="info_header">
                <div class="info">
                    <h5>My Cart <sup>{{ $store.state.cart_info.total_cart_items }}</sup></h5>
                </div>
                <div class="img">                    
                    <i class="fa fa-shopping-cart" />
                </div>
            </div>
            <div class="cart_items">
                <CartItemListView />
            </div>
            <div v-if="$store.state.cart_info.total_cart_items>0" class="footer_content_info">
                <div class="total_amount_block">
                    <span>Total</span>
                    <span><PriceViewBlock :data="$store.state.cart_info.total_cart_amount.toFixed(2)" :animate="true" /></span>
                </div>
                <div class="checkout_btn" @click="checkout_proceed"><i class="fa fa-check-circle"></i><span class="ml-2">Proceed to checkout</span></div>
            </div>
        </div>
    </div>
</template>
<script>
import CartItemListView from '~/components/content_display/CartItemListView'
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
export default {
    name: 'CartListItemBlock',
    components: {
        CartItemListView,
        PriceViewBlock
    },
    mounted(){
        document.body.classList.add('popup_open')
    },
    methods: {
        checkout_proceed: function(){
            if(this.$store.state.auth_info.loggedIn && this.$store.state.auth_info.user_data.user_type==this.$store.state.customer_user_type_id) {
                this.$router.push('/'+ this.$store.state.checkout_page_path +'?time=' + Date.now())
            }else{
                this.$parent.$parent.auth_form_open_status(true)
            }

            this.$parent.close_cart_list_block();
        }
    }
}
</script>
<style lang="scss" scoped>
    .cart_list_item_block{
        display: block;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content{
        position: fixed;
        color: #333;
        background-color: #fff;
        width: 350px;
        height: 100%;
        overflow-y: auto;
        right: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
    }
    .info_header{
        display: flex;
        height: 60px; padding: 0 15px;
        border-bottom: 1px solid #ddd;        
    }
    .info_header .img{                
        align-self: center;
        margin-left: auto;
    }
    .info_header .img img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .info_header .img i{
        font-size: 24px;
        color: #aaa;        
    }
    .info_header .info{        
        align-self: center;
    }
    .info_header .info h4{
        margin: 0;
        font-size: 18px;
    }
    .info_header .info sup{
        font-size: 10px;
        padding: 3px 10px;
        /* border: 1px solid #ddd; */
        border-radius: 25px;
        background-color: $sys_brand_color;
        color: #fff;
    }
    .cart_items{
        display: block;
        max-height: calc(100% - 156px);
        overflow-y: auto;
    }
    .cart_items > .item{
        display: flex;
        width: 100%;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .cart_items > .item .img{
        align-self: center;
        width: 60px;
        height: 50px;
        /* background-color: #eee; */
        border: 1px solid #ddd;
    }
    .cart_items > .item .img > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .cart_items > .item .info{    
        align-self: center;
        margin-left: 10px;
        width: calc(100% - 70px);
        height: 50px;
    }
    .cart_items > .item .info > .top_section{
        display: flex;
        align-self: center;
        height: 25px;
    }
    .cart_items > .item .info > .top_section > .title{
        align-self: center;
        font-size: 12px;
        color: #666;
        font-weight: bold;
    }
    .cart_items > .item .info > .top_section > .del_btn{
        margin-left: auto;
        align-self: center;
        cursor: pointer;
        color: #666;
        font-size: 12px;
        transition: all 0.4s;
    }
    .cart_items > .item .info > .top_section > .del_btn:hover{
        color: $sys_brand_color;
    }
    .cart_items > .item .info > .bottom_section{        
        align-self: center;
        height: 25px;
    }
    .footer_content_info{
        position: absolute;
        bottom: 0;
        width: 100%;
        border-top: 1px solid #ddd;
        text-align: center;
        padding: 15px;
    }
    .total_amount_block{
        display: flex;        
        font-size: 16px;
        font-weight: bold;
        height: 36px;
    }
    .total_amount_block > span:last-child{
        margin-left: auto;
    }

    .checkout_btn{
        display: block;
        background-color: $sys_brand_color;
        color: #fff;
        cursor: pointer;
        padding: 0 15px;
        height: 30px;
        line-height: 30px;
        border-radius: 25px;
        font-size: 12px;
        transition: all 0.4s;
    }
    .checkout_btn:hover{
        background-color: #CD0000;
    }
</style>