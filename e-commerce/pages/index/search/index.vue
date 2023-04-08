<template>
    <div class="dynamic_page_content_block">
        <div v-if="$route.query.category && (cat_page_loader || (cat_page_info.length > 0 && cat_page_info[0] && cat_page_info[0].banner_image))" class="banner_block">
            <template v-if="cat_page_loader">
                <div class="banner_img_block">
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                    <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                </div>
            </template>
            <div v-else class="banner_content_block">
                <div class="video_banner_block" v-if="cat_page_info[0].video_url">
                    <div class="bg_shadow"></div>
                    <iframe width="100%" frameborder="0" allowfullscreen :src="get_embed_code(cat_page_info[0].video_url)" allow="autoplay"></iframe>
                </div>
                <img v-else :src="cat_page_info[0].banner_image" />
            </div>
        </div>
        <div :class="['container',{has_img_banner:cat_page_info[0] && cat_page_info[0].banner_image},{has_video_banner:cat_page_info[0] && cat_page_info[0].video_url}]">            
            <div class="row">
                <div class="col-md-3">
                    <div class="content_wrapper_block mb-3">
                        <div v-if="pre_loader">
                            <div v-for="(n,i) in 3" :key="i">
                                <CardViewProductPreLoader />
                            </div>
                        </div>
                        <div v-else>
                            <div class="filter_block" v-if="$device.isMobile">
                                <div @click="float_filter_open=true"><i class="fa fa-filter"></i> Filter</div>
                                <FloatFilterBlock v-if="float_filter_open" />
                            </div>
                            <FilterItemsBlock v-else />
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

                    <RecommendedProducts :pp_custom="[[425,2],[768,3],[990,4]]" />
                    <HotProducts :pp_custom="[[425,2],[768,3],[990,4]]" />
                </div>
            </div>

            <!-- <template v-if="!pre_loader && data.length==0">
                <RecommendedProducts />
                <HotProducts />
            </template> -->
        </div>
    </div>
</template>
<script>
import FloatFilterBlock from './FloatFilterBlock'
import FilterItemsBlock from '@/components/content_display/FilerItemsBlock'
import CardViewProduct from '@/components/content_display/CardViewProduct'
import MobileCardViewProduct from '~/components/content_display/MobileCardViewProduct'
import CardViewProductPreLoader from '@/components/content_display/CardViewProductPreLoader'
import EmptyContentBlock from '@/components/content_display/EmptyContentBlock'
import RecommendedProducts from '@/pages/frontend/components/RecommendedProducts'
import HotProducts from '@/pages/frontend/components/HotProducts'
export default {
    name: 'DynamicPageContentBlock',
    components: {
        FloatFilterBlock,
        FilterItemsBlock,
        CardViewProduct,
        MobileCardViewProduct,
        CardViewProductPreLoader,
        EmptyContentBlock,
        RecommendedProducts,
        HotProducts
    },
    data(){
        return {
            pre_loader: true,
            cat_page_loader: false,
            pagination_show: false,
            data: [],
            cat_page_info: [],
            float_filter_open: false,
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
        $route(to, from){
            this.float_filter_open = false
            if(to.query.category) this.category_page_info()
            this.load_req_data()
        }
    },
    mounted(){
        this.float_filter_open = false
        if(this.$route.query.category) this.category_page_info()
        this.load_req_data()
    },
    methods: {
        get_embed_code: function(data){            
            if(data.indexOf("watch?v=")!=-1) {
                let getData = data.split("watch?v=")
                return data.replace('watch?v=','embed/') + '?autoplay=1&loop=1&playlist='+ getData[1] +'&rel=0&cc_load_policy=1&mute=1&showinfo=0&controls=0'
            }
        },
        async category_page_info(){
            this.cat_page_loader = true;            
            this.cat_page_info = [];            

            this.$axios.get('/api/categories/' + this.$route.query.category, this.$parent.header_config).then( (response) => {
                console.log('Get Banner Data', response.data.data)                
                this.cat_page_info = response.data.data
                this.cat_page_loader = false                
            }).catch(e => {
                console.log(e)                
                this.cat_page_loader = false;
            });
        },
        async load_req_data(pg=this.cur_page){
            this.pre_loader = true;
            this.pagination_show = false
            this.data = [];            

            if(this.$parent.srch_keyword){
                let obj = {}; obj['keyword'] = this.$parent.srch_keyword;
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            }

            // console.log('Query', this.$route.query)

            let qry_str = '', getQueryObj = this.$route.query, queryObjLen = Object.keys(getQueryObj).length, srch_info_text = '';
            if(queryObjLen > 0){
                for(var key in getQueryObj){
                    qry_str = qry_str + (qry_str==''?'?':'&') + key + '=' + getQueryObj[key]
                    srch_info_text = srch_info_text + (srch_info_text==''?'':', ') + getQueryObj[key]
                }
            }else srch_info_text = 'All';

            // console.log('Get Strign', qry_str)

            this.$parent.breadcrumb_data = {
                pre_loader: true,
                srch_info_text: srch_info_text
            };

            this.$axios.get('/api/products/search' + qry_str + (this.limit>0? (qry_str==''?'?':'&') + 'limit=' + this.limit:'') + (pg>1?'&page=' + pg:''), this.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data.data)
                if(response.data.data.length>0){
                    this.data = response.data.data
                    this.pagination_config.data = response.data.meta                    
                    this.pagination_show = true
                }
                
                this.$parent.breadcrumb_data.pre_loader = false
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
<style lang="scss" scoped>
    .banner_content_block{
        display: block;
        position: relative;
        width: 100%;
        // max-height: 350px;
        // margin-bottom: 25px;

        & > .video_banner_block{
            position: relative;            
            & > .bg_shadow{
                position: absolute;
                left: 0; top: 0;
                width: 100%; height: 100%;
                background-color: #00000040;
                z-index: 1;
            }
        }        

        & > img{
            width: 100%; height: 100%;
            object-fit: cover;
        }
    }
    .content_wrapper_block{
        display: block;
        background-color: #fff;
        padding: 15px;
        box-shadow: 0 0 25px #999;
    }
    .content_list_block{
        padding: 2px 8px
    }
    .filter_block{
        display: block;        
    }
    iframe{
        aspect-ratio: 16/9;
        margin-top: -150px
    }
    .container{
        position: relative;        
        z-index: 5;

        &.has_img_banner{
            margin-top: -60px
        }

        &.has_video_banner{
            margin-top: -150px;
        }
    }
    @media screen and (max-width: 480px) {
        iframe{            
            margin-top: -0
        }
        .container{
            &.has_img_banner,
            &.has_video_banner{
                margin-top: -25px;
            }
        }
    }
</style>