<template>
    <div v-if="$store.state.category_info.get_body_data.length>0" class="products_by_category_block">
        <template v-for="(item,index) in $store.state.category_info.get_body_data">
            <div :key="index" class="category_list_block mt-4">
                <div class="cat_info_section">
                    <label>{{ item.category_name }}</label>
                    <nuxt-link :to="{path: '/'+ $store.state.search_page_path +'?category=' + item.slug}">View All <i class="fa fa-angle-right"></i></nuxt-link>
                </div>
                <ProductGridViewBlock :cat_id="item.id" :pp_custom="[[425,2],[768,4],[990,5]]" />
            </div>
        </template>
    </div>
</template>
<script>
import ProductGridViewBlock from './ProductList'
export default {
    name: 'ProductsByCategoryBlock',    
    components: {
        ProductGridViewBlock
    },
    data(){
        return {

        }
    }
}
</script>
<style lang="scss" scoped>
    .products_by_category_block{
        display: block;
    }
    .category_list_block{
        .cat_info_section{
            display: flex;
            height: 50px;

            & > label{
                align-self: center;
                font-size: 16px; font-weight: bold;
                margin-bottom: 0;
            }

            & > a{
                align-self: center;
                margin-left: auto;
                font-size: 16px; font-weight: bold;
                transition: all 0.4s;

                &:hover{
                    color: $sys_brand_color
                }
            }
        }

        &:nth-child(6n+0) > .cat_info_section label{
            color: #007499;
        }
        &:nth-child(6n+1) > .cat_info_section label{
            color: #333366;
        }
        &:nth-child(6n+2) > .cat_info_section label{
            color: #336600;
        }
        &:nth-child(6n+3) > .cat_info_section label{
            color: #cc8844;
        }
        &:nth-child(6n+4) > .cat_info_section label{
            color: #CC6666;
        }
        &:nth-child(6n+5) > .cat_info_section label{
            color: #666600;
        }
    }    
</style>