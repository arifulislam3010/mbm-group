<template>
    <div :class="['header',{active:$parent.selected_price_index==cur_index}]" @click="$parent.select_entry(cur_index)">
        <div class="up_down_btn">
            <i v-if="$parent.selected_price_index==cur_index" class="fa fa-chevron-up"></i>
            <i v-else class="fa fa-chevron-down"></i>
        </div>
        <div class="info_title">
            <span v-if="title==''">Create an entry</span>
            <span v-else>{{ title }}</span>
        </div>
        <div class="action_block">
            <span class="del_btn" @click="$parent.del_entry(cur_index)"><i class="fa fa-trash-alt"></i></span>
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
        height: 36px;
        width: 100%;
        padding: 0px 15px;        
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        transition: all 0.4s;
    }
    .header.active{
        background-color: #e6f0fb;
        border-color: #aed4fb;
        color: #007eff;
    }
    .up_down_btn{
        align-self: center;        
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
        margin-left: 15px;
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