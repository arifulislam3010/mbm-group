<template>
    <div class="companies_more_products_block">
        <div v-if="pre_loader" class="row">            
            <div v-for="(n,i) in 4" :key="i" class="col-md-12">                
                <FormBlockLoader :cols="1" :height="165" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="150" :r2_h="0" />
            </div>
        </div>
        <div v-else class="row pl-2 pr-2">
            <div v-for="(item,index) in data" :key="index" class="col-6 col-md-12 mb-3 pr-2 pl-2">
                <MobileCardViewProduct v-if="$device.isMobile" :item="item" />
                <CardViewProduct v-else :item="item" />
            </div>
        </div>
    </div>
</template>
<script>
import CardViewProduct from '~/components/content_display/CardViewProduct'
import MobileCardViewProduct from '~/components/content_display/MobileCardViewProduct'
export default {
    name: 'CompaniesMoreProductsBlock',
    props: ['company_id'],
    components: {
        CardViewProduct,
        MobileCardViewProduct
    },
    data(){
        return {
            pre_loader: true,
            data: '',
            limit: 5
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        async load_data(){
            this.pre_loader = true;
            
            this.$axios.get('/api/products/by-company/' + this.company_id + '?limit=' + this.limit, this.$parent.$parent.header_config).then( (response) => {
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
</style>