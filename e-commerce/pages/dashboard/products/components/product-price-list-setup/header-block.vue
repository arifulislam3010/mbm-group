<template>
    <div :class="['header',{active:$parent.$parent.selected_price_index==cur_index}]">
        <div class="drag_cross">
            <i class="fa fa-up-down-left-right"></i>
        </div>
        <div class="up_down_btn" @click="$parent.$parent.select_entry(cur_index)">
            <i v-if="$parent.$parent.selected_price_index==cur_index" class="fa fa-chevron-up"></i>
            <i v-else class="fa fa-chevron-down"></i>
        </div>
        <div class="info_title" @click="$parent.$parent.select_entry(cur_index)">
            <span v-if="title==''">Create an entry</span>
            <span v-else>{{ title }}<template v-for="item in product_type_infos">
              <template v-if="item.title"> / {{ item.title }}</template>
            </template><template v-if="$parent.$parent.price_arr[cur_index].color_type_id && $parent.$parent.product_color_info_arr[$parent.$parent.price_arr[cur_index].color_type_id]">/ {{ $parent.$parent.product_color_info_arr[$parent.$parent.price_arr[cur_index].color_type_id].color_title }} Color</template><template v-if="$parent.$parent.price_arr[cur_index].cart_qty_mode">/ {{ $parent.$parent.price_arr[cur_index].cart_qty_mode==2?'Open':'Fixed' }}</template><template v-if="$parent.$parent.price_arr[cur_index].unit_price">/ {{ $store.state.currency_info.title }} {{ $parent.$parent.price_arr[cur_index].unit_price }}</template></span>
        </div>
        <div v-if="!$parent.$parent.drag" class="action_block">
            <SwithcBtn :status="$parent.$parent.price_arr[cur_index].status" :index="'status'" />
            <input type="hidden" v-model="$parent.$parent.price_arr[cur_index].status" />
            <span class="clone_btn" @click="$parent.$parent.clone_entry(clone_data)"><i title="Clone Price Item" class="far fa-clone"></i></span>
            <span class="del_btn" @click="$parent.$parent.del_entry(cur_index)"><i class="fa fa-trash-alt"></i></span>
        </div>
    </div>
</template>
<script>
import SwithcBtn from '@/components/action_buttons/SwitchBtn'
export default {
    name: 'HeaderBlock',
    props: ['cur_index','title','product_type_infos'],
    components: {
        SwithcBtn
    },
    computed: {
      clone_data: function(){
        return JSON.stringify(this.$parent.$parent.price_arr[this.cur_index])
      }
    },
    methods: {
        switch_data(index,status){
            this.$parent.$parent.price_arr[this.cur_index][index] = status
        }
    }
}
</script>
<style scoped>
    .header{
        display: flex;
        align-items: center;
        gap: 10px;
        height: 52px;
        width: 100%;
        padding: 0 10px;
        border-bottom: 1px solid #ddd;
        transition: all 0.4s;
    }
    .header.active{
        background-color: #e6f0fb;
        border-color: #aed4fb;
        color: #007eff;
    }
    .up_down_btn,.drag_cross{
        align-self: center;
        cursor: pointer;
    }
    .drag_cross > i{
        color: #666; cursor:move;
    }
    .up_down_btn > i{
        display: inline-block;
        width: 19px;
        height: 19px;
        line-height: 17px;
        font-size: 10px;
        text-align: center;
        color: #aaa;
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 50%;
    }
    .active .up_down_btn > i{
        background-color: #aed4fb;
        border: 1px solid #007eff;
        color: #007eff;
    }
    .info_title{
        align-self: center;
        /* margin-left: 15px; */
        width: calc(100% - 75px);
        cursor: pointer;
    }
    .action_block{
        display: flex;
        align-self: center;
        margin-left: auto;
        align-items: center;
        gap: 10px
    }
    .action_block > .del_btn,
    .action_block > .clone_btn{
        display: inline-block;
        font-size: 10px;
        color: #CD0000;
        cursor: pointer;
    }
    .action_block > .clone_btn{
        color: #336699;
    }
    .action_block :deep(.switch_block){
        width: 50px;
        height: 16px;
        line-height: 16px;
        font-size: 8px;
        border-radius: 25px;
        overflow: hidden;
    }
</style>
