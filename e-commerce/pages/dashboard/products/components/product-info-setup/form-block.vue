<template>
    <div class="product_info_form_block">
        <div class="mb-4">
            <label>Choose Information Type</label>
            <div class="mb-4">
                <select v-model="$parent.$parent.info_arr[cur_index].index" class="form-control" @change="$parent.$parent.select_info_type(cur_index)">
                    <option value="">Select One</option>
                    <template v-if="$parent.$parent.product_info_types.length>0">
                        <option v-for="(item,index) in $parent.$parent.product_info_types" :key="index" :value="index">{{ item.title }}</option>
                    </template>
                </select>
            </div>
            <label>Content</label>
            <div class="text_editor_block">
                <!-- <vue-editor v-model="$parent.$parent.info_arr[cur_index].content" /> -->
                <!-- <tinymce :id="'product-' + cur_index" v-model="$parent.$parent.info_arr[cur_index].content"></tinymce> -->
                <template v-if="$parent.$parent.info_arr[cur_index].content">
                    <div class="edit_btn" @click="edit_editor()"><i class="fa fa-edit"></i> Edit</div>
                    <!-- <div class="content_dtl" v-html="$parent.$parent.info_arr[cur_index].content.html"></div> -->
                    <ContentViewBlock :data="$parent.$parent.info_arr[cur_index].content" />
                </template> 
            </div>
        </div>
        <div class="black_overlay" v-if="content_builder_open">
            <div class="close_popup" @click="close_editor()">
                <i class="fa fa-times"></i>
            </div>
            <div class="content_builder_block">
                <!-- <ContentBuilder :content="$parent.$parent.info_arr[cur_index].content.html" :css="$parent.$parent.info_arr[cur_index].content.css" :assets="true" /> -->
                <ContentBuilder :content="$parent.$parent.info_arr[cur_index].content" @setContent="set_content" />
            </div>
        </div>
    </div>
</template>
<script>
import ContentViewBlock from './ContentViewBlock'
// import ContentBuilder from '@/components/ContentBuilder/v1'
import ContentBuilder from '@/components/ContentBuilder/v2'
export default {
    name: 'ProductInfoFormBlock',
    props: ['data','cur_index'],
    components: {
        ContentViewBlock,
        ContentBuilder
    },
    data(){
        return {
            content_builder_open: false
        }
    },
    mounted(){
        if(this.$parent.$parent.info_arr[this.cur_index].content.css){
            const style = document.createElement('style')
            style.innerHTML = this.$parent.$parent.info_arr[this.cur_index].content.css
            document.head.appendChild(style)
        }
    },
    methods: {
        set_content: function(obj){
            this.$parent.$parent.info_arr[this.cur_index].content = obj
        },
        getData: function(html,css){
            // console.log(this.editor.getHtml())
            // console.log(html,css)
            // this.$emit('change', this.editor.getHtml());
            this.$parent.$parent.info_arr[this.cur_index].content.html = html
            this.$parent.$parent.info_arr[this.cur_index].content.css = css            
        },
        edit_editor: function(){
            this.content_builder_open = true
            document.body.style.overflowY = "hidden";
        },
        close_editor: function(){
            this.content_builder_open = false
            document.body.style.overflowY = "auto";

            // const style = document.createElement('style')
            // style.innerHTML = this.$parent.$parent.info_arr[this.cur_index].content.css
            // document.head.appendChild(style)
        }
    }
}
</script>
<style lang="scss" scoped>
    .product_info_form_block{
        display: block;
        padding: 15px; background-color: #eee;
        border-bottom: 1px solid #ddd;
        transition: all 0.4s;
    }
    .text_editor_block{
        position: relative;
        background-color: #fff;
        min-height: 100px;
        max-height: 250px;
        padding: 10px;
        display: block;
        overflow-y: auto;
        .edit_btn{
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            background-color: #44444480;
            color: #fff;
            display: flex;
            height: 40px;
            padding: 0 25px;            
            border-radius: 25px;
            align-items: center;
            gap: 5px;
            transition: all 0.4s;
            z-index: 5;
            &:hover{
                background-color: #000;
            }
        }
    }
    .content_builder_block{
        position: relative;
        background-color: #fff;
        width: 80%;                
        top: 5%;
        left: 10%;
        box-shadow: 0 0 5px #000;        
    }
    .close_popup{
        display: inline-block;
        width: 36px;
        height: 36px;
        background-color: #fff;
        line-height: 38px;
        text-align: center;
        border-radius: 50%;
        right: 15px;
        position: absolute;
        top: 15px;
        color: #CD0000;
        cursor: pointer;
    }
</style>