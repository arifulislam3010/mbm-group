<template>
    <div class="info_content_block">
        <template v-for="(item,index) in data">
            <label v-if="item" :key="'label-' + index">{{ item.product_info_type_data.type_title }}</label>
            <!-- <div v-if="item" class="content_block mb-4" :key="'content-' + index" v-html="dtl_content[i].html"></div> -->
            <ContentViewBlock :data="dtl_content" />
        </template>
    </div>
</template>
<script>
import ContentViewBlock from './ContentViewBlock'
export default {
    name: 'InfoContentBlock',
    props: {
        data: Array
    },
    components: {
        ContentViewBlock
    },
    data(){
        return {
            dtl_content: []
        }
    },
    watch: {
        data: function(item){
            item.forEach(v => {
                let getContent = typeof JSON.parse(v.content) == 'object'?JSON.parse(v.content):v.content
                console.log('',getContent)
                // let obj = {
                //     html: getContent.html?getContent.html:getContent,
                //     css: getContent.css?getContent.css:''
                // }                
                // this.dtl_content.push(obj)
                this.dtl_content = getContent
            })
        }
    }
}
</script>
<style lang="scss" scoped>
    .info_content_block :deep(label){
        display: inline-block; padding-bottom: 5px;
        border-bottom: 2px dotted #ccc;
        font-size: 16px; color: #333;
    }
    .content_block{
        font-size: 13px;
    }
</style>