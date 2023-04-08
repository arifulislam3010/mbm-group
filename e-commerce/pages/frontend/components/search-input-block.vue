<template>
    <div class="seach_input_block">
        <input type="input" v-model="srch_keyword" :placeholder="srch_placeholder" />
        <i class="fa fa-search"></i>
    </div>
</template>
<script>
export default {
    name: 'SearchInputBlock',
    data() {
        return {
            srch_placeholder: 'generic name, medicine name, diseases name etc',
            srch_keyword: ''            
        }
    }
}
</script>
<style scoped>
    .seach_input_block{
        position: relative;
    }
    .seach_input_block > input{
        display: block; background-color: #f7f7f7; border: 1px solid #eee; color: #666; border-radius: 5px; width: 100%; line-height: 38px; margin: 10px 0; padding: 0 20px 0 12px
    }
    .seach_input_block > i{
        position: absolute;
        right: 12px; top: 12px; font-size: 16px; color: #ccc;
    }
</style>