<template>
    <div class="action_block" @click="$parent.new_form"><span class="button"><i class="fa fa-plus"></i> {{ btn_caption }}</span></div>
</template>
<script>
export default {
    name: 'AddNewActionButton',
    props: ['btn_caption']
}
</script>
<style scoped>
    .action_block{
        text-align: right
    }
    .button{
        display: inline-block;
        border: 1px solid;
        border-top-color: #377af5;
        border-right-color: #d53e33;
        border-bottom-color: #fbb300;
        border-left-color: #399953;
        padding: 5px 10px;
        background-color: #eee;
        border-radius: 25px;
        cursor: pointer;
        color: #336633; font-weight: 700;
        font-size: 12px;
        transition: all 0.4s ease-in-out
    }
    .button:hover{
        border-top-color: #399953;
        border-right-color: #377af5;
        border-bottom-color: #d53e33;
        border-left-color: #fbb300;        
    }
</style>