<template>
    <div class="item_view_block">
        <template v-if="upload_product_image_popup">
            <div class="black_overlay">
                <div class="upload_product_image_view_block">
                    <template v-if="item.custom_product_design && item.custom_product_design.content">
                        <nuxt-img :src="item.custom_product_design.content" />
                    </template>
                    <nuxt-img v-else :src="item.upload_product_image" />
                    <i class="fa fa-times" @click="upload_product_image_popup=false"></i>
                </div>
            </div>            
        </template>

        <div>
            <div class="img">
                <nuxt-img v-if="item.product_photo_info && item.product_photo_info[0].product_photo_data"
                    format="webp"
                    :src="item.product_photo_info[0].product_photo_data.content"
                />
                <img v-else class="empty-img" src="~/assets/images/empty-product.png" />
            </div>
            <div class="img mt-2" v-if="item.upload_product_image" @click="upload_product_image_popup=true">
                <nuxt-img :src="item.upload_product_image" />
                <div class="shade"></div>
                <i class="fa fa-expand"></i>
            </div>
            <template v-else-if="item.custom_product_design && item.custom_product_design.content">
                <div class="img mt-2" @click="upload_product_image_popup=true">
                    <nuxt-img :src="item.custom_product_design.content" />
                    <div class="shade"></div>
                    <i class="fa fa-expand"></i>
                </div> 
            </template>
        </div>
        <div class="info">                    
            <div class="title text_overflow max_one_line_allow">
                {{ item.product_title }}
            </div>
            <div class="price_unit_type">{{ item.product_price_type }}</div>
            <div class="quantity">
                <!-- <template v-if="$parent.$parent.edit_receipt">
                    <QuantityActionBlock :quantity="quantity" />
                </template> -->
                <template>{{ item.qty }}</template> x <PriceViewBlock :data="item.price" />
                <template>=</template>
                <PriceViewBlock :data="total_price" />
                <template v-if="$parent.$parent.edit_receipt && $parent.data.length>1">
                    <span class="ml-2" @click="removeItem"><i class="fa fa-trash-alt"></i></span>
                </template>
            </div>
            <hr />
            <div class="product_type_list">
                <div v-for="(ptiv,ptii) in item.product_type_infos" :key="ptii">
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
                <div v-if="item.custom_product_design_price">
                    <span>Design purpose price:</span>
                    <span><em><PriceViewBlock :data="item.custom_product_design_price" /></em></span>
                </div>
            </div>
        </div>
        <!-- <div class="price">
            <PriceViewBlock :data="total_price" />
        </div> -->
    </div>
</template>
<script>
import QuantityActionBlock from '@/components/content_display/QuantityActionBlock'
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import { mapMutations } from 'vuex'
export default {
    name: 'ItemViewBlock',
    props: ['item','index'],
    components: {
        QuantityActionBlock,
        PriceViewBlock
    },    
    data(){
        return {
            min_qty: 1,
            max_qty: 100,
            exist_qty: this.item.qty,
            quantity: this.item.qty,
            upload_product_image_popup: false
        }
    },
    computed: {
        total_price: function(){
            return (this.item.price * this.item.qty).toFixed(2)
        }
    },
    methods: {
        ...mapMutations({
            selOrderData: 'order_info/SEL_DATA',
            reconfigOrderData: 'order_info/RECONFIG_DATA'
        }),
        removeItem: async function(){
            let obj = {                
                'index': this.index,
                'remove': true
            }
            await this.reconfigOrderData(obj)
        },
        quantity_submit: async function(val){
            if(val<this.min_qty || isNaN(val)) this.quantity = this.min_qty
            else if(this.max_qty && val>this.max_qty) this.quantity = this.max_qty
            else this.quantity = val

            // let obj = Object.assign({}, this.$store.state.order_info.sel_data);
            // obj.order_items_info[this.index].qty = 1            
            let obj = {
                'field': 'order_items_info',
                'index': this.index,
                'qty': this.quantity
            }
            await this.reconfigOrderData(obj)
            // this.selOrderData(obj)                        

            // let total_amount = this.item.price * this.quantity
            // let total_payable = this.item.price * this.quantity
            // this.$parent.$parent.data.order_items_info[this.index].qty = this.quantity
            // this.$parent.$parent.data.total_amount = total_amount
            // this.$parent.$parent.data.total_payable = total_payable
        }
    }
}
</script>
<style lang="scss" scoped>   
    .upload_product_image_view_block{
        position: relative;        
        left: 10%; margin: 25px 0;
        width: 80%; height: auto;
        z-index: 1;
        
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
            right: -5px;
            top: -15px;
            color: #CD0000;
            transition: all 0.4s;
            cursor: pointer;

            &:hover{
            background-color: #CD0000; border-color: #CD0000; color: #fff
            }
        }
    }
    .item_view_block{
        display: flex;
        gap: 10px;
         
        .img{
            position: relative;
            width: 90px;
            height: 60px;
            // margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background-color: #eee;
            img{
                width: 100%;
                height: 100%;
                object-fit: contain;
                &.empty-img{
                    padding: 5px
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
                font-size: 20px;
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
            margin-right: 10px;
            & > .title{
                font-size: 13px;
            }
            & > .price_unit_type{
                font-size: 11px;
            }
            & > .quantity{
                margin-top: 3px;
                font-size: 12px;
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
        .price{
            margin-left: auto;
            font-size: 13px;        
        }
    }
</style>