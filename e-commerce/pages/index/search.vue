<template>
    <div class="product_page_container">
        <BreadcrumbBlock :data="breadcrumb_data" />
        <nuxt-child />
    </div>
</template>
<script>
import BreadcrumbBlock from '~/components/Breadcrumb'
export default {
    name: 'ProductPageContainer',
    components: {
        BreadcrumbBlock
    },
    data(){
        return {
            access_token: this.$store.state.oauth_token.token,
            breadcrumb_data: {
                pre_loader: true,
                srch_keyword: ''
            }
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.access_token
                }
            };
            return obj;
        },
        srch_keyword: function() {
            return this.$route.query.keyword
        }
    }
}
</script>
<style scoped>

</style>