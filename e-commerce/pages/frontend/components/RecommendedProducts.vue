<template>
    <div v-if="pre_loader || products.length>0" class="recomended_products_block mt-4">
        <label>Just For You</label>        
        <ProductSwiperListView v-if="$device.isMobile" :loader="pre_loader" :data="products" />
        <ProductSliderListView v-else :loader="pre_loader" :data="products" :pp_custom="pp_custom" />
    </div>
</template>
<script>
import ProductSliderListView from '~/components/content_display/ProductSliderListView'
import ProductSwiperListView from '~/components/content_display/ProductSwiperListView'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'RecomendedProductsBlock',
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
            if(this.$store.state.product.recommended_data.length==0) this.load_products();
            else this.products = this.$store.state.product.recommended_data;
        }
        // else{
        //     let getCacheData = localStorage.getItem('recommended_products')
        //     this.products = JSON.parse(getCacheData)
        // }
    },
    methods: {
        ...mapActions({        
            getRecomendedProducts: 'product/GET_RECOMENDED_PRODUCTS'
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
            await this.getRecomendedProducts(config_data)

            // get products list
            this.products = this.$store.state.product.recommended_data

            // cache store
            // localStorage.setItem('recommended_products', JSON.stringify(this.products))

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    .recomended_products_block{
        display: block;
    }
    .recomended_products_block > label{
        font-size: 16px; font-weight: bold; color: #007499;
    }
</style>