<template>
    <div class="content_list_view">
        <template v-if="content_loader">
            <div class="form_block">
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </template>
        <template v-else-if="data.length>0">
            <div v-for="(item,index) in data" :key="index" class="list_item">
                <div class="head_info">
                    <div class="order_info">
                        <div class="order_id">ID #{{ item.order_id }}</div>
                        <div class="ordered_at"><small>Placed on {{ item.created_at }}</small></div>
                    </div>
                    <div class="action_btn">
                        <span @click="$parent.view_details_order(item)"><i class="fa fa-external-link"></i> View Detail</span>
                    </div>
                </div>
                <div class="item_info">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="text-align:right" width="50">Sl.</th>
                                <th width="100">Photo</th>
                                <th>Product Info</th>                                
                                <th style="text-align:center">Qty</th>
                                <th style="text-align:center" width="80">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(product,pi) in item.order_items_info">
                                <tr :key="'product-' + pi">
                                    <td align="right">{{ (pi + 1) }}</td>
                                    <td>
                                        <div class="img">
                                            <img v-if="product.product_photo_info && product.product_photo_info[0]" :src="product.product_photo_info[0].product_photo_data.content" :title="product.product_title" />
                                            <i v-else class="fa fa-image"></i>
                                        </div>
                                    </td>
                                    <td><div v-html="product.product_title"></div></td>
                                    <td align="center">{{ product.qty }}</td>
                                    <td v-if="pi==0" :rowspan="item.order_items_info.length" style="vertical-align: middle; background-color: #fff" nowrap>
                                        <div v-if="item.status=='0'" class="status_list">
                                            <i class="far fa-check-circle"></i>
                                            <span>{{ $store.state.order_init_status_label }}</span>
                                        </div>
                                    </td>
                                </tr>                                
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <template v-else>
            <EmptyCartBlock />
        </template>
    </div>
</template>
<script>
import EmptyCartBlock from '~/components/content_display/EmptyCartBlock'
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    components: {
        EmptyCartBlock
    },
    data(){
        return {
        }
    },
    computed: {
        cur_path: function(){
            return this.$route.fullPath
        }
    },
    methods: {        
    }
}
</script>
<style scoped>
    .list_item{
        display: block;
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 3px;
        /* box-shadow: 0 0 5px #ccc */
    }
    .head_info{
        display: flex;
        padding: 5px 15px;
        min-height: 50px;
        background-color: #eee;
    }    
    .head_info > div.action_btn{
        align-self: center;
        margin-left: auto;
    }
    .head_info > div.action_btn > span{
        display: inline-block;
        border: 1px solid #ddd;
        background-color: #ccc;
        font-size: 12px; color: #333;
        padding: 1px 15px; border-radius: 25px;
        cursor: pointer;
    }
    .head_info > div.order_info{
        align-self: center;
    }
    .item_info{
        display: block;
        padding:  0 15px;
        margin-top: 15px;
    }
    .item_info .img{
        display: inline-block;
        width: 90px;
        height: 60px;
        background-color: #f7f7f7;
        border: 1px solid #ddd
    }
    .item_info .img > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .status_list{
        font-size: 12px;        
    }
</style>