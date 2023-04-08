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
                        <img v-if="item.company_info && item.company_info.logo" :src="item.company_info.logo" :title="item.company_info.company_name" />
                        <i v-else class="fa fa-institution"></i>
                    </span>
                    <div v-if="item.product_type_info" class="product_type_info text_overflow max_one_line_allow float-left">
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
                <div class="title text_overflow max_one_line_allow">{{ item.product_title }}</div>
                <!-- <div class="generic text_overflow max_two_lines_allow mb-3">
                    <template v-if="item.generic_info.length > 0">                
                        <template v-for="(generic,gi) in item.generic_info">
                            <template v-if="gi>0"> + </template><template>{{ generic.generics_data_info.generic_name }}</template>
                        </template>
                    </template>
                    <template v-else>
                        <small>---</small>
                    </template>
                </div>                 -->
            </div>
            <div class="mb-2 clearfix">
                <!-- <span v-if="item.product_price_infos.length > 0" class="price_info float-left">
                    <PriceViewBlock :data="item.product_price_infos[0].price" />
                </span> -->
                <div v-if="item.product_price_infos.length > 0" class="price_info float-left">
                    <div v-if="item.product_price_infos[0].unit_price">
                        <PriceViewBlock :data="item.product_price_infos[0].unit_price" />
                    </div>
                </div>
            </div>
        </nuxt-link>        
        <!-- <div v-if="item.product_price_infos.length > 0" class="action_btn float-right">
            <span :class="{active: $store.state.cart_info.cart_items[item.id]}" @click="add_to_cart(item)" :id="'item-' + item.id">
                <i v-if="$store.state.cart_info.cart_items[item.id]" class="fa fa-check"></i>
                <i v-else class="fa fa-plus"></i>                
            </span>
        </div> -->
    </div>
</template>
<script>
import PriceViewBlock from './price/ViewBlock'
import { mapMutations } from 'vuex'
import $ from 'jquery'
export default {
    name: 'CardViewItemBlock',
    props: {
        item: Object
    },
    components: {
        PriceViewBlock
    },
    methods: {
        ...mapMutations({
            add_cart_item: 'cart_info/ADD_CART_ITEMS'
        }),
        async add_to_cart(item){
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
    $img_height: 125;
    .item_block{
        display: block;
        position: relative;
        background-color: #ffffff;
        color: #666;        
        border: 1px solid #ddd;
        border-radius: 3px;
        box-shadow: 0 0 5px #ccc;
        transition: all 0.4s;
        &:hover{
            box-shadow: 0 0 5px #666;
        }
        & > a{
            color: #666;
        }
        .img{
            width: 100%;
            height: #{$img_height}px;
            border-bottom: 1px solid #ddd;
            overflow: hidden;
            img{
                // width: 100%; 
                // height: 100%; 
                height: #{$img_height}px;
                object-fit: contain;
            }
            .empty-img{
                padding: 25px
            }
        }
        .info{
            display: block;        
            & > .top-block{
                display: block;
                margin-top: 5px;
                & > .company_info{
                    position: relative;
                    width: 40px;
                    height: 40px;
                    border: 1px solid #ddd;
                    background-color: #fff;
                    margin-top: -15px;
                    & > img{
                        width: 100%;
                        height: 100%;
                        object-fit: contain;
                    }
                }
            }
            & > .title{
                /* display: block; */
                padding: 8px 12px 0 12px;
                color: #252d49;
            }
            & > .generic{
                /* display: block; */
                padding: 0 12px;
                height: 36px;
                font-size: 12px;
                color: #273c88;
                /* overflow: hidden; */
                & > small{
                    display: inline-block;
                    line-height: 16px;        
                    height: 16px;
                    overflow: hidden;
                }
                & > span{
                    display: inline-block;
                    line-height: 10px; padding: 0 3px;
                    position: relative;
                    top: -5px;
                }
            }
        }
    }
    .rating_info{
        padding: 3px 10px 0;
    }
    .product_type_info{
        /* display: block; */
        display: flex;
        padding: 0 10px;
        text-align: left;
        height: 25px;        
        font-size: 12px;
        width: calc(100% - 42px);        
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
    .price_info{
        padding: 0 12px;
    }
    .action_btn{
        position: absolute;
        display: inline-block;
        bottom: 10px;
        right: 0px;
        /* background-color: #666; */
        /* color: #fff; */
        font-size: 10px;
        /* padding: 3px 8px; */
        border-radius: 5px;
        margin-right: 12px;
        pointer-events: auto;
        cursor: pointer;
        z-index: 5;
    }    
    .action_btn > span{
        display: block;
        width: 30px; height: 30px;
        line-height: 30px;
        text-align: center;
        background-color: $sys_brand_color;
        color: #fff;
        border-radius: 50%;
    }
    .action_btn > span.active{
        background-color: rgb(0, 169, 199)
    }
    .action_btn > span:hover,
    .action_btn > span.active:hover{
        border-radius: 5px;
        background-color: $sys_brand_color
    }
</style>