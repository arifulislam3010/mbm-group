<template>
    <div class="content_builder_setup_block">
        <div class="flex flex-row">
            <div v-if="!sel_preview_status" class="left_block">
                <ContentToolsBlock @addContentElement="add_content_element" />
            </div>
            <div :class="['content_block',{full:sel_preview_status}]">
                <div class="flex flex-row items-center header_bar_block" :class="{popup_open:$store.state.content_builder_media_gallery}">
                    <div :class="['laptop_device',{active:sel_device_id==1}]" @click="sel_device(1)"><i class="fa fa-laptop" title="Laptop View"></i></div>
                    <!-- <div :class="['mobile_device',{active:sel_device_id==2}]" @click="sel_device(2)" title="Mobile View"><i class="fa fa-mobile-screen"></i></div> -->
                    <!-- <div class="both_device">
                        <i class="fa fa-mobile-screen _mobile"></i>
                        <i class="fa fa-laptop _laptop"></i>
                    </div> -->
                    <template v-if="contentLayoutData.length > 0">
                        <div :class="['layout_btn ml-auto',{active:!sel_preview_status}]" @click="sel_preview(false)" title="Build Mode"><i class="fa fa-palette"></i></div>
                        <div :class="['preview_btn',{active:sel_preview_status}]" @click="sel_preview(true)" title="Preview Mode"><i class="fa fa-play"></i></div>
                        <!-- <div :class="['save_btn']" @click="submit_data" title="Save Form"><i class="fa fa-save"></i> Save</div> -->
                    </template>
                </div>
                <ContentViewBlock v-if="sel_preview_status" :data="contentLayoutData" />
                <ContentLayoutBlock v-else
                @gridSpanModify="grid_span_modify"
                @removeFormElement="remove_form_element"
                @cloneFormElement="clone_form_element"
                @selContentComponent="sel_content_component" 
                :data="contentLayoutData" />
            </div>
        </div>
    </div>
</template>
<script>
import ContentToolsBlock from './components/ContentTools'
import ContentLayoutBlock from './components/ContentLayout'
import ContentViewBlock from './components/ContentViewLayout'
export default {
    name: 'ContentBuilderBlock',
    props: ['content'],
    components: {
        ContentToolsBlock,
        ContentLayoutBlock,
        ContentViewBlock
    },
    data() {
        return {
            sel_element_index: -1,
            sel_element_col_index: -1,
            sel_elm_attr: {},
            contentLayoutData: this.content && this.content.length > 0?this.content:[],
            sel_device_id: 1,
            sel_preview_status: false
        }
    },
    watch: {
        contentLayoutData: {
            handler(obj){
                this.$emit('setContent', obj)
            },
            deep: true
        }        
    },
    methods: {
        sel_device: function(id) {
            this.sel_device = id
        },
        sel_preview: function(status) {
            this.sel_preview_status = status
        },
        sel_content_component: function(elm_index, col_index) {
            this.sel_element_index = elm_index
            this.sel_element_col_index = col_index

            if(elm_index > -1 && col_index > -1) this.sel_elm_attr = this.contentLayoutData[elm_index].column[col_index]
            else if(elm_index > -1) this.sel_elm_attr = this.contentLayoutData[elm_index]
        },
        grid_span_modify: function(elm_index, col_index, grid_span) {
            if(col_index > -1) this.contentLayoutData[elm_index].column[col_index].size = grid_span
        },
        remove_form_element: function(elm_index, col_index, index) {
            // console.log(elm_index, col_index)
            if(index > -1) this.contentLayoutData[elm_index].column[col_index].data.splice(index, 1)
            else if(col_index > -1) this.contentLayoutData[elm_index].column.splice(col_index, 1)
            else this.contentLayoutData.splice(elm_index, 1)

            // sel_input_field_type.value = ''
            // sel_form_element_index.value = ''
        },
        clone_form_element: function(elm_index, col_index, index) {
            if(index > -1) {
                let getData = this.contentLayoutData[elm_index].column[col_index].data[index]
                this.contentLayoutData[elm_index].column[col_index].data.splice(index, 0, getData)
            }else if(col_index > -1) {
                let getData = this.contentLayoutData[elm_index].column[col_index]
                this.contentLayoutData[elm_index].column.splice(col_index, 0, getData)
            }else {
                let getData = this.contentLayoutData[elm_index]
                this.contentLayoutData.splice(elm_index, 0, getData)
            }
        },
        add_content_element: function(obj) {
            let elm_index = this.sel_element_index
            let col_index = this.sel_element_col_index
            // console.log(elm_index, col_index)
            // console.log(this.contentLayoutData[elm_index].column[col_index].data)
            if(elm_index > -1 && col_index > -1) this.contentLayoutData[elm_index].column[col_index].data.push(obj)
        },
        add_new_grid_column: function(index) {
            let colObj = {
                name: 'Column',
                size: 6,
                data: []
            }
            this.contentLayoutData[index].column.push(colObj)
        },
        add_new_element: function() {
            let obj = {
                name: 'Element',
                // css: {
                //     padding: {
                //         top: {size: 15,type: 'px'},
                //         right: {size: 15,type: 'px'},
                //         bottom: {size: 15,type: 'px'},
                //         left: {size: 15,type: 'px'}
                //     }
                // },
                column: [{
                    name: 'Column',
                    size: 6,
                    data: []
                }]
            }
            this.contentLayoutData.push(obj)
        }
    }
}
</script>
<style lang="scss" scoped>
    $left_side_width: 200;
    $right_side_width: 0;
    .content_builder_setup_block{
        padding: 15px;
        transition: all 0.4s;
        & > div{
            gap: 15px;        
            .left_block{
                width: #{$left_side_width}px;
            }
            .content_block{
                width: calc(100% - #{($left_side_width + $right_side_width + 50)}px);
                transition: all 0.4s;
                &.full{
                    width: 100%
                }

                & > .header_bar_block{
                    position: sticky;
                    top: 0;
                    background-color: #f7f7f7;
                    border: 1px solid #eee;
                    padding: 5px;
                    z-index: 1;
                    &.popup_open{
                        z-index: 0;
                    }
                    & > div{
                        position: relative;
                        padding: 0 5px;
                        height: 22px;
                        line-height: 22px;                
                        color: #666;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        font-size: 12px;
                        
                        cursor: pointer;

                        &:hover{
                            color: #1890ff;
                            border-color: #1890ff;
                        }

                        &.active{
                            color: #ccc;
                            background: #eee;                    

                            &:hover{
                                color: #ccc;
                                border-color: #ddd;
                                cursor: auto;
                            }
                        }

                        // &.both_device{                    
                        //     background-color: #eee;
                        //     width: 36px;

                        //     & > i{
                        //         top: 6px;
                        //         left: 20%;
                        //         position: absolute;
                        //         background-color: #eee;
                        //         z-index: 1;
                        //         font-size: 10px;
                        //         &._laptop{
                        //             z-index: 2;
                        //         }
                        //     }
                        // }
                    }
                }
            }
            .right_block{
                width: #{$right_side_width}px;
            }
        }
    }
</style>