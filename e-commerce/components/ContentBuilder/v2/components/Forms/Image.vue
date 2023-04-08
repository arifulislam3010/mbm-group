<template>
    <div class="image_content_block">
        <div :class="['input_box','upload_box']">
            <!-- <div v-if="!preview" class="overlay_shade"></div> -->
            <div v-if="data.content" class="img_area">
                <img :src="data.content" />
                <span v-if="!preview" class="change_btn" tabindex="0" role="button" @click="open_file_manager">Change</span>
            </div>
            <div v-else class="browse_area" @click="open_file_manager">
                <div class="icon"><i class="fa fa-arrow-up"></i></div>                
                <p>Choose from File Manager</p>
            </div>
            <!-- <input type="file" ref="photo_input_form" class="hidden" @change="load_image" accept="image/jpeg,image/png" /> -->
        </div>
        <!-- {{ photo_arr }} -->
        <PopupBlock ref="popup_block" />
    </div>
</template>
<script>
import PopupBlock from '../popup-block'
import { mapMutations } from 'vuex'
export default {
    name: 'ImageBlock',
    props: {
        data: Object,
        index: Number,
        preview: Boolean
    },
    components: {
        PopupBlock
    },
    data(){
        return {
            photo_arr: []
        }
    },
    watch: {
        // photo_arr: function(obj) {
        //     alert('')
        //     this.data.content = obj
        // }
        photo_arr: {
            handler(obj){
                alert('')
                this.data.content = obj.content
            },
            deep: true
        }
    },
    methods: {        
        ...mapMutations({
            content_builder_media_gallery: 'CONTENT_BUILDER_MEDIA_GALLERY'
        }),
        open_file_manager: function(){
            document.body.classList.add('popup_open');
            this.content_builder_media_gallery(true)
            this.$refs.popup_block.popup_content_show = true
        }
    }
}
</script>
<style lang="scss" scoped> 
    .image_content_block{
        display: block;
        position: relative;
        & > .input_box{
            input{
                pointer-events: none;
                padding: 2px 8px;
                width: 100%; border: 1px solid #ddd;
                border-radius: 5px;

                &[type="file"]{
                    display: none;
                }
            }

            &.preview input{
                pointer-events: all;
            }
        }

        & > .upload_box{
            display: block;
            position: relative;            
            border-radius: 5px;
            cursor: pointer;

            & > .overlay_shade{
                position: absolute;
                left: 0; top: 0; width: 100%;
                background-color: #ffffff80;
                height: 100%; z-index: 1;
            }

            & > *{
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                gap: 15px
            }

            & > .img_area{
                position: relative;
                padding: 20px;
                border: 1px dashed #ddd;
                text-align: center;
                border-radius: 5px;

                & > img{
                    width: 100%; height: 100%;
                    object-fit: contain;
                }
                & > .change_btn{
                    position: absolute;
                    display: inline-block;
                    background-color: #09986780;
                    color: #fff;
                    font-size: 12px;
                    padding: 3px 20px;
                    border-radius: 25px;
                    cursor: pointer;
                    transition: all 0.4s;
                    bottom: 20px; left: 35%;
                    z-index: 5;
                    &:hover{
                        background-color: #099867;
                    }
                }
            }

            & > .browse_area{
                position: relative;
                padding: 15px;
                border: 1px dashed #ddd;
                text-align: center;
                border-radius: 5px;
                // z-index: 5;

                & > .icon{
                    display: inline-block;
                    width: 60px; height: 60px;
                    line-height: 60px;
                    text-align: center;
                    color: #099867;
                    font-size: 18px;                            
                    border: 1px solid #ddd;
                    border-radius: 50%;
                }

                & > p{
                    width: calc(100% - 75px);
                    text-align: left;
                    font-size: 12px;
                    color: #444
                }
            }
        }
    }
</style>