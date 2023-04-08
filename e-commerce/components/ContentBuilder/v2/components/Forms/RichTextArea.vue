<template>
    <div class="rich_text_area_block">        
        <div :class="['input_box']">
            <vue-editor :editorToolbar="customToolbar" v-model="data.content" />
        </div>
    </div>
</template>
<script>    
export default {
    name: 'RichTextAreaBlock',
    props: {
        data: Object,
        index: Number,
        preview: Boolean
    },
    data(){
        return {
            customToolbar: [
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                ["bold", "italic", "underline", "strike"], // toggled buttons
                [
                    { align: "" },
                    { align: "center" },
                    { align: "right" },
                    { align: "justify" }
                ],
                ["blockquote", "code-block"],
                [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
                [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                // ["link", "image", "video"],
                ["link"],
                ["clean"] // remove formatting button
            ]
        }
    }
}
</script>
<style lang="scss" scoped> 
    .rich_text_area_block{
        display: block;
        position: relative;        
        & > .input_box{
            textarea{
                pointer-events: none;
                padding: 2px 8px;
                width: 100%; border: 1px solid #ddd;
                border-radius: 5px;
            }
            &.preview textarea{
                pointer-events: all;
            }
        }
    }
</style>