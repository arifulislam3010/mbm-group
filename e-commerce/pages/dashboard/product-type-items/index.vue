<template>
    <div class="product_type_items_block_container">
        <template v-if="$store.state.add_new_status">
            <ContentEntryForm :edit_content_id="edit_content_id" />
        </template>

        <template v-else>
            <ContentList :data="contents" :content_loader="content_loader" />

            <div v-if="pagination_show" class="row mt-3">
                <div class="col-md-12 mb-3" align="center">
                    <span class="total_record_block">Total records: {{ pagination_config.data.total }}</span>
                </div>
                <div class="col-md-12">
                    <Pagination :config="pagination_config" />
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import ContentEntryForm from './content-entry-form'
import ContentList from './content-list-view'
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'ProductTypeItemsBlockContainer',
    components: {
        ContentEntryForm,
        ContentList
    },
    data(){
        return {
            content_loader: false,
            pagination_show: false,
            product_type_list: [],
            contents: [],
            edit_content_id: this.$route.query.id?parseInt(this.$route.query.id):null,
            pagination_config: {
                data: [],
                lang: 'en',
                align: 'center',
                action: ''
            },
            filter: {
                product_type_id: '',
                status: ''
            },
            cur_page: (this.$route.query.page>0?this.$route.query.page:1),
            limit: 10,
            user_id: this.$store.state.auth_info.user_data.id,
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    watch: {
        $route(to, from){
            if(to.query.product_type_id) this.filter.product_type_id = to.query.product_type_id
            if(to.query.status) this.filter.status = to.query.status

            this.load_data(to.query.page?to.query.page:1);
        },
        get_search_info: function(){
            this.load_data();
        }
    },
    computed: {
        get_search_info: function(){
            return this.$store.state.search_keyword;
        },
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.user_access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.user_access_token
                }
            };
            return obj;
        }
    },
    mounted(){
        if(this.$route.query.product_type_id) this.filter.product_type_id = this.$route.query.product_type_id
        if(this.$route.query.status) this.filter.status = this.$route.query.status

        // load product types
        this.load_product_types();

        // load data
        this.load_data();
    },
    methods: {
        ...mapActions({
            productTypeNameSubmit: 'product_type_info_name/DATA_SUBMIT'
        }),
        ...mapMutations({
            add_new_entity: 'ADD_NEW_STATUS',
            form_submit_state: 'FORM_SUBMIT_STATUS'
        }),
        async load_product_types(){
            let url = '/api/product-types';

            this.product_type_list = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                this.product_type_list = response.data.data
            }).catch(e => {
                this.$toast.error('Product type load failed!!!', {icon: "error_outline"})
            });
        },
        async load_data(pg=this.cur_page){
            let url = '/api/product-type-info-name'+ (this.limit>0?'?limit=' + this.limit:'') + (pg>1?'&page=' + pg:'') + (this.$parent.$parent.role_access.view_others=='0'?'&own_result=true':'') + (this.$store.state.search_keyword?'&keyword=' + this.$store.state.search_keyword:'') + (this.filter.product_type_id?'&product_type_id=' + this.filter.product_type_id:'') + (this.filter.status?'&status=' + this.filter.status:'');

            this.content_loader = true
            this.pagination_show = false
            this.contents = [];
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                if(response.data.data.length>0){
                    this.contents = response.data.data
                    this.pagination_config.data = response.data.meta
                    this.$parent.total_rows = this.pagination_config.data.total
                    this.pagination_show = true
                }
                this.content_loader = false
                this.cur_page = pg
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false
            });
        }
    }
}
</script>
<style scoped>

</style>
