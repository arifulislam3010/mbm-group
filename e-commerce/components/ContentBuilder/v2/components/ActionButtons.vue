<template>
    <div class="action_block">
        <i class="fa fa-up-down-left-right drag_cross" title="Drag"></i>
        <template v-if="grid_span">
            <div class="relative grid_span_block">
                <i class="fa fa-table-cells-large" title="Grid Span"></i>
                <div class="absolute grid grid-cols-4 span_list">
                    <span class="text-center" v-for="n in 12" @click="grid_span_modify(n)">{{ n }}</span>
                </div>
            </div>
        </template>
        <i class="fa fa-clone" @click="clone_form_element" title="Clone"></i>
        <i class="fa fa-trash-alt" @click="remove_form_element" title="Remove"></i>
    </div>
</template>
<script>
export default {
    name: 'ActionButtonsBlock',    
    props: {
        grid_span: Boolean
    },
    methods: {
        remove_form_element: function() {
            this.$emit('removeFormElement')
        },
        clone_form_element: function() {
            this.$emit('cloneFormElement')
        },
        grid_span_modify: function(size) {
            this.$emit('gridSpanModify', size)
        }
    }
}
</script>
<style lang="scss" scoped>
    $item_width: 22;
    .action_block{
        position: absolute;
        display: none;
        right: 15px; top: 0;
        background-color: #f7f7f7;
        z-index: 1;
        
        & > i,
        & > * > i{
            font-size: 8px;
            background-color: #39bdff;
            color: #fff;
            width: #{$item_width}px;
            height: #{$item_width - 2}px;
            line-height: #{$item_width - 2}px;
            border-left: 1px solid #fff;
            text-align: center;
        }

        & > .grid_span_block{            
            top: -3px;
            & > .span_list{
                display: none;
            }
            &:hover{
                & > .span_list{
                    display: grid;
                    width: #{$item_width * 4}px;
                    left: -#{$item_width}px;

                    & > span{
                        font-size: 8px;
                        background-color: #39bdff;
                        color: #fff;
                        height: #{$item_width - 2}px;
                        line-height: #{$item_width - 2}px;
                        border-left: 1px solid #fff;
                        border-top: 1px solid #fff;
                    }
                }
            }
        }
    }
</style>