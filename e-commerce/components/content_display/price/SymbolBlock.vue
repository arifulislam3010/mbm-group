<template>
    <span class="symbol_view_block">
        <template v-if="$store.state.currency_info.symbol">{{ $store.state.currency_info.symbol }}</template>
        <template v-else>{{ $store.state.currency_info.title }}</template>
    </span>
</template>
<script>
export default {
    name: 'SymbolViewBlock'
}
</script>
<style scoped>
    .symbol_view_block{
        display: inline-block;
    }
</style>