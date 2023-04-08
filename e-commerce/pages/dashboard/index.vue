<template>
    <div class="dashboard-container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <ProductsInfoByCategory />
            </div>
            <!-- <div class="col-md-6 mb-4">
                <ProductsInfoByCompany />
            </div> -->
            <div class="col-md-6 mb-4">
                <ProductsInfoByProductType />
            </div>
            <div class="col-md-6 mb-4">
                <ProductsInfoByOperator v-if="products_info_by_operator_view" />
            </div>
            <div class="col-md-6 mb-4">
                <ProductsRptByOperator />
            </div>
        </div>
    </div>
</template>
<script>
import ProductsInfoByCategory from './dashboard-components/ProductsInfoByCategory'
import ProductsInfoByCompany from './dashboard-components/ProductsInfoByCompany'
import ProductsInfoByProductType from './dashboard-components/ProductInfoByProductType'
import ProductsInfoByOperator from './dashboard-components/ProductsInfoByOperator'
import ProductsRptByOperator from './dashboard-components/ProductsRptByOperator'
export default {
    name: 'DashboardContainer',
    components: {
        ProductsInfoByCategory,
        ProductsInfoByCompany,
        ProductsInfoByProductType,
        ProductsInfoByOperator,
        ProductsRptByOperator
    },
    data(){
        return {
            products_info_by_operator_view: false,
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
        let user_role_access = this.$store.state.auth_info.user_data.role_info.role_accesses;
        // console.log('Dashboard role access', user_role_access)
        user_role_access.forEach(v => {
            if(this.$store.state.admin_user_role_feature_id==v.feature_id){
                this.products_info_by_operator_view = true;
                return ;
            }
        });
    }
}
</script>
<style scoped>
</style>