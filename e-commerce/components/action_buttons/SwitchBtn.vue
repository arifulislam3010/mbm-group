<template>
    <div class="switch_block" @click="switch_toggle">
        <span :class="['active',{left:!reqStatus},{right:reqStatus}]"></span>
        <span :class="['off',{active:!reqStatus}]">{{ option_1 }}</span>
        <span :class="['on',{active:reqStatus}]">{{ option_2 }}</span>
    </div>
</template>
<script>
export default {
    name: 'SwitchBlock',
    props: ['index','status','opt1','opt2'],
    data(){
        return {
            reqStatus: this.status,
            option_1: this.opt1?this.opt1:'OFF',
            option_2: this.opt2?this.opt2:'ON'
        }
    },
    methods: {
        switch_toggle: function(){
            if(this.reqStatus) this.reqStatus = false;
            else this.reqStatus = true;

            if(this.$parent.switch_data) this.$parent.switch_data(this.index,this.reqStatus);
        }
    }
}
</script>
<style>
    .switch_block{
        position: relative;
        width: 120px; height: 36px;
        line-height: 36px;
        background-color: #eee;
        cursor: pointer;
    }
    .switch_block > span{
        position: absolute;
        top: 0;
        width: 50%; height: 100%;        
        color: #666; text-align: center;
        transition: all 0.4s;
    }
    .switch_block > span.off{
        left: 0; right: auto;
    }
    .switch_block > span.on{
        right: 0; left: auto;
    }
    .switch_block > span.off.active,
    .switch_block > span.on.active{
        color: #fff;
    }
    .switch_block > span.active.left{
        left: 0; right: auto;
        background-color: lightcoral;        
    }
    .switch_block > span.active.right{
        right: 0; left: auto;
        background-color: lightseagreen;        
    }
</style>