<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <template v-else-if="data.length>0">
            <div class="head_section">
                <div>Order Review list</div>
                <div>Total: {{ data.length }}</div>
            </div>
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <div class="list">                        
                        <div>                            
                            <div class="order_info">
                                <div class="order_id">ID #{{ item.order_id }}</div>
                                <div class="ordered_at"><small>Placed on {{ item.created_at }}</small></div>
                            </div>
                        </div>
                        <hr />
                        <div align="center">
                            <StarRating :cur_rating="item.delivery_person_info.rating_points" :len="$store.state.rating_out_off" :size="'medium'" />
                            <div><small>{{ item.delivery_person_info.rating_points }} / {{ $store.state.rating_out_off }}</small></div>
                        </div>
                        <hr />                        
                        <div align="center" class="bottom_block">
                            <div class="action_btn">
                                <span @click="$parent.view_details_order(item)"><i class="fa fa-external-link"></i> View Detail</span>
                                <div class="total_items"><small>Total {{ item.order_items_info.length }} item{{ item.order_items_info.length>1?'s':'' }}</small></div>
                            </div>
                        </div>
                    </div>
                </swiper-slide>
            </swiper>
        </template>
        <template v-else>
            <div><i class="fa fa-info-circle"></i> There is no data</div>
        </template>
    </div>
</template>
<script>
import StarRating from '~/components/StarRating'
export default {
    name: 'ContentListView',
    props: ['data','content_loader','per_view'],
    components: {        
        StarRating
    },
    data(){
        return {            
            swiperOption: {
                slidesPerView: this.per_view?this.per_view:1,
                spaceBetween: 10,
                freeMode: true,
                grabCursor: true
            }
        }
    }
}
</script>
<style scoped>
    .head_section{
        display: flex;
        height: 30px
    }
    .head_section > div{
        font-size: 14px;
        font-weight: bold;
        color: #333;
        align-self: center
    }
    .head_section > div:last-child{
        margin-left: auto
    }
    .list{
        display: block;
        padding: 15px;
        background-color: #f7f7f7;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .bottom_block{
        display: flex;
        height: 30px;
    }
    .bottom_block > div{
        align-self: center
    }
    div.action_btn{
        align-self: center;
        margin-left: auto;
    }
    div.action_btn > span{
        display: inline-block;
        border: 1px solid #ddd;
        background-color: #ccc;
        font-size: 12px; color: #333;
        padding: 1px 15px; border-radius: 25px;
        cursor: pointer;
    }
</style>