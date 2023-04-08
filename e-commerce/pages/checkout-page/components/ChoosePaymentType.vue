<template>
    <div class="choose_payment_type_block">
        <div @click="$parent.choose_payment_type=1" :class="{active:$parent.choose_payment_type==1}">
            <i v-if="$parent.choose_payment_type=='1'" class="far fa-check-circle active"></i>
            <i v-else class="far fa-circle"></i>
            <i class="fa fa-hand-holding-usd ml-2"></i>
            <span class="ml-2">Cash Payment</span>
        </div>
        <div @click="$parent.choose_payment_type=2" :class="{active:$parent.choose_payment_type==2}">
            <i v-if="$parent.choose_payment_type=='2'" class="far fa-check-circle active"></i>
            <i v-else class="far fa-circle"></i>
            <i class="fa fa-credit-card ml-2"></i>
            <span class="ml-2">Digital Payment</span>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ChoosePaymentTypeBlock'
}
</script>
<style scoped>
    .choose_payment_type_block{
        display: block;
        margin: 0 15px;
    }
    .choose_payment_type_block > div{
        display: flex;
        height: 30px;
        cursor: pointer;
    }
    .choose_payment_type_block > div.active{
        color: #cd5200
    }
    .choose_payment_type_block > div > i,
    .choose_payment_type_block > div > span{
        align-self: center;
    }
    .choose_payment_type_block > div > i:first-child{
        font-size: 18px; color: #ccc;
        cursor: pointer;
        transition: all 0.4s;
    }
    .choose_payment_type_block > div > i:first-child.active,
    .choose_payment_type_block > div > i:first-child:hover{
        color: #cd5200;
    }
</style>