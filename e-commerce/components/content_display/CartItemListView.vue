<template>
    <div class="cart_item_list_view">
        <template v-if="upload_product_image_popup">
            <div class="black_overlay">
                <div class="upload_product_image_view_block">                    
                    <nuxt-img :src="get_upload_product_image" />
                    <i class="fa fa-times" @click="get_upload_product_image='';upload_product_image_popup=false"></i>
                </div>
            </div>            
        </template>
        <template v-if="$store.state.cart_info.total_cart_items>0">
            <template v-for="(list,id) in $store.state.cart_info.cart_items">
                <template v-for="(pt_item,tid) in list">
                    <template v-for="(item,pid) in pt_item">
                        <div v-if="item.product_title" :id="'item-' + id + '-' + tid + '-' + pid" :key="'ci-' + id + '-' + tid + '-' + pid" class="item">
                            <div>
                                <div class="img" @click="get_upload_product_image=item.product_image?item.product_image:'';get_upload_product_image?upload_product_image_popup=true:upload_product_image_popup=false">
                                    <template v-if="item.product_image">
                                        <clazy-load :src="item.product_image">
                                            <img :src="item.product_image" :title="item.product_title" :alt="item.product_title">
                                            <div class="preloader" slot="placeholder">
                                                <img src="/_ipx/images/logo.png" title="Preloader image" alt="Preloader image" />
                                            </div>
                                        </clazy-load>
                                    </template>
                                    <template v-else>
                                        <clazy-load src="/_ipx/images/empty-product.png">
                                            <img class="empty-img" src="/_ipx/images/empty-product.png" title="Empty Product" alt="Empty Product">
                                            <div class="preloader" slot="placeholder">
                                                <img src="/_ipx/images/logo.png" title="Preloader image" alt="Preloader image"/>
                                            </div>
                                        </clazy-load>
                                    </template>
                                    <div class="shade"></div>
                                    <i class="fa fa-expand"></i>
                                </div>
                                <template v-if="item.custom_product_design && (item.custom_product_design.design_id || item.custom_product_design.upload_file)">
                                    <div class="img mt-2" @click="get_upload_product_image=item.custom_product_design.design_url?item.custom_product_design.design_url:item.custom_product_design.upload_file;upload_product_image_popup=true">
                                        <clazy-load :src="item.custom_product_design.design_url?item.custom_product_design.design_url:item.custom_product_design.upload_file">
                                            <img :src="item.custom_product_design.design_url?item.custom_product_design.design_url:item.custom_product_design.upload_file" :title="item.product_title" :alt="item.product_title">
                                            <div class="preloader" slot="placeholder">
                                                <img src="/_ipx/images/logo.png" title="Preloader image" alt="Preloader image" />
                                            </div>
                                        </clazy-load>
                                        <div class="shade"></div>
                                        <i class="fa fa-expand"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="info">
                                <div class="top_section">
                                    <div class="product_info">
                                        <div class="title text_overflow max_one_line_allow">
                                            <nuxt-link :to="{path: '/'+ $store.state.product_dtl_path + '/' + item.product_slug}">{{ item.product_title }}</nuxt-link>                                    
                                        </div>
                                        <div class="price_type">{{ item.price_type }}</div>
                                    </div>
                                    <div class="del_btn" @click="removeCartItem(id,tid,pid)"><i class="fa fa-trash"></i></div>
                                </div>
                                <div class="bottom_section">
                                    <PriceUpdateBlock :product_id="id" :price_type_id="tid" :item="item" />                                    
                                </div>
                                <hr />
                                <div class="product_type_list">
                                    <div v-for="(ptiv,ptii) in item.product_type_info_values" :key="ptii">
                                        <template v-if="ptii=='color'">
                                            <span>{{ ptiv.title }}:</span>
                                            <span>
                                                <b :style="{'background-color': ptiv.values.color_code}"></b>
                                                <em>{{ ptiv.values.color_title }}</em>
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span>{{ ptiv.title }}:</span>
                                            <span><em>{{ ptiv.values }}</em></span>
                                        </template>
                                    </div>
                                    <template v-if="item.custom_product_design && item.custom_product_design.price">
                                        <div>
                                            <span>Design purpose price:</span>
                                            <span><em><PriceViewBlock :data="item.custom_product_design.price" /></em></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </template>
        </template>
        <template v-else>
            <EmptyCartBlock />
        </template>
    </div>
</template>
<script>
import PriceViewBlock from './price/ViewBlock'
import PriceUpdateBlock from './price/PriceUpdateBlock'
import EmptyCartBlock from './EmptyCartBlock'
import { mapMutations } from 'vuex'
import $ from 'jquery'
export default {
    name: 'CatItemListViewBlock',
    components: {
        PriceViewBlock,
        PriceUpdateBlock,
        EmptyCartBlock
    },
    data(){
        return {
            get_upload_product_image: '',
            upload_product_image_popup: false
        }
    },
    mounted(){
        // get total cart amount
        this.total_cart_amount()
    },
    methods: {
        ...mapMutations({
            update_cart_item_quantity: 'cart_info/UPDATE_CART_ITEMS_QUANTITY',
            remove_cart_item: 'cart_info/REMOVE_CART_ITEM',
            total_cart_amount: 'cart_info/TOTAL_CART_AMOUNT'
        }),
        async removeCartItem(id,tid,pid){
            let obj = {
                id: id,
                ptid: tid,
                ptvid: pid
            }
            await this.remove_cart_item(obj)
            
            this.total_cart_amount()            
            
            $('#item-' + id + '-' + tid).remove();

            // this.$toast.success('Item removed from cart', {icon: "Success"});
            this.$notify.success({
                title: 'Success',
                message: 'Item removed from cart',
                position: 'bottomLeft',
                transitionIn: 'bounceInRight',
                transitionOut: 'fadeOutLeft',
                timeout: 1500
            })
        }
    }
}
</script>
<style lang="scss" scoped>
    .upload_product_image_view_block{
        position: relative;
        left: 10%;
        margin: 25px 0;
        width: 80%;
        height: auto;
        z-index: 1;
        text-align: center;
        background-color: #fff;
        box-shadow: 0 0 15px #000;
        
        & > img{
            background-color: #fff; padding: 10px;
            max-width: calc(100% - 10px); max-height: calc(100% - 10px);
            object-fit: contain;
        }     
        
        & > i{
            position: absolute;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background-color: #fff;
            border-radius: 50%;
            border: 1px solid #ccc;
            z-index: 1;
            right: -10px;
            top: -10px;
            color: #CD0000;
            transition: all 0.4s;
            cursor: pointer;

            &:hover{
                background-color: #CD0000; border-color: #CD0000; color: #fff
            }
        }
    }
    .cart_item_list_view > .item{
        display: flex;
        width: 100%;
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        .img{
            position: relative;
            width: 60px;
            height: 50px;
            /* background-color: #eee; */
            border: 1px solid #ddd;
            .loaded,.loading,.preloader{
                width: 100%; height: 100%;
                &.preloader{
                    background-color: #f5f5f5;
                    img{
                        padding: 5px;
                        -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
                        filter: grayscale(100%);
                    }
                }
            }

            img{
                width: 100%; height: 100%; object-fit: contain;
                .empty-img{
                    padding: 5px;
                    -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
                    filter: grayscale(100%);
                }
            }

            .shade{                
                position: absolute;
                display: none;
                width: 100%; height: 100%;
                left: 0; top: 0;
                background-color: #00000080;                
                z-index: 1;
                transition: all 0.4s;
                cursor: pointer;
            }
            i{
                position: absolute;
                left: 40%;                
                top: 20px;                
                opacity: 0;
                z-index: 5;
                transition: all 0.4s;
                color: #fff;
                font-size: 10px;
            }

            &:hover{
                .shade{
                    display: block;
                }
                i{
                    opacity: 1.0;
                }
            }
        }
    
        .info{    
            align-self: center;
            margin-left: 10px;
            width: calc(100% - 70px);
            // height: 50px;
            height: auto;
            & > .top_section{
                display: flex;
                align-self: center;
                // height: 30px;
                height: auto;
                & > .product_info{
                    align-self: center;

                    & > .title,
                    & > .title a{
                        text-align: left;
                        font-size: 12px;
                        color: rgb(40, 50, 104);
                        font-weight: bold;
                    }

                    & > .price_type{
                        font-size: 11px; color: #666; text-align: left;
                    }                    
                }    
                & > .del_btn{
                    margin-left: auto;
                    align-self: center;
                    cursor: pointer;
                    color: #666;
                    font-size: 12px;
                    transition: all 0.4s;
                    &:hover{
                        color: $sys_brand_color;
                    }
                }
            }
    
            & > .bottom_section{        
                align-self: center;
                height: 25px;
            }

            & > hr{
                margin: 5px 0;
            }

            & > .product_type_list > div{
                display: flex;
                gap: 5px;
                font-size: 11px;
                color: #444;

                & > span{
                    display: flex;
                    align-items: center;
                    gap: 4px;

                    & > b{
                        display: inline-block;
                        width: 14px;
                        height: 14px;
                        border-radius: 50%;
                        border: 1px solid #ccc;
                    }
                }

                & > span:first-child{
                    color: #666;
                    white-space: nowrap;
                }
            }
        }
    }
</style>