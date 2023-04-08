<template>
    <div class="content_wrapper_block">
        <template v-if="pre_loader">
            <div class="content_black_overlay"></div>
            <div class="loader_block">
                <i class="fa fa-cog fa-spin"></i> Content Loading...Wait
            </div>
        </template>
        
        <ManageAccount v-if="cur_user_page=='manage-account'" />
        <ManageExactLocation v-else-if="cur_user_page=='manage-exact-location'" />
        <ManageOrders v-else-if="cur_user_page=='manage-orders'" />
        <MyReviews v-else-if="cur_user_page=='my-reviews'" />
        <ChangePassword v-else-if="cur_user_page=='change-password'" />        
    </div>
</template>
<script>
import ManageAccount from './pages/manage-account'
import ManageExactLocation from './pages/manage-exact-location/index'
import ManageOrders from './pages/manage-orders/index'
import MyReviews from './pages/my-reviews'
import ChangePassword from './pages/change-password'
export default {
    name: 'ContentWrapperBlock',
    components: {
        ManageAccount,
        ManageExactLocation,
        ManageOrders,
        MyReviews,
        ChangePassword
    },
    data(){
        return {
            pre_loader: false,       
            user_id: this.$store.state.auth_info.user_data.id,
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    watch: {
        cur_user_page: function(){}
    },
    computed: {
        cur_user_page: function(){
            return this.$route.params.page
        },
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
    methods: {
        get_string: function(str){
            return str.replace(this.$store.state.dashboard.prefix + '-',' ').replace(/-/g,' ');
        }
    }
}
</script>
<style scoped>
    .content_wrapper_block{
        display: block;
        position: relative;
        width: 100%;
        height: 100%;
    }
    .content_black_overlay{
        position: absolute;
        background-color: #00000080;
        left: 0; top: 0;
        width: 100%; 
        height: 100%;
        z-index: 5;
    }
    .loader_block{
        position: absolute;
        background-color: #fff;
        padding: 5px 15px;
        border-radius: 25px;
        box-shadow: 0 0 10px #000;
        border: 1px solid #ddd;
        left: 25px;
        top: 25px;
        z-index: 6;
    }
</style>