<template>
    <div class="my_car_info_block">
        <span class="cart" @click="open_cart_list=true">
            <i class="fa fa-cart-plus"></i>
            <sup>{{ $store.state.cart_info.total_cart_items }}</sup>
        </span>
        <div v-if="$route.name!=='cart'"><small>Cart</small></div>
                
        <component v-if="open_cart_list" :is="CartListItemBlock" />
    </div>
</template>
<script>
export default {
    name: 'MyCartInfoBlock',    
    data(){
        return {
            open_cart_list: false
        }
    },
    computed: {
        CartListItemBlock: function() {
            return () => import(`@/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/header/CartListBlock`);
        }
    },
    methods: {        
        close_cart_list_block: function(){
            this.open_cart_list = false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style lang="scss" scoped>
    .my_car_info_block{
        display: block;
    }
    .my_car_info_block > div{
        font-size: 10px;
        line-height: 12px;
    }
    .cart{
        position: relative;
        display: inline-block;
    }
    .cart > sup{
        position: absolute;
        top: -13px;
        right: -13px;
        width: 22px;
        height: 22px;
        background-color: $white;
        color: $sys_brand_color;
        border-radius: 50%;
        margin: 0;
        padding: 0;
        line-height: 20px;
        border: 1px solid $sys_brand_color;
        font-size: 10px;
        font-weight: bold;
    }
    .my_car_info_block .content{
        width: 300px; z-index: 5;
    }
</style>