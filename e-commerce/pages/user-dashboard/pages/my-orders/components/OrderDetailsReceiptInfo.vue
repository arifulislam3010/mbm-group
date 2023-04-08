<template>
    <div class="order_details_receipt_info_block">
        <template v-if="upload_product_image_popup">
            <div class="black_overlay">
                <div class="upload_product_image_view_block">                    
                    <nuxt-img :src="get_upload_product_image" />
                    <i class="fa fa-times" @click="get_upload_product_image='';upload_product_image_popup=false"></i>
                </div>
            </div>            
        </template>
        <template v-for="(item,id) in data">
            <div v-if="item.id" :id="'item-' + id" :key="'ci-' + id" class="item">
                <div>
                    <div class="img">
                        <nuxt-img v-if="item.product_photo_info && item.product_photo_info[0] && item.product_photo_info[0].product_photo_data"
                            format="webp"
                            :src="(item.product_photo_info[0].product_photo_data.exist_content?'':$store.state.media_gallery_img_path + '/') + item.product_photo_info[0].product_photo_data.content"
                        />
                        <img v-else class="empty-img" src="~/assets/images/empty-product.png" />
                    </div>
                    <div class="img mt-2" v-if="item.upload_product_image" @click="get_upload_product_image=item.upload_product_image;upload_product_image_popup=true">
                        <nuxt-img :src="item.upload_product_image" />
                        <div class="shade"></div>
                        <i class="fa fa-expand"></i>
                    </div>
                    <template v-else-if="item.custom_product_design && item.custom_product_design.content">
                        <div class="img mt-2" @click="get_upload_product_image=item.custom_product_design.content;upload_product_image_popup=true">
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
                    <div class="quantity">{{ item.qty }} x <PriceViewBlock :data="item.price" /></div>
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
                <div class="price">
                    <PriceViewBlock :data="(item.price * item.qty).toFixed(2)" />
                </div>                
            </div>
        </template>        
    </div>
</template>
<script>
import PriceViewBlock from '~/components/content_display/price/ViewBlock'
export default {
    name: 'OrderDetailsReceiptInfoBlock',
    props: ['data'],
    components: {
        PriceViewBlock
    },
    data(){
        return {
            get_upload_product_image: '',
            upload_product_image_popup: false
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
    .item{
        display: flex;
        margin-bottom: 12px;
        &:last-child{
            margin-bottom: 5px;
        }
        .img{
            position: relative;
            width: 60px;
            height: 40px;
            margin-right: 10px;
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
                left: 42%;
                top: 12px;
                opacity: 0;
                z-index: 5;
                transition: all 0.4s;
                color: #fff;
                font-size: 14px;
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
        & > .info{
            margin-right: 10px;
            & > .title{
                font-size: 13px;
            }
            & > .quantity{
                margin-top: 3px;
                font-size: 12px;
            }
            & > .price_unit_type{
                font-size: 11px;
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
        & > .price{
            margin-left: auto;
            font-size: 13px;        
        }
    }
</style>