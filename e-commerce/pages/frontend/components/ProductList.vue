<template>
    <div v-if="pre_loader || products.length>0" class="products_block">
        <ProductSwiperListView v-if="$device.isMobile" :loader="pre_loader" :data="products" />
        <ProductSliderListView v-else :loader="pre_loader" :data="products" :pp_custom="pp_custom" />
    </div>
</template>
<script>
import ProductSliderListView from '~/components/content_display/ProductSliderListView'
import ProductSwiperListView from '~/components/content_display/ProductSwiperListView'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'ProductsBlock',
    props: ['cat_id','pp_custom'],
    components: {
        ProductSliderListView,
        ProductSwiperListView
    },
    data(){
        return {
            pre_loader: false,
            products: [],
            user_access_token: this.$store.state.oauth_token.token,
            limit: 15
        }
    },
    mounted(){
        if($nuxt.isOnline){            
            if(Object.keys(this.$store.state.product.products_by_category).length > 0 && this.$store.state.product.products_by_category[this.cat_id]) this.products = this.$store.state.product.products_by_category[this.cat_id];
            else this.load_products();
        }else{
            let getCacheData = localStorage.getItem('products-' + this.cat_id)
            this.products = JSON.parse(getCacheData)
        }
    },
    methods: {
        ...mapActions({        
            getProductsByCategory: 'product/GET_PRODUCTS_BY_CATEGORY'
        }),
        async load_products(){
            // setup submitted data
            let config_data = {
                cat_id: this.cat_id,
                access_token: this.user_access_token,
                limit: this.limit
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getProductsByCategory(config_data)

            // get products list
            if(Object.keys(this.$store.state.product.products_by_category).length > 0 && this.$store.state.product.products_by_category[this.cat_id]){
                this.products = this.$store.state.product.products_by_category[this.cat_id]

                // cache store
                localStorage.setItem('products-' + this.cat_id, JSON.stringify(this.products))
            }

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    .products_block{
        display: block;
    }
    .products_block > label{
        font-size: 16px; font-weight: bold; color: #007499;
    }
</style>