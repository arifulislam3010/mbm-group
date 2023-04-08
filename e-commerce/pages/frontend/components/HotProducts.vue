<template>
    <div v-if="pre_loader || products.length>0" class="hot_products_block mt-4">
        <label><i class="fa fa-bolt"></i> Most Selling Products</label>
        <ProductSwiperListView v-if="$device.isMobile" :loader="pre_loader" :data="products" />
        <ProductSliderListView v-else :loader="pre_loader" :data="products" :pp_custom="pp_custom" />
    </div>
</template>
<script>
import ProductSliderListView from '~/components/content_display/ProductSliderListView'
import ProductSwiperListView from '~/components/content_display/ProductSwiperListView'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'HotProductsBlock',
    props: ['pp_custom'],
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
            if(this.$store.state.product.hot_data.length==0) this.load_products();
            else this.products = this.$store.state.product.hot_data;
        }else{
            let getCacheData = localStorage.getItem('hot_products')
            this.products = JSON.parse(getCacheData)
        }
    },
    methods: {
        ...mapActions({        
            getHotProducts: 'product/GET_HOT_PRODUCTS'
        }),
        async load_products(){
            // setup submitted data
            let config_data = {                
                access_token: this.user_access_token,
                limit: this.limit                
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getHotProducts(config_data)

            // get products list
            this.products = this.$store.state.product.hot_data

            // cache store
            localStorage.setItem('hot_products', JSON.stringify(this.products))

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    .hot_products_block{
        display: block;
    }
    .hot_products_block > label{
        font-size: 16px; font-weight: bold; color: #CD0000;
    }
</style>