<template>
    <div class="basic_content_block">
        <!-- <hr class="mt-2 mb-2" /> -->
        <div class="category_info mb-1"><small>
            <template v-for="(item,index) in data.cat_info">
              <template v-if="item.cat_data_info && item.cat_data_info.category_name">
                <span :key="'cat-title-' + index" class="cat_title" @click="goto_search('category',item.cat_data_info.slug)">{{ item.cat_data_info.category_name }}</span><span v-if="data.cat_info[index + 1]" :key="'cat-sep-' + index" class="mr-1">,</span>
              </template>
            </template>
        </small></div>
        <h1 class="product_title">{{ data.product_title }}</h1>
        <h6 class="generic_infos">
            <template v-for="(item,gi) in data.generic_info">
                <template v-if="gi>0"> + </template>
                <span @click="goto_search('keyword',item.generics_data_info.generic_name)" :key="gi">{{ item.generics_data_info.generic_name }}</span>
            </template>
        </h6>
        <div v-if="data.product_type_info" class="product_type_info">
            <div @click="goto_search('product-type',data.product_type_info.slug)">
                <span v-if="data.product_type_info.icon" class="icon"><img :src="data.product_type_info.icon" :title="data.product_type_info.type_title" :alt="data.product_type_info.type_title" /></span>
                <span class="title">{{ data.product_type_info.type_title }}</span>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-8">
                <div class="company_info" @click="goto_search('company',data.company_info.slug)">
                    <span class="logo"><img :src="data.company_info.logo" /></span>
                    <span class="name">{{ data.company_info.company_name }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="product_type_info">
                    <span class="icon"><img :src="data.product_type_info.icon" /></span>
                    <span class="title">{{ data.product_type_info.type_title }}</span>
                </div>
            </div>
        </div> -->
        <hr />
        <div class="row">
            <div class="col-7">
                <div v-if="data.company_info" class="company_info" @click="goto_search('company',data.company_info.slug)">
                    <span class="logo"><img :src="data.company_info.logo" :title="data.company_info.company_name" :alt="data.company_info.company_name" /></span>
                    <span class="name">{{ data.company_info.company_name }}</span>
                </div>
                <!-- <star-rating
                    :rating="3.5"
                    :read-only="true"
                    :animate="false"
                    :active-color="['#eeeeee','#f05a24']"
                    :active-border-color="['#ffffff','#f05a24']" :border-width="2"
                    :rounded-corners="false"
                    :show-rating="true"
                    :increment="0.1"
                    :star-size="10">
                </star-rating> -->
            </div>
            <div class="col-5" align="right">
                <div class="social-links">
                    <span @click="social_share('fb')"><i class="fab fa-facebook"></i></span>
                    <span @click="social_share('twitter')"><i class="fab fa-twitter"></i></span>
                    <!-- <span><i class="fa fa-heart"></i></span> -->
                </div>
            </div>
        </div>
        <hr />
        <div v-if="data.product_price_infos" class="row">
            <div class="col-md-12">
                <div class="price_type_list">
                    <label>Price Type</label>
                    <template v-for="(item,index) in data.product_price_infos">
                        <span :class="[{active:sel_price_type_index==index},{'ml-2':index>0}]" :key="'ppt-' + index" @click="sel_price_type_index=index">{{ item.product_price_type_data.type_title }}<i v-if="sel_price_type_index==index" :key="'a-ppt-' + index" class="fa fa-check"></i></span>
                    </template>
                </div>
            </div>
            <div v-if="data.product_price_infos && data.product_price_infos[sel_price_type_index] && data.product_price_infos[sel_price_type_index].remarks" class="col-md-12">
                <div class="remark_info mt-3">
                    <label>Remarks</label>
                    <p>{{ data.product_price_infos[sel_price_type_index].remarks }}</p>
                </div>
            </div>
            <div v-if="data.product_price_infos && data.product_price_infos[sel_price_type_index]" class="col-5">
                <div class="price_info mt-3">
                    <label>Price</label>
                    <div v-if="data.product_price_infos[sel_price_type_index].discount_price">
                        <PriceViewBlock :data="data.product_price_infos[sel_price_type_index].discount_price" />
                    </div>
                    <div v-if="data.product_price_infos[sel_price_type_index].price">
                        <PriceViewBlock :data="data.product_price_infos[sel_price_type_index].price" :discount="data.product_price_infos[sel_price_type_index].discount_price?true:false" />

                        <template v-if="data.product_price_infos[sel_price_type_index].discount_price">
                            <DiscountPercentageBlock :regular_price="data.product_price_infos[sel_price_type_index].price" :discount_price="data.product_price_infos[sel_price_type_index].discount_price" />
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="quantity_info mt-3">
                    <label>Quantity</label>
                    <QuantityActionBlock :quantity="quantity" />
                    <div class="mt-2" v-if="quantity>1">
                        <span v-if="$store.state.sel_lang=='bn'">মোট </span>
                        <span v-else>Total</span>
                        <template v-if="data.product_price_infos[sel_price_type_index].discount_price">
                            <PriceViewBlock :data="(data.product_price_infos[sel_price_type_index].discount_price * quantity).toFixed(2)" :lang="$store.state.sel_lang" />
                        </template>
                        <template v-else>
                            <PriceViewBlock :data="(data.product_price_infos[sel_price_type_index].price * quantity).toFixed(2)" :lang="$store.state.sel_lang" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mt-4 pr-2">
                <span class="action_btn" @click="buy_now(data)"><i class="fa fa-dollar-sign"></i> Buy Now</span>
            </div>
            <div class="col-6 mt-4 pl-2">
                <span class="action_btn cart" @click="add_to_cart(data)"><i class="fa fa-shopping-cart"></i> Add to Cart</span>
            </div>
        </div>
    </div>
</template>
<script>
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import DiscountPercentageBlock from '@/components/content_display/price/DiscountPercentageBlock'
import QuantityActionBlock from '@/components/content_display/QuantityActionBlock'
import { mapMutations } from 'vuex'
import $ from 'jquery'
export default {
    name: 'BasicContentBlock',
    props: ['data'],
    components: {
        PriceViewBlock,
        DiscountPercentageBlock,
        QuantityActionBlock
    },
    data(){
        return {
            sel_price_type_index: 0,
            min_qty: 1,
            max_qty: 100,
            quantity: 1
        }
    },
    watch: {
        sel_price_type_index: function(val){
            this.min_qty = 1
            this.max_qty = ''
            this.quantity = 1
            this.check_qty_restrict()
        }
    },
    mounted() {
        this.check_qty_restrict()
    },
    methods: {
        ...mapMutations({
            add_cart_item: 'cart_info/ADD_CART_ITEMS'
        }),
        goto_search: function(keyword,value){
            this.$router.push('/'+ this.$store.state.search_page_path +'?'+ keyword +'=' + value)
        },
        quantity_submit: function(val){
            if(val<this.min_qty || isNaN(val)) this.quantity = this.min_qty
            else if(this.max_qty && val>this.max_qty) this.quantity = this.max_qty
            else this.quantity = val
        },
        buy_now: function(item){
            this.add_to_cart(item)
            this.$router.push('/'+ this.$store.state.checkout_page_path +'?time=' + Date.now())
        },
        async add_to_cart(item){
            item.req_quantity = this.quantity
            item.sel_price_type_index = this.sel_price_type_index
            // if(item.product_price_infos[this.sel_price_type_index].discount_price) item.product_price_infos[this.sel_price_type_index].price = item.product_price_infos[this.sel_price_type_index].discount_price
            // alert(this.sel_price_type_index)
            console.log('Cart Item', item)

            await this.add_cart_item(item)

            // let get_pos = $('#item-' + item.id).offset().top;
            // console.log(get_pos)

            // this.$toast.success('Item added to cart', {icon: "Success"});
            this.$notify.success({
                title: 'Success',
                message: 'Item added to cart',
                timeout: 1500
            })
        },
        check_qty_restrict: function(){
            if(this.data.product_price_infos && this.data.product_price_infos[this.sel_price_type_index] && this.data.product_price_infos[this.sel_price_type_index].min_qty) this.min_qty = this.data.product_price_infos[this.sel_price_type_index].min_qty
            if(this.data.product_price_infos && this.data.product_price_infos[this.sel_price_type_index] && this.data.product_price_infos[this.sel_price_type_index].max_qty) this.max_qty = this.data.product_price_infos[this.sel_price_type_index].max_qty

            this.quantity = this.min_qty>this.quantity?this.min_qty:this.quantity
        },
        social_share: function(type){
            let url = ''
            if(type=='fb'){
                url = 'https://www.facebook.com/sharer.php?t=' + encodeURIComponent(this.data.product_title) + '&u=' + encodeURIComponent(process.env.BASE_URL + this.$route.fullPath)
                // alert(url)
            }else if(type=='twitter'){
                url = 'http://twitter.com/share?text='+ encodeURIComponent(this.data.product_title) +'&url='+ encodeURIComponent(process.env.BASE_URL + this.$route.fullPath) + (this.data.tag_info.length>0?'&hashtags=' + this.data.tag_info[0].tag_title:'')
            }

            window.open (url,'','width=500, height=500, scrollbars=yes, resizable=no');
        }
    }
}
</script>
<style lang="scss" scoped>
    .category_info .cat_title:hover{
        color: purple;
        cursor: pointer
    }
    .product_title{
        display: block;
        font-size: 18px;
        color: #333;
    }
    .generic_infos{
        font-size: 12px;
        color: #273c88;
        transition: all 0.4s;
        :deep(span:hover){
            color: purple;
            cursor: pointer
        }
    }
    .social-links > span{
        display: inline-block;
        padding: 0 3px; color: #999;
        transition: all 0.4s;
        &:hover{
            color: #273c88
        }
    }
    .product_type_info{
        display: block; text-align: left; height: 20px;
        line-height: 20px; font-size: 11px;
        & > div,
        & > div > span{
            display: inline-block;
        }
        & > div > .icon{
            width: 20px; height: 20px;
            & > img{
                display: inline-block;
                width: 100%; height: 100%;
                object-fit: contain;
            }
        }
        &:hover > div > .title{
            color: purple;
            cursor: pointer
        }
    }
    .company_info{
        display: inline-block; height: 20px;
        line-height: 20px; font-size: 11px;
        transition: all 0.4s;
        & > .logo{
            display: inline-block;
            width: 20px; height: 20px;
            & > img{
                display: inline-block;
                width: 100%; height: 100%;
                object-fit: contain;
            }
        }
        &:hover > .name{
            color: purple;
            cursor: pointer
        }
    }    
    .price_type_list > label,
    .remark_info > label,
    .price_info > label,
    .quantity_info > label{
        display: block; font-size: 12px; margin-bottom: 5px;
    }
    .price_type_list{
        display: block;
        & > span{
            display: inline-block; position: relative; background-color: #fff; font-size: 10px; height: 30px; line-height: 30px; white-space: nowrap; padding: 0 10px; text-align: center; border: 1px solid #ddd; border-radius: 3px; cursor: pointer;
            &.active{
                border-color: #CD0000;
                & > i{
                    position: absolute; margin: 0 -1px -1px 0; padding: 2px 2px 2px 2px; border-radius: 3px 0 3px 0; font-size: 7px;
                    bottom:0; right: 0; background-color: #CD000099; color: #fff;
                }
            }
        }
    }
    .remark_info > p{
        background-image: linear-gradient(to right, #f7f7f7, transparent); padding: 8px 12px; font-size: 12px;
    }
    .price_info{
        :deep(.price_view_block){
            font-size: 34px;
            line-height: 34px;
            color: #e5733a;
            &.discounted{
                font-size: 16px;
                line-height: 16px;
                color: #999;
                text-decoration: line-through;
                /* margin-left: 5px */
            }
        }
        :deep(.discount_percentage_block) span{
            font-size: 16px;
            line-height: 16px;
        }
    }
    .action_btn{
        display: block;
        height: 36px; line-height: 36px; text-align: center;
        background-color: #e5733a; color: #fff;
        cursor: pointer; border-radius: 3px; transition: all 0.4s;
        &.cart{
            background-color: #006699;
            & > i{
                background-color: #0088cc;
            }
        }
        & > i{
            display: inline-block; position: relative;
            background-color: #ff7f40; font-size: 10px;
            width: 24px; height: 24px; margin-right: 5px;
            line-height: 24px; border-radius: 50%;
        }
    }    
</style>
