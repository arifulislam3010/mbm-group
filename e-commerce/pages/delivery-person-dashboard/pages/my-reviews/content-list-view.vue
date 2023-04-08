<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">#</th>                    
                    <th>Order Info</th>                    
                    <th style="text-align:center" width="80">Rating</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ (index + 1) }}</td>                        
                        <td>                            
                            <div class="order_info">
                                <div class="order_id">ID #{{ item.order_id }}</div>
                                <div class="ordered_at"><small>Placed on {{ item.created_at }}</small></div>
                            </div>
                        </td>                        
                        <td align="center">                            
                            <StarRating :cur_rating="item.delivery_person_info.rating_points" :len="$store.state.rating_out_off" :size="'small'" />
                            <div><small>{{ item.delivery_person_info.rating_points }} / {{ $store.state.rating_out_off }}</small></div>
                        </td>
                        <td align="center">
                            <div class="action_btn">
                                <span @click="$parent.view_details_order(item)"><i class="fa fa-external-link"></i> View Detail</span>
                                <div class="total_items"><small>Total {{ item.order_items_info.length }} item{{ item.order_items_info.length>1?'s':'' }}</small></div>
                            </div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>                        
                        <td colspan="4" align="center"><i class="fa fa-info-circle"></i> There is no data</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
import StarRating from '~/components/StarRating'
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    components: {        
        StarRating
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
    .my_reviews_block{
        padding: 15px
    }
    .content_block{
        display: block;
        padding: 15px;
    }
    .mobile_device .content_block{
        padding: 0
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