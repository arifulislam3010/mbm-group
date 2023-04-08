<template>
    <div class="order_details_receipt_info_block">
        <template v-for="(item,id) in data">
            <div :id="'item-' + id" :key="'ci-' + id" class="item">
                <div class="img">
                    <nuxt-img v-if="item.product_photo_info && item.product_photo_info[0].product_photo_data"
                        format="webp"
                        :src="item.product_photo_info[0].product_photo_data.content"
                    />
                    <img v-else class="empty-img" src="~/assets/images/empty-product.png" />
                </div>
                <div class="info">                    
                    <div class="title text_overflow max_one_line_allow">
                        {{ item.product_title }}
                    </div>
                    <div class="quantity">{{ item.qty }} x <PriceViewBlock :data="item.price" /></div>
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
    }
}
</script>
<style scoped>
    .item{
        display: flex;
        margin-bottom: 12px;
    }
    .item:last-child{
        margin-bottom: 5px;
    }
    .item > .img{
        width: 60px;
        height: 40px;
        margin-right: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #eee;
    }
    .item > .img img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .item > .img img.empty-img{
        padding: 5px
    }
    .item > .info{
        margin-right: 10px;
    }
    .item > .info > .title{
        font-size: 13px;
    }
    .item > .info > .quantity{
        margin-top: 3px;
        font-size: 12px;
    }
    .item > .price{
        margin-left: auto;
        font-size: 13px;        
    }
</style>