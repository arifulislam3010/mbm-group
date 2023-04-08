<template>
    <div class="content_layout_block">
        <draggable :list="data" group="container_group_block" handle=".drag_cross">
            <div v-for="(element,elm_index) in data" class="block relative mb-4 p-3 element_layer_block" :class="{active:sel_element_index==elm_index && sel_grid_column_index==-1 && sel_content_item_index==-1}" :key="elm_index" @click="sel_element_layer($event, elm_index)">
                <div class="flex flex-row items-center elm_action_btn_block">
                    <ActionButtons @cloneFormElement="clone_form_element" @removeFormElement="remove_form_element" :grid_span="false" />
                </div>
                <draggable :list="element.column" group="grid_group_block" handle=".drag_cross" class="grid grid-cols-12 relative grid_cols_block">
                    <div :class="['block','col-span-' + (column.size?column.size:6),'relative','border','rounded-md','p-3',{active:sel_element_index==elm_index && sel_grid_column_index==col_index && sel_content_item_index==-1}]" v-for="(column,col_index) in element.column" :key="col_index" @click="sel_grid_col($event, elm_index, col_index)">
                        
                        <div class="flex flex-row items-center grid_action_btn_block">
                            <ActionButtons @cloneFormElement="clone_form_element" @removeFormElement="remove_form_element" @gridSpanModify="grid_span_modify" :grid_span="true" />
                        </div>

                        <template v-if="column.data.length > 0">
                            <draggable :list="column.data" group="content_group_block" handle=".drag_cross">
                                <div :class="['block','relative','p-3','mb-4','content_layer_block',{active:sel_element_index==elm_index && sel_grid_column_index==col_index && sel_content_item_index==index}]" v-for="(item,index) in column.data" :key="index" @click="sel_content_property($event, item.type, elm_index, col_index, index)">
                                    
                                    <div class="flex flex-row items-center content_action_btn_block">
                                        <ActionButtons @cloneFormElement="clone_form_element" @removeFormElement="remove_form_element" :grid_span="false" />
                                    </div>

                                    <!-- <div>{{ item }}</div> -->
                                    <RichTextAreaFormBlock v-if="item.type=='rich_txtarea'" :data="item" :index="index"  />
                                    <ImageFormBlock v-else-if="item.type=='image'" :data="item" :index="index"  />
                                    <EmbedFormBlock v-else-if="item.type=='embed'" :data="item" :index="index"  />
                                </div>
                            </draggable>
                        </template>
                        <div v-else class="flex flex-row items-center px-4 add_component_btn justify-center cursor-pointer" @click="add_content_block(elm_index,col_index)">
                            <i class="fa fa-plus-circle"></i>
                            <span>Add New Component</span>
                        </div>
                    </div>
                </draggable>

                <div class="flex flex-row items-center justify-center mt-3 add_col_btn">
                    <div class="flex flex-row items-center px-5 cursor-pointer" @click="$parent.add_new_grid_column(elm_index)">
                        <i class="fa fa-table-cells"></i>
                        <span>Add Grid Column</span>
                    </div>
                </div>
            </div>
        </draggable>
        <div class="flex items-center justify-center cursor-pointer mt-4 add_new_elm_btn" @click="$parent.add_new_element">
            <i class="fa fa-plus-circle"></i>
            <span>Add New Element</span>
        </div>
    </div>
</template>
<script>
import RichTextAreaFormBlock from './Forms/RichTextArea'
import ImageFormBlock from './Forms/Image'
import EmbedFormBlock from './Forms/Embed'
import ActionButtons from './ActionButtons'
export default {
    name: 'ContentLayoutBlock',
    props: {
        data: Array
    },
    components: {
        RichTextAreaFormBlock,
        ImageFormBlock,
        EmbedFormBlock,
        ActionButtons
    },
    data(){
        return {
            sel_element_index: -1,
            sel_grid_column_index: -1,
            sel_content_item_index: -1
        }
    },    
    methods: {        
        grid_span_modify: function(grid_span) {
            this.$emit('gridSpanModify', this.sel_element_index, this.sel_grid_column_index, grid_span)
        },
        remove_form_element: function() {
            this.$emit('removeFormElement', this.sel_element_index, this.sel_grid_column_index, this.sel_content_item_index)
        },
        clone_form_element: function() {
            this.$emit('cloneFormElement', this.sel_element_index, this.sel_grid_column_index, this.sel_content_item_index)
        },
        add_content_block: function(elm_index,col_index) {            
            this.$emit('selContentComponent', elm_index, col_index)
        },
        sel_content_property: function(e, type, elm_index, col_index, index) {
            e.stopPropagation()
            
            this.sel_element_index = elm_index
            this.sel_grid_column_index = col_index
            this.sel_content_item_index = index
            
            this.$emit('selContentComponent', elm_index, col_index)
            // emit('selInputFieldProperty', type, elm_index, col_index, index)
        },
        sel_grid_col: function(e, elm_index, col_index) {
            e.stopPropagation()

            this.sel_element_index = elm_index
            this.sel_grid_column_index = col_index
            this.sel_content_item_index = -1

            this.$emit('selContentComponent', elm_index, col_index)
            // emit('selInputFieldProperty', '', elm_index, col_index, -1)
        },
        sel_element_layer: function(e, elm_index) {
            e.stopPropagation()
            
            this.sel_element_index = elm_index
            this.sel_grid_column_index = -1
            this.sel_content_item_index = -1
            
            this.$emit('selContentComponent', elm_index, -1)
            // emit('selInputFieldProperty', '', elm_index, -1, -1)
        },
        getPaddingStyle: function(content) {
            let styles = ''
            styles += content.top?(content.top.size + content.top.type):0
            styles += ' ' + content.right?(content.right.size + content.right.type):0
            styles += ' ' + content.bottom?(content.bottom.size + content.bottom.type):0
            styles += ' ' + content.left?(content.left.size + content.left.type):0 + ';'

            return styles
        },
        getStyles: function(content) {
            let styles = ''
            if(Object.keys(content).length > 0) {
                for(let key in content) {
                    if(key=='padding') styles += 'padding:' + this.getPaddingStyle(content[key])
                }
            }            
            return styles;
        }        
    }
}
</script>
<style lang="scss" scoped>
    .content_layout_block{        
        display: block;
        position: sticky;
        top: 34px;
        border: 1px solid #eee;
        border-top: none;
        padding: 15px;
        border-radius: 0 0 5px 5px;

        .element_layer_block{
            border: 1px solid #eee;
            border-radius: 5px;
            &.active{
                border: 1px solid #39bdff;

                & > .elm_action_btn_block{
                    &:deep(.action_block){
                        display: flex;
                    }
                }
            }
            .add_col_btn{                
                & > div{
                    gap: 5px;
                    width: auto;
                    font-size: 12px;
                    transition: all 0.4s;

                    &:hover{
                        color: #39bdff;
                    }
                }
            }

            .grid_cols_block{
                gap: 15px;
                & > div{
                    border-radius: 5px;
                    &.active{
                        border: 1px solid #39bdff !important;

                        & > .grid_action_btn_block{
                            &:deep(.action_block){
                                display: flex;
                            }
                        }
                    }

                    .content_layer_block{
                        user-select: none;
                        &.active{
                            border: 1px solid #39bdff !important;

                            & > .content_action_btn_block{
                                &:deep(.action_block){
                                    display: flex;
                                }
                            }
                        }
                    }

                    .add_component_btn{
                        gap: 5px;
                        height: 100px;
                        border: 2px dashed #ddd;
                        background-color: #efefef;
                        border-radius: 5px;
                    }
                }
            }
        }
        .add_new_elm_btn{
            height: 30px;
            border-radius: 5px;
            background-color: #b0cddc80;
            font-size: 11px;
            gap: 5px;
            transition: all 0.4s;

            &:hover{
                background-color: #b0cddc;
            }
        }
    }
</style>