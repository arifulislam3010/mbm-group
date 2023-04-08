<template>
    <div class="content_list_view table-responsive">
        <div class="filter_option_block">
            <span>Filters</span>
            <ul>
                <li>
                    <!-- <div @click="filter_by_type('lazz_pharma')">
                        <i v-if="$parent.filter.lazz_pharma" class="far fa-check-square"></i>
                        <i v-else class="far fa-square"></i> Only Lazz Pharma
                    </div> -->
                    <div @click="filter_by_type('selected')">
                        <i v-if="$parent.filter.selected" class="far fa-check-square"></i>
                        <i v-else class="far fa-square"></i> Only Selected Product
                    </div>
                </li>
                <li>
                    <label>Letters</label>
                    <select v-model="$parent.filter.alpha" @change="filter_by_type('alpha')">
                        <option value="">All</option>
                        <option v-for="(chr,i) in letters" :value="chr" :key="'chr-' + i">{{ chr }}</option>
                    </select>
                </li>
                <!-- <li>
                    <label>Pharmaceutical Companies</label>
                    <select v-model="$parent.filter.company_id" @change="filter_by_type('company_id')">
                        <option value="">All</option>
                        <option v-for="(item,i) in $parent.company_list" :value="item.id" :key="'pharma-' + i">{{ item.company_name }}</option>
                    </select>
                </li> -->
                <li>
                    <label>Categories</label>
                    <select v-model="$parent.filter.cat_id" @change="filter_by_type('cat_id')">
                        <option value="">All</option>
                        <option v-for="(item,i) in $parent.category_list" :value="item.id" :key="'cat-' + i">{{ item.category_name }}</option>
                    </select>
                </li>
                <li>
                    <label>Product Type</label>
                    <select v-model="$parent.filter.product_type_id" @change="filter_by_type('product_type_id')">
                        <option value="">All</option>
                        <option v-for="(item,i) in $parent.product_type_list" :value="item.id" :key="'product_type-' + i">{{ item.type_title }}</option>
                    </select>
                </li>
                <li>
                    <label>Status</label>
                    <select v-model="$parent.filter.status" @change="filter_by_type('status')">
                        <option value="">All</option>
                        <option value="1">Published</option>
                        <option value="0">Unpublished</option>
                    </select>
                </li>
            </ul>
        </div>
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Photo</th>
                    <th>Title</th>
                    <!-- <th>Slug</th> -->
                    <!-- <th style="text-align:center">Company</th> -->
                    <th style="text-align:center">Type</th>
                    <th>Info</th>
                    <!-- <th>Price</th> -->
                    <th>Post Time</th>
                    <th style="text-align:center" width="80">Selected</th>
                    <th style="text-align:center" width="80">Status</th>
                    <!-- <th></th> -->
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>
                            <div class="photo_block">
                                <template v-if="item.product_photo_infos.length>0">
                                    <carousel :paginationEnabled="false" :perPage="1">
                                        <template v-for="(photo_item,mg_index) in item.product_photo_infos">
                                            <template v-if="photo_item.product_photo_data">
                                                <slide :key="mg_index">
                                                    <img :src="photo_item.product_photo_data.content" />
                                                </slide>
                                            </template>
                                            <div class="empty_slide" v-else :key="mg_index" align="center"><i class="fa fa-images"></i></div>
                                        </template>
                                    </carousel>
                                </template>
                                <i v-else class="fa fa-images"></i>
                            </div>
                        </td>
                        <td>
                            <div>{{ item.product_title }}</div>
                            <hr class="mt-1 mb-1" />
                            <div class="action_block nowrap" align="center">
                                <a :href="$dtlProductUrl(item)" title="details view" target="_new"><i class="fa fa-external-link-alt"></i></a>
                                <span @click="clone_item(item.id)" title="clone item"><i class="far fa-clone"></i></span>
                                <template v-if="($parent.$parent.$parent.role_access.edit_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.edit))">
                                    <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                </template>
                                <template v-if="($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
                                    <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>
                                </template>
                            </div>
                            <!-- <hr class="mt-1 mb-1" />
                            <ProductGenericInfos v-if="item.generic_info.length>0" :generic_info="item.generic_info" /> -->
                        </td>
                        <!-- <td>{{ item.slug }}</td> -->
                        <!-- <td align="center">
                            <div v-if="item.company_info">
                                <span class="company_logo" v-if="item.company_info.logo">
                                    <img :src="item.company_info.logo" :title="item.company_info.company_name" />
                                </span>
                                <span v-else>{{ item.company_info.company_name }}</span>
                            </div>
                        </td> -->
                        <!-- <td align="center">
                            <div v-if="item.product_type_info" class="mt-1">
                                <span class="type_icon" v-if="item.product_type_info.icon">
                                    <img :src="item.product_type_info.icon" :title="item.product_type_info.type_title" />
                                </span>
                                <span v-else>{{ item.product_type_info.type_title }}</span>
                            </div>
                        </td> -->
                        <td>
                          <!-- {{ item.product_type_info }} -->
                            <template v-if="item.product_type_info.length>0">
                                <ProductTypeInfos :data="item.product_type_info" />
                            </template>
                            <template v-else>
                                <span><em>[Not Provided]</em></span>
                            </template>
                        </td>
                        <td>
                            <ProductCategoryInfos :cat_info="item.cat_info" />
                            <ProductTagInfos v-if="item.tag_info.length>0" :tag_info="item.tag_info" />
                            <ProductPriceInfos v-if="item.product_price_infos.length>0" :price_info="item.product_price_infos" />
                        </td>
                        <!-- <td nowrap>
                            <ProductPriceInfos :price_info="item.product_price_infos" />
                        </td> -->
                        <td>
                            <template v-if="item.created_at">
                                <div><small><b>Created at: </b>{{ item.created_at }}</small></div>
                                <div v-if="item.operator_info"><small><b>Created by: </b>{{ item.operator_info.email }}</small></div>
                            </template>

                            <template v-if="item.updated_at">
                                <hr v-if="item.created_at" class="mt-1 mb-1" />
                                <div><small><b>Updated at: </b>{{ item.updated_at }}</small></div>
                                <div v-if="item.update_operator_info"><small><b>Updated by: </b>{{ item.update_operator_info.email }}</small></div>
                            </template>
                        </td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.selected}]"></i>
                            </span>
                        </td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.status}]"></i>
                            </span>
                        </td>
                        <!-- <td align="center" nowrap>
                            <div class="action_block">
                                <a :href="$dtlProductUrl(item)" title="details view" target="_new"><i class="fa fa-external-link-alt"></i></a>
                                <span @click="clone_item(item.id)" title="clone item"><i class="far fa-clone"></i></span>
                                <template v-if="($parent.$parent.$parent.role_access.edit_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.edit))">
                                    <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                </template>
                                <template v-if="($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
                                    <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>
                                </template>
                            </div>
                        </td> -->
                    </tr>
                </template>
                <template v-else>
                    <tr><td colspan="10" align="center"><i class="fa fa-info-circle"></i> There is no data</td></tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
import ProductCategoryInfos from './components/product-category-infos'
import ProductTypeInfos from './components/product-type-infos'
import ProductGenericInfos from './components/product-generic-infos'
import ProductTagInfos from './components/product-tag-infos'
import ProductPriceInfos from './components/product-multi-price-infos'
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    components: {
        ProductCategoryInfos,
        ProductTypeInfos,
        ProductGenericInfos,
        ProductTagInfos,
        ProductPriceInfos
    },
    computed: {
        letters() {
            let letters = []
            for(let i = "A".charCodeAt(0); i <= "Z".charCodeAt(0); i++) {letters.push(String.fromCharCode([i]))}

            return letters
        }
    },
    methods: {
        filter_by_type: function(type){
            if(this.$route.query.page) delete this.$route.query['page']
            // if(type=='lazz_pharma' && this.$parent.filter[type]) this.$parent.filter[type] = false
            // else if(type=='lazz_pharma') this.$parent.filter[type] = true

            if(type=='selected' && this.$parent.filter[type]) this.$parent.filter[type] = false
            else if(type=='selected') this.$parent.filter[type] = true

            if(this.$parent.filter[type]==''){
                let query = Object.assign({}, this.$route.query);
                delete query[type];
                this.$router.replace({ query });
            }else{
                let obj = {}; obj[type] = this.$parent.filter[type];
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            }
        },
        clone_item: function(val){
            this.$parent.edit_content_id = val
            this.$parent.add_new_entity(true)
            this.$parent.$parent.$parent.clone_route(val)
        },
        edit_item: function(val){
            this.$parent.edit_content_id = val
            this.$parent.add_new_entity(true)
            this.$parent.$parent.$parent.edit_route(val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/products/delete/' + id, this.$parent.header_config).then( (response) => {
                    console.log('Get Data', response.data)
                    this.$swal("Good job!", "Data has been deleted successfully.", "success");
                    this.$parent.load_data();
                }).catch(e => {
                    console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                });
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .filter_option_block{
        display: flex;
        height: 60px;
        border: 1px solid #ddd;
        background-color: #eee;
        margin-bottom: 15px;
        padding: 0 15px;
        border-radius: 3px;
    }
    .filter_option_block > span,
    .filter_option_block > ul{
        align-self: center;
    }
    .filter_option_block > span{
        padding-right: 15px; color: #444; font-size: 14px; border-right: 1px solid #ccc;
    }
    .filter_option_block > ul{
        display: flex;
        list-style: none;
        margin: 0; padding: 0;
        margin-left: auto;
        height: 30px
    }
    .filter_option_block > ul > li{
        /* display: flex; */
        align-self: center;
        /* height: 100%; */
        font-size: 12px;
        cursor: pointer;
        user-select: none;
        margin-left: 10px
    }
    .filter_option_block > ul > li label{
        margin-bottom: 3px
    }
    .filter_option_block > ul > li select{
        display: block;
        border: 1px solid #ddd;
        padding: 1px 5px; border-radius: 3px;
        max-width: 185px;
    }
    .content_list_view > table{
        border: 1px solid #ddd
    }
    .content_list_view > table td{
        vertical-align: middle;
    }
    .photo_block{
        display: inline-block;
        width: 90px; height: 65px; padding: 5px;
        line-height: 50px; text-align: center;
        border: 1px solid #ddd; background-color: #fff;
        border-radius: 5px; overflow: hidden;
        .empty_slide{
            width: 100%;
        }
        :deep(img){
            width: 100%; height: 100%;
            object-fit: contain;
        }
        :deep(i){
            font-size: 36px; line-height: 55px; color: #ccc
        }
    }
    .company_logo{
        display: inline-block;
        width: 50px; height: 50px; padding: 5px;
        line-height: 40px; text-align: center;
        overflow: hidden;
    }
    .company_logo img{
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .type_icon{
        display: inline-block;
        width: 60px; height: 40px; padding: 5px;
        line-height: 30px; text-align: center;
        overflow: hidden;
    }
    .type_icon img{
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .status > i{
        font-size: 18px;
        color: #ccc
    }
    .status > i.active{
        color: #5dad5d
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
        cursor: pointer;
    }
</style>
