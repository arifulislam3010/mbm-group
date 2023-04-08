<template>
    <div class="inner_page_block">
        <template v-if="$parent.current_slug==$store.state.static_page_path">            
            <StaticPageBlock />            
        </template>
        <template v-else-if="$parent.check_user_dashboard && $store.state.auth_info.user_data.user_type==$store.state.delivery_user_type_id">            
            <DeliveryPersonDashboardBlock />
        </template>
        <template v-else-if="$parent.check_user_dashboard && $store.state.auth_info.user_data.user_type==$store.state.customer_user_type_id">            
            <UserDashboardBlock />
        </template>
    </div>
</template>
<script>
import UserDashboardBlock from '~/pages/user-dashboard/index'
import DeliveryPersonDashboardBlock from '~/pages/delivery-person-dashboard/index'
import StaticPageBlock from '~/pages/static-page/index'
export default {
    name: 'InnerPageBlock',
    components: {
        UserDashboardBlock,
        DeliveryPersonDashboardBlock,
        StaticPageBlock
    }
}
</script>
<style scoped>

</style>