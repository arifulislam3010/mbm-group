<template>
    <div class="content_tools_block">
        <div class="head_bar">
            <div class="flex flex-row items-center">
                <i class="fa fa-cog"></i>
                <span>Display Tools</span>
            </div>
        </div>
        <div class="grid grid-cols-2">
            <div @click="addItem('rich_txtarea')">
                <i class="fa fa-plus-circle"></i>
                <div><i class="fa fa-newspaper"></i></div>
                <div>Rich Textarea</div>
            </div>
            <div @click="addItem('image')">
                <i class="fa fa-plus-circle"></i>
                <div><i class="fa fa-image"></i></div>                    
                <div>Image</div>
            </div>
            <div @click="addItem('embed')">
                <i class="fa fa-plus-circle"></i>
                <div><i class="fa fa-code"></i></div>                    
                <div>Embed Code</div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'FormComponentsBlock',
    methods: {
        addItem: function(type) {
            let obj = {}
            if(type=='rich_txtarea') {
                obj = {
                    'name': 'rich_txtarea',
                    'type': 'rich_txtarea',                    
                    'content': ''
                }
            }else if(type=='image') {
                obj = {
                    'name': 'image_content',
                    'type': 'image',                    
                    'content': ''
                }
            }else if(type=='embed') {
                obj = {
                    'name': 'embeded_content',
                    'type': 'embed',                    
                    'content': ''
                }
            }

            this.$emit('addContentElement', obj)
        }
    }
}
</script>
<style lang="scss" scoped>
    .content_tools_block{
        position: sticky;
        top: 0;
        .head_bar{
            background-color: #eee;
            height: 32px;
            padding: 0 12px;
            cursor: pointer;
            font-size: 13px;

            & > div{
                height: 100%;
                align-items: center;
                gap: 10px;
            }
        }
        .grid > div{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 10px;
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            overflow: hidden;
            & > i{
                position: absolute;
                opacity: 0;
                font-size: 20px;
                color: #fff;
                z-index: 5;
                transition: all 0.4s;
            }
            & > div {
                font-size: 12px;
                font-weight: 600;
                color: #666;
                
                & > i{
                    color: #b0cddc;
                    font-size: 30px;
                }
            }
            &:hover{
                background-color: rgba(0, 0, 0, 0.6);
                & > i{
                    opacity: 1;
                }
            }
        }
    }
</style>