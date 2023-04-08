<template>
    <div class="discount_percentage_block">        
        <span v-if="$store.state.sel_lang=='bn'">{{ $enToBnNumber(discount_percentage_val) }}%</span>
        <span v-else>{{ discount_percentage_val }}%</span>
    </div>
</template>
<script>
export default {
    name: 'DiscountPercentageBlock',
    props: ['regular_price','discount_price'],    
    computed: {
        discount_percentage_val: function() {
            if(parseFloat(this.discount_price) > parseFloat(this.regular_price)) return '+' + ((this.discount_price*100)/this.regular_price).toFixed(1);
            else return '-' + ( ( (this.regular_price - this.discount_price) * 100 ) / this.regular_price).toFixed(1);
        }
    },
    watch: {        
    },
    mounted(){
        this.price = this.data
    }
}
</script>
<style lang="scss" scoped>
    .discount_percentage_block{
        display: inline-block;
        white-space: nowrap;
        font-size: 16px;

        span {            
            font-size: 13px
        }
    }
</style>