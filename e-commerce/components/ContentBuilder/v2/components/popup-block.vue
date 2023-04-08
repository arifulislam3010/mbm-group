<template>
    <div v-show="popup_content_show" class="black_overlay">
        <div class="white_overlay">
            <label>Media Content Form</label>                
            <span class="close_btn" @click="popup_close">x</span>
            <div class="content">
                <PhotoGalleryContent :content_type="'photo'" :selected_arr="$parent.photo_arr" :view_others="get_role_access && get_role_access.view_others" ref="popup_content_block" />
            </div>
        </div>
    </div>
</template>
<script>
import PhotoGalleryContent from '@/pages/dashboard/media-galleries/components/galley-content-view'
export default {
    name: 'PopupBlock',
    components: {
        PhotoGalleryContent
    },
    data(){
        return {
            popup_content_show: false
        }
    },
    computed: {
        get_role_access: function(){
            return this.$parent.$parent.$parent.$parent.$parent.role_access
        }
    },
    methods: {
        popup_close: function(){
            this.popup_content_show=false
            this.$parent.content_builder_media_gallery(false)
            document.body.classList.remove('popup_open')
        },
        add_content: function(obj){            
            this.$parent.photo_arr[0] = obj
            this.$parent.data.content = obj.content
        }
    }
}
</script>
<style scoped>
    .white_overlay{
        width: 80%;
        left: 10%;
        top: 10%;
        min-height: 450px;
        /* max-height: 100%; */
    }
    .content{
        display: block;
        width: 100%;
        height: 100%;
    }
</style>