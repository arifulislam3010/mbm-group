<template>
    <div class="photo_input_form_block">
        <div :class="['gallery_photo_block']">
            <div v-if="!photoImageBrowse && $parent.formData.content!=null">
                <img :src="$parent.formData.content" />
                <span class="browse_btn change_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Change</span>
            </div>
            <div v-else-if="!photoImageBrowse">
                <span class="browse_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>
            <vue-croppie v-show="photoImageBrowse"
                ref="photoImageCroppie"
                :enableOrientation="true"
                :viewport="{ width: 1700, height: 600 }"
                :showZoomer="true"
                :enableResize="true"
                @result="photoImageCroppie_result"
                @update="photoImageCroppie_update">
            </vue-croppie>
            <span v-if="photoImageBrowse" class="action_btn cancel_btn" @click="cancel_upload_photo" title="Click for cancle"><i class="fa fa-times"></i></span>
        </div>
        <input type="file" ref="photo_input_form" @change="load_image" accept="image/*" capture />
    </div>
</template>
<script>
export default {
    name: 'LogoInputFormBlock',
    data(){
        return {
            photoImageCropped: null,
            photoImageBrowse: false,
        }
    },
    methods: {
        upload_photo: function(){
            this.$refs.photo_input_form.click();
        },
        cancel_upload_photo: function(){
            this.$parent.formData.content = null
            this.photoImageBrowse = false
        },
        load_image(e)
        {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) return;
            console.log('File info', files[0]);
            this.get_file_name(files[0].name);
            this.CreateImage(files[0]);
        },
        get_file_name(file){
            this.$parent.formData.content_title = file.split('.').slice(0, -1).join('.')
        },
        CreateImage(file)
        {
            var image = new Image();
            var reader = new FileReader();

            reader.onload = (e) => {
                this.$parent.formData.content = e.target.result;
                this.$refs.photoImageCroppie.bind({
                    url: e.target.result
                });
                this.photoImageBrowse = true;
            };
            reader.readAsDataURL(file);
        },
        photoImageCrop() {
            //bcvx
            // Here we are getting the result via callback function
            // and set the result to this.cropped which is being
            // used to display the result above.
            let options = {
                format: 'png',
                square: true
            }
            this.$refs.photoImageCroppie.result(options, (output) => {
                this.photoImageCropped = output;
                this.$parent.formData.content = this.photoImageCropped;
            });
        },
        photoImageCroppie_result(output) {
            this.photoImageCropped = output;
            this.$parent.formData.content = this.photoImageCropped;
        },
        photoImageCroppie_update(val) {
            //// console.log('Logo postion');
            // console.log(val);
            this.photoImageCrop();
        }
    }
}
</script>
<style scoped>
    .gallery_photo_block{
        display: inline-block;
        width: 1760px;
        height: 660px;
        position: relative;
        background-color: #eee;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        zoom: 0.338;
    }
    .gallery_photo_block > div{
        width: 100%; height: 100%; text-align: center;
    }
    .gallery_photo_block span.browse_btn{
        position: absolute;
        bottom: 40%;
        left: 0;
        right: 0;
        margin: 0 25%;
        display: inline-block;
        cursor: pointer;
        text-align: center;
        padding: 5px 8px;
        font-size: 12px;
        border-radius: 25px;
        background-color: rgba(0,0,0,0.50196);
        color: #ffffff;
        opacity: 0.4;
        transition: all 0.4s;
        zoom: 3
    }

    .gallery_photo_block:hover span.browse_btn{
        background-color: purple;
        opacity: 1.0;
    }
    .gallery_photo_block :deep(img){
        width: 1730px;
        height: 630px;
        object-fit: contain;
        /* padding: 5px; */
        border-radius: 3px;
        border: 1px solid #ddd;
        background-color: #fff;
    }
    .gallery_photo_block :deep(.action_btn){
        position: absolute;
        right: 0;
        bottom: 0px;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        background-color: #fff;
        border: 1px solid #ddd;
        color: #00344d;
        font-size: 10px;
        cursor: pointer;
        border-radius: 50%;
        z-index: 1;
        zoom: 3;
    }
    .gallery_photo_block :deep(.cancel_btn){
        right: -10px;
        top: -10px;
    }
    input[type="file"]{
        display: none
    }
</style>
