<template>
    <div class="my_dashboard_block">
        <DeliveryManDashboard v-if="$store.state.auth_info.user_data.user_type==$store.state.delivery_user_type_id" />
        <CustomerDashboard v-else-if="$store.state.auth_info.user_data.user_type==$store.state.customer_user_type_id" />
    </div>
</template>
<script>
import CustomerDashboard from './components/customer-dashboard.vue'
import DeliveryManDashboard from './components/delivery-man-dashboard.vue'
export default {
    name: 'MyDashboard',
    components: {
        CustomerDashboard,
        DeliveryManDashboard
    }
}
</script>
<style lang="scss" scoped>    
</style>