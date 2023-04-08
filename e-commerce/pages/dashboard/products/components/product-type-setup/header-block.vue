<template>
    <div :class="['header',{active:$parent.$parent.selected_product_type_index==cur_index}]" @click="$parent.$parent.select_entry(cur_index)">
        <div class="drag_cross">
            <i class="fa fa-up-down-left-right"></i>
        </div>
        <div class="up_down_btn">
            <i v-if="$parent.$parent.selected_product_type_index==cur_index" class="fa fa-chevron-up"></i>
            <i v-else class="fa fa-chevron-down"></i>
        </div>
        <div class="info_title">
            <span v-if="title==''">Create an entry</span>
            <span v-else>{{ title }}</span>
        </div>
        <div v-if="!$parent.$parent.drag" class="action_block">
            <span class="del_btn" @click="$parent.$parent.del_entry(cur_index)"><i class="fa fa-trash-alt"></i></span>
        </div>
    </div>
</template>
<script>
export default {
    name: 'HeaderBlock',
    props: ['cur_index','title']
}
</script>
<style scoped>
    .header{
        display: flex;
        align-items: center;
        gap: 10px;
        height: 36px;
        width: 100%;
        padding: 0 10px;        
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        transition: all 0.4s;
    }
    .header.active{
        background-color: #e6f0fb;
        border-color: #aed4fb;
        color: #007eff;
    }
    .up_down_btn.drag_cross{
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
    }
    .action_block{
        align-self: center;
        margin-left: auto;        
    }
    .action_block > .del_btn{
        display: inline-block;
        font-size: 10px;
        color: #CD0000;
        cursor: pointer;
    }
</style>