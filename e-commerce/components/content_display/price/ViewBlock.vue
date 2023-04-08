<template>
    <div :class="['price_view_block', {discounted:discount}, {resize:resize}]">
        <template v-if="$store.state.currency_info.symbol_pos=='left'">
            <SymbolBlock />
        </template>
        <span v-if="data">
            <template v-if="animate">{{ animatedNumber }}</template>
            <template v-else>{{ data }}</template>
        </span>
        <template v-if="$store.state.currency_info.symbol_pos=='right'">
            <SymbolBlock />
        </template>
    </div>
</template>
<script>
import SymbolBlock from './SymbolBlock'
export default {
    name: 'PriceViewBlock',
    props: ['data','discount','animate','resize'],
    data(){
        return {
            price: 0,
            tweenedNumber: 0
        }
    },
    components: {
        SymbolBlock
    },
    computed: {
        animatedNumber: function() {
            return this.tweenedNumber.toFixed(2);
        }
    },
    watch: {
        price: function(newValue) {
            // alert(newValue)
            this.$gsap.to(this.$data, { duration: 0.5, tweenedNumber: newValue });
        },
        data: function(newVal) {
            this.price = newVal
        }
    },
    mounted(){
        this.price = this.data
    }
}
</script>
<style lang="scss" scoped>
    .price_view_block{
        display: inline-block;
        white-space: nowrap;
    }
    .discounted{
        color: $ash;
        text-decoration: line-through;
        font-size: 13px;

        &.resize{
          font-size: 10px
        }
    }
</style>
