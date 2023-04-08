<template>
    <div class="related_products_block">
        <div v-if="pre_loader" class="row">            
            <div v-for="(n,i) in 4" :key="i" class="col-md-3">                
                <CardViewProductPreLoader />
            </div>
        </div>
        <div v-else class="row">
            <div v-for="(item,index) in data" :key="index" class="col-6 col-md-3 mb-3 pr-2 pl-2">
                <MobileCardViewProduct v-if="$device.isMobile" :item="item" />
                <CardViewProduct v-else :item="item" />
            </div>
        </div>
    </div>
</template>
<script>
import CardViewProduct from '~/components/content_display/CardViewProduct'
import MobileCardViewProduct from '~/components/content_display/MobileCardViewProduct'
import CardViewProductPreLoader from '~/components/content_display/CardViewProductPreLoader'
export default {
    name: 'RelatedProductsBlock',
    props: ['product_id'],
    components: {
        CardViewProduct,
        MobileCardViewProduct,
        CardViewProductPreLoader
    },
    data(){
        return {
            pre_loader: true,
            data: '',
            limit: 8
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        async load_data(){
            this.pre_loader = true;

            let url = `/api/products/related?product_id=${this.product_id}&limit=${this.limit}`
            
            this.$axios.get(url, this.$parent.$parent.header_config).then( (response) => {
                // console.log('Get Data', response.data.data)
                this.data = response.data.data                
                this.pre_loader = false
            }).catch(e => {
                console.log(e)                
                this.pre_loader = false;
            });
        }
    }
}
</script>
<style scoped>
    .related_products_block{
        display: block;
        margin: 0 10px;
    }
</style>