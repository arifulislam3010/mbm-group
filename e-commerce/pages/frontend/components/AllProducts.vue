<template>
    <div class="all_products_block mt-4">
        <label>All Medicines</label>
        <div v-if="pre_loader" class="row">            
            <div v-for="(n,i) in 4" :key="i" class="col-md-3">                
                <FormBlockLoader :cols="1" :height="165" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="150" :r2_h="0" />
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </div>
        <div v-else class="product_list_block">
            <div class="row">
                <div v-for="(item,index) in products" :key="index" class="col-6 col-md-2 mb-2 pl-1 pr-1">
                    <MobileCardViewProduct v-if="$device.isMobile" :item="item" />
                    <CardViewProduct v-else :item="item" />
                </div>
            </div>            
        </div>

        <div v-if="pagination_config.data.last_page>=cur_page" class="row mt-3">
            <template v-if="more_loader">
                <div v-for="(n,i) in 4" :key="i" class="col-md-3">                
                    <FormBlockLoader :cols="1" :height="165" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="150" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                </div>
            </template>
            <div v-else class="col-md-12 mb-3 load_more_btn" align="center">
                <span @click="load_products(cur_page + 1)">Load More</span>
            </div>
        </div>

        <!-- <div v-if="pagination_show" class="row mt-3">            
            <div class="col-md-12 mb-3" align="center">
                <span class="total_record_block">Total records: {{ pagination_config.data.total }}</span>
            </div>
            <div class="col-md-12">
                <Pagination :config="pagination_config" />
            </div>
        </div> -->
    </div>
</template>
<script>
import CardViewProduct from '~/components/content_display/CardViewProduct'
import MobileCardViewProduct from '~/components/content_display/MobileCardViewProduct'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'AllProductsBlock',
    components: {
        CardViewProduct,
        MobileCardViewProduct
    },
    data(){
        return {
            pre_loader: false,
            more_loader: false,
            products: [],
            pagination_show: false,
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            user_access_token: this.$store.state.oauth_token.token,
            limit: 18
        }
    },
    watch: {
        $route(to, from){
            this.load_products(to.query.page?to.query.page:1);
        }
    },
    mounted(){
        if($nuxt.isOnline){
            if(this.$store.state.product.all_data.length==0) this.load_products();
            else this.products = this.$store.state.product.all_data;
        }else{
            let getCacheData = localStorage.getItem('all_products')
            this.products = JSON.parse(getCacheData)
        }
    },
    methods: {
        ...mapActions({        
            getAllProducts: 'product/GET_ALL_PRODUCTS'
        }),
        async load_products(pg=this.cur_page){
            // setup submitted data
            let config_data = {                
                access_token: this.user_access_token,
                page: pg,
                limit: this.limit                
            }

            // content pre loader active
            if(this.products.length==0){
                this.pre_loader = true
                this.pagination_show = false
            }else{
                this.more_loader = true
            }

            // call function for load data
            await this.getAllProducts(config_data)            

            // get products list
            this.products.push(...this.$store.state.product.all_data)
            this.pagination_config.data = this.$store.state.product.all_pagination_data

            // cache store
            localStorage.setItem('all_products', JSON.stringify(this.products))

            // call for submit
            this.pre_loader = false
            this.more_loader = false
            if(this.products.length>0) this.pagination_show = true
            this.cur_page = pg
        }
    }
}
</script>
<style lang="scss" scoped>
    .all_products_block{
        display: block;
    }
    .all_products_block > label{
        font-size: 16px; font-weight: bold; color: $label_heading_color;
    }
    .product_list_block{
        padding: 0 13px;
    }
    .load_more_btn{
        display: block;
        position: relative;
        text-align: center;
        margin-top: 15px;
    }
    .load_more_btn > span{
        display: inline-block;
        background-color: $orange;
        color: $white;
        padding: 5px 20px;
        border-radius: 3px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.4s;
    }
    .load_more_btn > span:hover{        
        background-color: $ripple_orange;        
        padding: 5px 15px;
    }
</style>