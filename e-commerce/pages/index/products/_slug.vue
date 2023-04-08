<template>
    <div class="product_details_block">
        <div class="container mt-2">
            <div v-if="invalid" class="row">
                <div class="col-md-12 mb-3">
                    <div class="content_wrapper_block">
                        <EmptyContentBlock />
                    </div>

                    <RecommendedProducts :pp_custom="[[425,2],[768,3],[990,5]]" />
                    <HotProducts :pp_custom="[[425,2],[768,3],[990,5]]" />
                </div>
            </div>
            <div v-else class="row">
                <div class="col-md-12">
                    <div :class="['content_wrapper_block_fuller','mb-5',{mobile_device:$device.isMobile}]">
                        <template v-if="pre_loader">
                            <PhotoContentPreLoaderBlock />
                        </template>
                        <template v-else>
                            <PhotoContentFullerBlock :data="JSON.stringify(data.product_photo_infos)" />
                        </template>
                        <div class="basic_wrapper_block mb-2">
                            <template v-if="pre_loader">
                                <BasicContentPreLoaderBlock />
                            </template>
                            <template v-else>
                                <BasicContentBlock :data="data" />
                            </template>
                        </div>
                    </div>
                    <!-- <div class="content_wrapper_block mb-3">
                        <div class="row">
                            <div class="col-md-6 pr-md-2 mb-2">
                                <template v-if="pre_loader">
                                    <PhotoContentPreLoaderBlock />
                                </template>
                                <template v-else>
                                    <PhotoContentZoomerBlock :data="data.product_photo_infos" />
                                </template>
                            </div>
                            <div class="col-md-6 pl-md-2 mb-2">
                                <template v-if="pre_loader">
                                    <BasicContentPreLoaderBlock />
                                </template>
                                <template v-else>
                                    <BasicContentBlock :data="data" />
                                </template>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="col-md-12">
                    <div v-if="pre_loader || data.product_infos.length>0" class="mb-3">
                        <!-- <div class="info_label">Product Details</div> -->
                        <div class="content_wrapper_block">
                            <template v-if="pre_loader">
                                <InfoContentPreLoaderBlock />
                            </template>
                            <template v-else-if="data.product_infos">
                                <InfoContentBlockWithSwiperTab v-if="$device.isMobile" :data="data.product_infos" />
                                <InfoContentBlockWithTab v-else :data="data.product_infos" />
                            </template>
                        </div>
                    </div>

                    <!-- <div class="mb-3">
                        <div class="info_label">Rating & Reviews</div>
                        <div class="content_wrapper_block">
                            <template v-if="pre_loader">
                                <RatingNReviewPreLoaderBlock />
                            </template>
                            <template v-else>
                                <RatingNReviewsBlock />
                            </template>
                        </div>
                    </div> -->

                    <div class="mb-3">
                        <div class="info_label">Related Products</div>
                        <div class="content_wrapper_block mt-4">
                            <template v-if="pre_loader">
                                <div class="row">
                                    <div v-for="(n,i) in 4" class="col-md-3" :key="i">
                                        <CardViewProductPreLoader />
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <RelatedProductsBlock :product_id="data.id" />
                            </template>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="info_label">From the same company</div>
                    <div class="content_wrapper_block">
                        <template v-if="pre_loader">
                            <div v-for="(n,i) in 4" :key="i" class="mb-4">
                                <CardViewProductPreLoader />
                            </div>
                        </template>
                        <template v-else-if="data.company_info">
                            <CompaniesMoreProducts :company_id="data.company_info.id" />
                        </template>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</template>
<script>
import EmptyContentBlock from '~/components/content_display/EmptyContentBlock'
import RecommendedProducts from '@/pages/frontend/components/RecommendedProducts'
import HotProducts from '@/pages/frontend/components/HotProducts'
import CardViewProductPreLoader from '@/components/content_display/CardViewProductPreLoader'
import PhotoContentPreLoaderBlock from './components/PhotoContentPreLoader'
import BasicContentPreLoaderBlock from './components/BasicContentPreLoader'
import InfoContentPreLoaderBlock from './components/InfoContentPreLoader'
import RatingNReviewPreLoaderBlock from './components/RatingNReviewPreLoader'
import PhotoContentFullerBlock from './PhotoContentFullerBlock'
import PhotoContentZoomerBlock from './PhotoContentZoomerBlock'
import BasicContentBlock from './BasicContentBlock.v2'
import InfoContentBlock from './InfoContentBlock'
import InfoContentBlockWithTab from './InfoContentBlockWithTab.v2'
import InfoContentBlockWithSwiperTab from './InfoContentBlockWithSwiperTab.v2'
import CompaniesMoreProducts from './CompaniesMoreProducts'
import RatingNReviewsBlock from './RatingNReviewsBlock'
import RelatedProductsBlock from './RelatedProductsBlock'
export default {
    name: 'ProductDetailsBlock',
    components: {
        EmptyContentBlock,
        RecommendedProducts,
        HotProducts,
        PhotoContentPreLoaderBlock,
        BasicContentPreLoaderBlock,
        InfoContentPreLoaderBlock,
        RatingNReviewPreLoaderBlock,
        CardViewProductPreLoader,
        PhotoContentFullerBlock,
        PhotoContentZoomerBlock,
        BasicContentBlock,
        InfoContentBlock,
        InfoContentBlockWithTab,
        InfoContentBlockWithSwiperTab,
        CompaniesMoreProducts,
        RatingNReviewsBlock,
        RelatedProductsBlock
    },
    data(){
        return {
            pre_loader: true,
            data: '',
            invalid: false,
            product_type_photo_infos: [],
            tag_id_history: [],
            tag_id_history_limit: 15
        }
    },
    // activated() {
    //   // Call fetch again if last fetch more than 30 sec ago
    //   if (this.$fetchState.timestamp <= Date.now() - 30000) {
    //     this.$fetch()
    //   }
    // },
    // async asyncData({ $http, params, store }){
    //     // console.log(params)
    //     // console.log(store.state.oauth_token.cached)
    //     if(store.state.oauth_token.cached){
    //         let headerObj = {
    //             headers: {
    //                 'Authorization': store.state.oauth_token.data.token_type + ' ' + store.state.oauth_token.data.access_token,
    //                 'Content-Type': 'application/json',
    //                 'X-XSRF-TOKEN': store.state.oauth_token.data.access_token
    //             }
    //         }
    //         // console.log('Header Obj', headerObj)
    //         const data = await $http.$get('/api/products/' + params.slug, headerObj)

    //         if(data && data.data){
    //             // this.data = data.data
    //             // console.log('data', data.data)
    //             // return { product_info: data.data }
    //             store.commit('product/GET_PRODUCT_DTL_INFO', data.data)
    //             return { product_info: data.data }
    //         }
    //     }
    // },
    watch: {
        $route(to, from){
            this.data = ''
            this.load_req_data(to.params.slug)
            this.get_tag_id_history()
        }
    },
    computed: {
        cur_slug: function(){
            return this.$route.params.slug
        }
    },
    mounted(){
        // console.log('data', this.$store.state.product.product_dtl_info)
        if(this.$store.state.product.product_dtl_info) this.data = this.$store.state.product.product_dtl_info
        this.load_req_data(this.cur_slug)
        this.get_tag_id_history()
    },
    methods: {
        remove_same_history: async function(nv){
            if(this.tag_id_history.length>0){
                this.tag_id_history.forEach((v,i) => {
                    if(nv==v) this.tag_id_history.splice(i,1)
                });
            }
        },
        get_tag_id_history: function(){
            // if(localStorage.getItem('_visited_tag_id_history')) this.tag_id_history = JSON.parse(localStorage.getItem('_visited_tag_id_history'))
            console.log('Tag ids', typeof this.$cookiz.get('_visited_tag_id_history'))
            if(this.$cookiz.get('_visited_tag_id_history')){
                if(typeof this.$cookiz.get('_visited_tag_id_history') == 'object') this.tag_id_history = this.$cookiz.get('_visited_tag_id_history')
                else this.tag_id_history = JSON.parse(this.$cookiz.get('_visited_tag_id_history'))
            }
        },
        async set_req_data(){
            this.$parent.breadcrumb_data.pre_loader = false
            this.$parent.breadcrumb_data.cat_info = this.data.cat_info
            this.$parent.breadcrumb_data.product_title = this.data.product_title

            /**
             * Visited data keys stored
             */
            if(this.data.tag_info.length>0){
                await this.data.tag_info.forEach( async (v,i) => {
                    await this.remove_same_history(v.product_tag_id).then(() =>{
                        this.tag_id_history.unshift(v.product_tag_id)
                        if(this.tag_id_history.length > this.tag_id_history_limit) this.tag_id_history.pop()

                        if(this.data.tag_info.length==(i+1)){
                            // localStorage.setItem('_visited_tag_id_history', JSON.stringify(this.tag_id_history))
                            this.$cookiz.set('_visited_tag_id_history', JSON.stringify(this.tag_id_history))
                        }
                    })
                })
            }
        },
        async load_req_data(slug){
            this.pre_loader = true;
            this.$parent.breadcrumb_data = {
                pre_loader: true,
                cat_info: '',
                product_title: ''
            };

            if(this.data){
                console.log('Load server side')
                this.set_req_data()

                this.pre_loader = false
            } else {
                console.log('Load client side')
                this.$axios.get('/api/products/' + slug, this.$parent.header_config).then( async (response) => {
                    // console.log('Get Data', response.data)
                    if(response.data && response.data.data){
                        this.data = response.data.data
                        this.set_req_data()
                    }else{
                        this.$parent.breadcrumb_data.pre_loader = false
                        this.$parent.breadcrumb_data.product_title = 'not found'
                        this.invalid = true
                    }

                    this.pre_loader = false
                }).catch(e => {
                    console.log(e)
                    this.pre_loader = false;
                });
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    .info_label{
      display: block;
      padding: 0 15px;
      height: 50px;
      line-height: 50px;
      font-size: 16px;
      background-color: #fafafa;
      border-bottom: 1px solid #eee;
    }
    .content_wrapper_block{
      display: block; position: relative;
      background-color: #fff;
      /* padding: 15px;
      box-shadow: 0 0 25px #ddd; */
    }
    .content_wrapper_block_fuller{
        display: flex; position: relative; overflow: hidden; min-height: 520px;
        & > .basic_wrapper_block{
            /* position: absolute;
            min-width: 450px;
            top: 75px;
            right: 0;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none; border-right: none;
            border-radius: 0 0 0 15px;
            z-index: 5; */
            align-items: flex-start;
            margin-left: auto;
            padding-top: 75px;
            position: relative;
            width: 40%;
            z-index: 5;
        }
        &.mobile_device{
            flex-wrap: wrap;
            min-height: auto; overflow: visible;

            & > .basic_wrapper_block{
                margin-left: 0;
                width: 100%;
                padding-top: 20px;
            }
        }
    }    
</style>
