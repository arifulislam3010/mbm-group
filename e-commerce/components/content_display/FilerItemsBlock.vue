<template>
    <div :class="['filter_items_list_block',{mobile_device:$device.isMobile}]">
        <template v-if="!$route.params.slug">
            <label>Categories</label>
            <div class="list mb-3">
                <template v-if="category_list.length>0">
                    <ul>
                        <template v-for="(item,index) in category_list">
                            <template v-if="sel_cat_info==item.slug">
                                <li :key="index">
                                    <i class="far fa-check-square" @click="filter_click('category','')"></i>
                                    <span>{{ item.category_name }}</span>
                                </li>
                            </template>
                            <template v-else-if="sel_cat_info==''">
                                <li :key="index">
                                    <i class="far fa-square" @click="filter_click('category',item.slug)"></i>
                                    <span>{{ item.category_name }}</span>
                                </li>
                            </template>
                        </template>
                    </ul>
                </template>
                <template v-else>
                    <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="20" :r2_h="0" />
                </template>
            </div>
        </template>

        <label>Product Types</label>
        <div class="list mb-3">
            <template v-if="product_type_list.length>0">
                <ul>
                    <template v-for="(item,index) in product_type_list">
                        <template v-if="sel_product_type_info==item.slug">
                            <li :key="index">
                                <i class="far fa-check-square" @click="filter_click('product-type','')"></i>
                                <span>{{ item.type_title }}</span>
                            </li>
                        </template>
                        <template v-else-if="sel_product_type_info==''">
                            <li :key="index">
                                <i class="far fa-square" @click="filter_click('product-type',item.slug)"></i>
                                <span>{{ item.type_title }}</span>
                            </li>
                        </template>
                    </template>
                </ul>
            </template>
            <template v-else>
                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="20" :r2_h="0" />
            </template>
        </div>

        <!-- <label>Companies</label>
        <div class="list">
            <template v-if="company_list.length>0">
                <ul>
                    <template v-for="(item,index) in company_list">
                        <template v-if="sel_company_info==item.slug">
                            <li :key="index">
                                <i class="far fa-check-square" @click="filter_click('company','')"></i>
                                <span>{{ item.company_name }}</span>
                            </li>
                        </template>
                        <template v-else-if="sel_company_info==''">
                            <li :key="index">
                                <i class="far fa-square" @click="filter_click('company',item.slug)"></i>
                                <span>{{ item.company_name }}</span>
                            </li>
                        </template>
                    </template>
                </ul>
            </template>
            <template v-else>
                <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="20" :r2_h="0" />
            </template>
        </div> -->

        <!-- <label class="mt-3">Price Range</label>
        <div class="list">
            <ul>
                <li></li>
            </ul>
        </div> -->
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    name: 'FilterItemsListBlock',
    data(){
        return {
            pre_loader: false,
            category_list: [],
            product_type_list: [],
            company_list: [],
            sel_cat_info: this.$route.query.category?this.$route.query.category:'',
            sel_product_type_info: this.$route.query['product-type']?this.$route.query['product-type']:'',
            sel_company_info: this.$route.query.company?this.$route.query.company:'',
            user_access_token: this.$store.state.oauth_token.token,
            limit: 8
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
        }
    },
    mounted(){
        if(Object.keys(this.$store.state.category_info.get_data).length==0) this.load_categories();
        else this.category_list = this.$store.state.category_info.get_data;

        if(Object.keys(this.$store.state.product_type.get_data).length==0) this.load_product_types();
        else this.product_type_list = this.$store.state.product_type.get_data;

        if(Object.keys(this.$store.state.company_info.get_data).length==0) this.load_companies();
        else this.company_list = this.$store.state.company_info.get_data;
    },
    methods: {
        ...mapActions({
            getCategories: 'category_info/GET_DATA',
            getProductTypes: 'product_type/GET_DATA',
            getCompanies: 'company_info/GET_DATA'
        }),
        filter_click: function(type,val){
            if(val==''){
                let query = Object.assign({}, this.$route.query);
                delete query[type];
                this.$router.replace({ query });
            }else{
                let obj = {}; obj[type] = val;
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            }
        },
        async load_categories(){
            // setup submitted data
            let config_data = {
                access_token: this.user_access_token,
                limit: this.limit
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getCategories(config_data)

            // get products list
            this.category_list = this.$store.state.category_info.get_data

            // call for submit
            this.pre_loader = false
        },
        async load_product_types(){
            // setup submitted data
            let config_data = {
                access_token: this.user_access_token,
                // limit: this.limit
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getProductTypes(config_data)

            // get products list
            this.product_type_list = this.$store.state.product_type.get_data

            // call for submit
            this.pre_loader = false
        },
        async load_companies(){
            // setup submitted data
            let config_data = {
                access_token: this.user_access_token,
                // limit: this.limit
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getCompanies(config_data)

            // get products list
            this.company_list = this.$store.state.company_info.get_data

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    label{
        display: block;
        font-size: 12px;
        /* font-weight: bold; */
        color: #999;
    }
    .list{
        display: block;
        max-height: 260px;
        overflow-y: auto;
    }
    .mobile_device .list{
        max-height: none;
        overflow-y: hidden;
    }
    .list ul{
        list-style: none;
        margin: 0; padding: 0;
    }
    .list ul > li{
        display: flex;
        height: 26px;
    }
    .list ul > li > i,
    .list ul > li > span{
        align-self: center;
        font-size: 13px;
        color: #666;
    }
    .list ul > li > span{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .list ul > li > i{
        font-size: 20px;
        margin-right: 7px;
        color: #ccc;
        cursor: pointer;
        transition: all 0.4s;
    }
    .list ul > li > i:hover{
        color: #CD0000
    }
</style>
