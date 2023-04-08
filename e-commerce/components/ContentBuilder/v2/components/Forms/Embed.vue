<template>
    <div class="embed_content_block">        
        <div :class="['input_box']">
            <textarea v-model="data.content"></textarea>
        </div>
    </div>
</template>
<script>
export default {
    name: 'EmbedCodeBlock',
    props: {
        data: Object,
        index: Number,
        preview: Boolean
    }
}
</script>
<style lang="scss" scoped> 
    .embed_content_block{
        display: block;
        position: relative;
        & > .input_box{
            textarea{
                pointer-events: all;
                padding: 2px 8px;
                width: 100%; border: 1px solid #ddd;
                border-radius: 5px;                
            }
        }
    }
</style>