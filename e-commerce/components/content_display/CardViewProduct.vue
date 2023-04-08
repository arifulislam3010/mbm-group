<template>
    <div class="item_block">
        <nuxt-link :to="{path: $dtlProductUrl(item)}">
            <div class="img">
                <template v-if="item.product_photo_infos.length>0 && item.product_photo_infos[0].product_photo_data">
                    <clazy-load :src="item.product_photo_infos[0].product_photo_data.content">
                        <img :src="item.product_photo_infos[0].product_photo_data.content" :title="item.product_title" :alt="item.product_title">
                        <div class="preloader" slot="placeholder">
                            <img src="/_ipx/images/logo.png" title="Default Image" alt="Default Image" />
                        </div>
                    </clazy-load>
                </template>
                <template v-else>
                    <clazy-load :src="require('~/assets/images/empty-product.png')">
                        <img class="empty-img" :src="require('~/assets/images/empty-product.png')" :title="item.product_title" :alt="item.product_title">
                        <div class="preloader" slot="placeholder">
                            <img src="/_ipx/images/logo.png" title="Default Image" alt="Default Image" />
                        </div>
                    </clazy-load>
                </template>
            </div>
            <div class="info">
                <div class="top-block clearfix">
                    <!-- <span class="company_info float-left">
                        <img v-if="item.company_info && item.company_info.logo" :src="item.company_info.logo" :title="item.company_info.company_name" :alt="item.company_info.company_name" />
                        <i v-else class="fa fa-university"></i>
                    </span> -->
                    <!-- <div v-if="item.product_type_info" class="product_type_info float-left">
                        <span v-if="item.product_type_info.icon" class="icon">
                            <img :src="item.product_type_info.icon" :title="item.product_type_info.type_title" :alt="item.product_type_info.type_title" />
                        </span>
                        <span class="title">{{ item.product_type_info.type_title }}</span>
                    </div> -->
                    <span class="rating_info float-left">
                        <star-rating
                            :rating="0.0"
                            :read-only="true"
                            :animate="false"
                            :active-color="['#eeeeee','#f05a24']"
                            :active-border-color="['#ffffff','#f05a24']" :border-width="2"
                            :rounded-corners="false"
                            :show-rating="false"
                            :increment="0.1"
                            :star-size="10">
                        </star-rating>
                    </span>
                </div>
                <div class="title text_overflow max_two_lines_allow">{{ item.product_title }}</div>
                <!-- <div class="generic text_overflow max_two_lines_allow mb-3">
                    <template v-if="item.generic_info.length > 0">
                        <template v-for="(generic,gi) in item.generic_info">
                            <template v-if="gi>0"> + </template><template>{{ generic.generics_data_info.generic_name }}</template>
                        </template>
                    </template>
                    <template v-else>
                        <small>---</small>
                    </template>
                </div> -->
            </div>
            <div class="mb-2 clearfix">
                <!-- <div v-if="init_price_info.length > 0" class="price_info float-left"> -->
                <div v-if="item.product_price_infos.length > 0" class="price_info float-left">
                    <!-- <pre>{{ init_price_info }}</pre> -->
                    <!-- <pre>{{ item.product_price_infos[0] }}</pre> -->
                    <!-- <div :class="{'regular_price_view':init_price_info[0].discount_price}" v-if="init_price_info[0].price">
                        <PriceViewBlock :data="init_price_info[0].price" :discount="init_price_info[0].discount_price?true:false" />
                        <template v-if="init_price_info[0].discount_price">
                            <DiscountPercentageBlock :regular_price="init_price_info[0].price" :discount_price="init_price_info[0].discount_price" />
                        </template>
                    </div>
                    <div v-if="init_price_info[0].discount_price">
                        <PriceViewBlock :data="init_price_info[0].discount_price" />
                    </div> -->

                    <div v-if="item.product_price_infos[0].unit_price">
                        <PriceViewBlock :data="item.product_price_infos[0].unit_price" />
                    </div>

                    <!-- {{ init_price_info[0].quantity }} starting at <PriceViewBlock :data="init_price_info[0].price * init_price_info[0].quantity" :discount="init_price_info[0].discount_price?true:false" :resize="false" /> <template v-if="init_price_info[0].discount_price"><PriceViewBlock :data="init_price_info[0].discount_price * init_price_info[0].quantity" /></template> -->
                </div>
            </div>
        </nuxt-link>
        <!-- <div v-if="item.product_price_infos.length > 0" class="action_btn float-right">
            <span @click="add_to_cart(item)" :id="'item-' + item.id"><i class="fa fa-shopping-cart"></i> Add To Cart</span>
        </div> -->
    </div>
</template>
<script>
import PriceViewBlock from './price/ViewBlock'
import DiscountPercentageBlock from './price/DiscountPercentageBlock'
import { mapMutations } from 'vuex'
import $ from 'jquery'
export default {
    name: 'CardViewItemBlock',
    props: {
        item: Object
    },
    components: {
        PriceViewBlock,
        DiscountPercentageBlock
    },
    data(){
        return {
            base_url: process.env.BASE_URL,
            loading: false,
            loaded: false,
            init_price_info: []
        }
    },
    mounted(){
      if(this.item.product_price_infos.length > 0){
        this.init_price_info = JSON.parse(this.item.product_price_infos[0].price_info)
      }
    },
    methods: {
        ...mapMutations({
            add_cart_item: 'cart_info/ADD_CART_ITEMS'
        }),
        getImgPath: function(val){
            return val.replace(process.env.API_ENDPOINT,'')
        },
        check_qty_restrict: function(item){
            let min_qty = 1, quantity = 1
            if(item.min_qty) min_qty = item.min_qty

            return min_qty>quantity?min_qty:quantity
        },
        async add_to_cart(item){
            item.req_quantity = await this.check_qty_restrict(item.product_price_infos[0])

            await this.add_cart_item(item)

            let get_pos = $('#item-' + item.id).offset().top;
            console.log(get_pos)

            // this.$toast.success('Item added to cart', {icon: "Success"});
            this.$notify.success({
                title: 'Success',
                message: 'Item added to cart',
                timeout: 1500
            })
        }
    }
}
</script>
<style lang="scss" scoped>
    .item_block{
        display: block;
        position: relative;
        background-color: $white;
        color: $dark_ash;
        border: 1px solid $default_border_color;
        border-radius: 3px;
        box-shadow: 0 0 5px $ash;
        transition: all 0.4s;
    }
    .item_block:hover{
        box-shadow: 0 0 5px $dark_ash;
    }
    .item_block > a{
        display: block;
        color: $dark_ash;
    }
    .img{
        width: 100%;
        height: 150px;
        border-bottom: 1px solid $default_border_color;
    }
    .img .loaded,
    .img .loading,
    .img .preloader{
        width: 100%; height: 100%
    }
    .img .preloader{
        background-color: $light_white;
    }
    .img img{
        width: 100%; height: 100%; object-fit: cover; object-position: left;
    }
    .img .preloader img,
    .img .empty-img{
        padding: 25px;
        -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
        filter: grayscale(100%);
    }
    .info{
        display: block;
    }
    .info > .top-block{
        display: block;
        margin: 0 12px;
    }
    .info > .top-block .company_info{
        position: relative;
        width: 50px;
        height: 50px;
        text-align: center;
        border: 1px solid $default_border_color;
        background-color: $white;
        margin-top: -20px;
    }
    .info > .top-block .company_info > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .info > .top-block .company_info > i{
        line-height: 48px;
        font-size: 24px;
        color: #999
    }
    .info > .title{
        /* display: block; */
        margin: 8px 12px 0 12px;
        height: 40px;
        color: $title_color_1;
    }
    .info > .generic{
        /* display: block; */
        margin: 0 12px;
        height: 40px;
        font-size: 12px;
        color: $title_color_2;
        overflow: hidden;
    }
    .info > .generic > small{
        display: inline-block;
        line-height: 16px;
        height: 16px;
        overflow: hidden;
    }
    .info > .generic > span{
        display: inline-block;
        line-height: 10px; padding: 0 3px;
        position: relative;
        top: -5px;
    }
    .rating_info{
        // padding: 3px 10px 0;
        padding: 10px 0 0 0;
    }
    .product_type_info{
        /* display: block; */
        display: flex;
        padding: 0 10px;
        text-align: left;
        height: 30px;
        font-size: 12px;
        width: calc(100% - 52px);
    }
    .product_type_info > span{
        align-self: center;
        white-space: nowrap;
    }
    .product_type_info > .icon{
        display: inline-block;
        width: 20px; height: 20px;
        margin-right: 5px;
    }
    .product_type_info > .icon > img{
        display: inline-block;
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .image-lazy {
        opacity: 0;
    }
    .image-lazy-loading {
        transform: translateX(100vh);
    }
    .image-lazy-loaded {
        transition: opacity 1s ease;
        opacity: 1;
    }
    .price_info{
        padding: 0 12px;
        position: relative;
        .regular_price_view {
            position: absolute;
            top: -20px;
            white-space: nowrap;
        }
        & > :deep(.price_view_block){
            font-size: 18px;
        }
        // .discounted{
        //     font-size: 10px;
        // }
    }
    .action_btn{
        position: absolute;
        display: inline-block;
        bottom: 10px;
        right: 0px;
        background-color: $add_cart_btn_color;
        color: $white;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 5px;
        margin-right: 12px;
        pointer-events: auto;
        cursor: pointer;
        z-index: 5;
    }
    .action_btn:hover{
        background-color: $sys_brand_color
    }
    .action_btn > span{
        display: block;
    }
</style>
