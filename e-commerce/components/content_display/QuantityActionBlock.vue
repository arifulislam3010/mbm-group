<template>
    <div class="quantity_action_block">
        <span class="minus" @click="$parent.quantity_submit(parseInt(cur_quantity) - 1)"><i class="fa fa-minus"></i></span>
        <span class="amount"><input type="number" v-model="$parent.quantity" step="1" min="1" @keyup="check_input($event)" /></span>
        <span class="plus" @click="$parent.quantity_submit(parseInt(cur_quantity) + 1)"><i class="fa fa-plus"></i></span>
    </div>
</template>
<script>
export default {
    name: 'QuantityActionBlock',
    props: ['quantity'],
    data(){
        return {
            // cur_quantity: this.quantity
        }
    },
    computed: {
        cur_quantity: function(){
            return this.quantity
        }
    },
    methods: {
        check_input: function(e){
            console.log(e)
            if(e.keyCode==8 || e.keyCode==38 || e.keyCode==40){
                this.$parent.quantity_submit(parseInt(this.cur_quantity))
                e.preventDefault()
            }
            else if (e.keyCode < 48 || e.keyCode > 57) e.preventDefault();
            else this.$parent.quantity_submit(parseInt(this.cur_quantity))
        }
    }
}
</script>
<style scoped>
    .quantity_action_block{
        display: inline-block;
    }
    .quantity_action_block > .minus,
    .quantity_action_block > .plus{
        display: inline-block; text-align: center; user-select: none;
        background-color: #f7f7f7; color: #999; font-size: 12px;
        width: 26px; height: 26px; line-height: 26px;
        cursor: pointer; transition: all 0.4s;
    }
    .quantity_action_block > .minus:hover,
    .quantity_action_block > .plus:hover{
        background-color: #eee; font-size: 14px;
    }
    .quantity_action_block > .amount{
        display: inline-block;
        width: 35px; height: 26px; text-align: center; line-height: 26px; white-space: nowrap; font-size: 12px;
    }
    .quantity_action_block > .amount > input{
        display: inline-block; text-align: center; border: none; width: 100%;
    }
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>