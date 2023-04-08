<template>
    <div class="float_filter_block">
        <div class="black_mask_overlay" @click="close_popup"></div>
        <div class="white_overlay">
            <FilterItemsBlock />
        </div>
    </div>
</template>
<script>
import FilterItemsBlock from '@/components/content_display/FilerItemsBlock'
export default {
    name: 'FloatFilterBlock',
    components: {
        FilterItemsBlock
    },
    mounted() {        
        document.body.classList.add('popup_open')
    },
    methods: {
        close_popup: function(){
            document.body.classList.remove('popup_open');
            this.$parent.float_filter_open = false
        }
    }
}
</script>
<style lang="scss" scoped>
    .float_filter_block{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        z-index: 10;
    }
    .black_mask_overlay {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-image: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(102, 102, 102, 0.50196));
    }
    .white_overlay {
        position: relative;
        left: 0;
        top: 60px;
        width: 90%;        
        padding: 15px 15px 80px 15px;
        background-color: #fff;
        transition: all 0.4s;
    }
</style>