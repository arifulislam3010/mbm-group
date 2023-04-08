<template>
    <div class="dynamic_page_content_block">
        <template v-if="$parent.check_user_dashboard">
            <UserDashboardBlock />
        </template>
        <template v-else-if="$parent.current_slug==$store.state.checkout_page_path">
            <CheckoutPageBlock />
        </template>
        <template v-else-if="$parent.current_slug==$store.state.static_page_path">
            <StaticPageBlock />
        </template>
        <ProductsByCategory v-else />        
    </div>
</template>
<script>
import UserDashboardBlock from '~/pages/user-dashboard/index'
import CheckoutPageBlock from '~/pages/checkout-page/index'
import StaticPageBlock from '~/pages/static-page/index'
import ProductsByCategory from './ProductsByCategory'
export default {
    name: 'DynamicPageContentBlock',
    components: {
        UserDashboardBlock,
        CheckoutPageBlock,
        StaticPageBlock,
        ProductsByCategory
    }
}
</script>
<style scoped>
    .content_wrapper_block{
        display: block;
        background-color: #fff;
        padding: 15px;
        box-shadow: 0 0 25px #ddd;
    }
    .content_list_block{
        padding: 2px 8px
    }
</style>