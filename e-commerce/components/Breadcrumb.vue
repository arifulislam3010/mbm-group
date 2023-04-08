<template>
    <div :class="['breadcrumb_block','mb-1',{mobile_device:$device.isMobile}]">
        <div class="container pt-2">
            <template v-if="data.pre_loader">
                <ul class="row clearfix">
                    <li class="col-2" v-for="(n,i) in 3" :key="i">
                        <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    </li>
                </ul>
            </template>
            <ul v-else class="clearfix">
                <li>
                    <nuxt-link :to="{path: '/'}">
                        <i class="fa fa-home"></i>
                    </nuxt-link>
                </li>
                <template v-if="data.page_info">
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <li>/</li>
                    <template v-if="$route.params.page">
                        <li v-if="$route.params.slug==$store.state.user_dashboard_path"><nuxt-link :to="{path: '/' + $store.state.user_dashboard_path}">{{ data.page_info }}</nuxt-link></li>
                        <li v-else-if="$route.params.slug==$store.state.static_page_path">{{ data.page_info }}</li>
                        <!-- <li><i class="fa fa-angle-right"></i></li> -->
                        <li>/</li>
                        <li>{{ $strCapitalize(get_string($route.params.page)) }}</li>
                    </template>
                    <li v-else>{{ data.page_info }}</li>
                </template>
                <template v-if="data.cat_info">
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <!-- <li>/</li> -->
                    <template v-for="(cat_data, cat_index) in data.cat_info">
                        <!-- <li v-if="cat_index>0" :key="'cat-item-div-' + cat_index"><i class="fa fa-angle-right"></i></li> -->
                        <template v-if="cat_data.cat_data_info && cat_data.cat_data_info.category_name">
                          <li :key="'cat-item-div-' + cat_index">/</li>
                          <li :key="'cat-item-' + cat_index">
                              <nuxt-link :to="{path: '/search?category=' + cat_data.cat_data_info.slug}">{{ cat_data.cat_data_info.category_name }}</nuxt-link>
                          </li>
                        </template>
                    </template>
                </template>
                <template v-else-if="data.cat_default_info">
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <li>/</li>
                    <li>{{ data.cat_default_info }}</li>
                </template>
                <template v-if="data.product_title">
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <li>/</li>
                    <li>{{ data.product_title }}</li>
                </template>
                <template v-if="data.srch_info_text">
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <li>/</li>
                    <li>Search</li>
                    <!-- <li><i class="fa fa-angle-right"></i></li> -->
                    <li>/</li>
                    <li>{{ data.srch_info_text }}</li>
                </template>
            </ul>
        </div>
    </div>
</template>
<script>
export default {
    name: 'BreadcrumbBlock',
    props: {
        data: Object
    },
    methods: {
        get_string: function(str){
            return str.replace(this.$store.state.dashboard.prefix + '-',' ').replace(/-/g,' ');
        }
    }
}
</script>
<style lang="scss" scoped>
    .breadcrumb_block{
        position: relative;
        background-color: #fff;
        z-index: 1;
        ul{
            display: block;
            list-style: none;
            min-height: 45px;
            padding: 0; margin: 0;
            & > li{
                float: left;
                line-height: 45px;
                padding: 0 5px;
                & > a {
                    font-size: 14px;
                    color: #ac3e13
                }
                & > i{
                    display: inline-block;
                    font-size: 18px;
                    line-height: 45px;
                    color: #999;
                }
            }
        }
        
        .mobile_device ul{
            min-height: 30px;
            & > li{
                line-height: 30px;
                & > i{
                    line-height: 30px
                }
            }
        }
    }
</style>
