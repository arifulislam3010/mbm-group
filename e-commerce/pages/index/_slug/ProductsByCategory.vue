<template>
    <div class="category_page_content_block mt-2">
        <div class="container">            
            <div class="row">
                <div class="col-md-3">
                    <div class="content_wrapper_block mb-3">
                        <div v-if="pre_loader">
                            <div v-for="(n,i) in 3" :key="i">
                                <CardViewProductPreLoader />
                            </div>
                        </div>
                        <div v-else>
                            <FilterItemsBlock />
                        </div>
                    </div>
                </div>
                <div class="col-md-9 mb-3">
                    <div class="content_wrapper_block">
                        <div v-if="pre_loader" class="row">
                            <div v-for="(n,i) in 12" class="col-md-3 mb-3" :key="i">
                                <CardViewProductPreLoader />
                            </div>
                        </div>
                        <template v-else-if="data.length>0">
                            <div class="content_list_block">
                                <div class="row">
                                    <div v-for="(item,index) in data" :key="index" class="col-6 col-md-3 mb-2 pl-1 pr-1">
                                        <MobileCardViewProduct v-if="$device.isMobile" :item="item" />
                                        <CardViewProduct v-else :item="item" />
                                    </div>
                                </div>
                            </div>
                            <div v-if="pagination_show" class="row mt-3">
                                <div class="col-md-12 mb-3" align="center">
                                    <span class="total_record_block">Total records: {{ pagination_config.data.total }}</span>
                                </div>
                                <div class="col-md-12">
                                    <Pagination :config="pagination_config" />
                                </div>
                            </div>
                        </template>
                        <div v-else>
                            <EmptyContentBlock />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FilterItemsBlock from '@/components/content_display/FilerItemsBlock.vue'
import CardViewProduct from '~/components/content_display/CardViewProduct'
import MobileCardViewProduct from '~/components/content_display/MobileCardViewProduct'
import CardViewProductPreLoader from '~/components/content_display/CardViewProductPreLoader'
import EmptyContentBlock from '~/components/content_display/EmptyContentBlock'
export default {
    name: 'CategoryPageContentBlock',
    components: {
        FilterItemsBlock,
        CardViewProduct,
        MobileCardViewProduct,
        CardViewProductPreLoader,
        EmptyContentBlock
    },
    data(){
        return {
            pre_loader: true,
            pagination_show: false,
            data: [],
            sel_cat_info: '',
            sel_product_type_info: '',
            sel_company_info: '',
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 20
        }
    },
    watch: {
        $route(to,from){
            if(this.$route.query.category) this.sel_cat_info = this.$route.query.category
            else this.sel_cat_info = ''
            if(this.$route.query['product-type']) this.sel_product_type_info = this.$route.query['product-type']
            else this.sel_product_type_info = ''
            if(this.$route.query.company) this.sel_company_info = this.$route.query.company
            else this.sel_company_info = ''

            this.load_req_data()
        }
    },
    mounted(){
        this.load_req_data()
    },
    methods: {
        async load_req_data(pg=this.cur_page){
            this.pre_loader = true;
            this.pagination_show = false
            this.data = [];
            
            this.$parent.$parent.breadcrumb_data = {
                pre_loader: true,
                cat_info: ''                
            };

            this.$axios.get('/api/products/category/' + this.$parent.$parent.current_slug + (this.limit>0?'?limit=' + this.limit:'') + (this.sel_cat_info?'&category=' + this.sel_cat_info:'') + (this.sel_product_type_info?'&product-type=' + this.sel_product_type_info:'') + (this.sel_company_info?'&company=' + this.sel_company_info:'') + (pg>1?'&page=' + pg:''), this.$parent.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data.data)
                if(response.data.data.length>0){
                    this.data = response.data.data
                    this.pagination_config.data = response.data.meta                    
                    this.pagination_show = true

                    this.$parent.$parent.breadcrumb_data.pre_loader = false
                    this.$parent.$parent.breadcrumb_data.cat_info = this.data[0].cat_info
                }else{
                    this.$parent.$parent.breadcrumb_data.pre_loader = false
                    this.$parent.$parent.breadcrumb_data.cat_default_info = this.$parent.$parent.current_slug
                }
                this.pre_loader = false
                this.cur_page = pg
            }).catch(e => {
                console.log(e)                
                this.pre_loader = false;
            });
        }
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