<template>
    <div class="icon_input_form_block">
        <div :class="['banner_image_block']">
            <div v-if="!iconImageBrowse && $parent.formData.banner_image!=null">            
                <img :src="$parent.formData.banner_image" />
                <span class="browse_btn change_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Change</span>
            </div>
            <div v-else-if="!iconImageBrowse">                
                <span class="browse_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>
            <vue-croppie v-show="iconImageBrowse"
                ref="bannerImageCroppie" 
                :enableOrientation="true"
                :viewport="{ width: 1700, height: 567 }"
                :showZoomer="true"
                :enableResize="true"
                @result="bannerImageCroppie_result"
                @update="bannerImageCroppie_update">
            </vue-croppie>
            <span v-if="iconImageBrowse" class="action_btn cancel_btn" @click="cancel_upload_photo" title="Click for cancle"><i class="fa fa-times"></i></span>
        </div>
        <input type="file" accept="image/*" ref="icon_input_form" @change="load_image" />
    </div>
</template>
<script>
export default {
    name: 'IconInputFormBlock',
    data(){
        return {
            bannerImageCropped: null,
            iconImageBrowse: false,
        }
    },
    methods: {
        upload_photo: function(){
            this.$refs.icon_input_form.click();
        },        
        cancel_upload_photo: function(){      
            this.$parent.formData.banner_image = null
            this.iconImageBrowse = false
        },
        load_image(e) 
        {
            var files = e.target.files || e.dataTransfer.files; 
            if (!files.length) return;
            this.CreateImage(files[0]);
        },
        CreateImage(file) 
        {
            var image = new Image();
            var reader = new FileReader();

            reader.onload = (e) => {
                this.$parent.formData.banner_image = e.target.result;
                this.$refs.bannerImageCroppie.bind({
                    url: e.target.result
                });
                this.iconImageBrowse = true;
            };
            reader.readAsDataURL(file);
        },
        bannerImageCrop() { 
            //bcvx
            // Here we are getting the result via callback function
            // and set the result to this.cropped which is being 
            // used to display the result above.
            let options = {
                format: 'png', 
                square: true
            }
            this.$refs.bannerImageCroppie.result(options, (output) => {
                this.bannerImageCropped = output;
                this.$parent.formData.banner_image = this.bannerImageCropped;
            });
        },
        bannerImageCroppie_result(output) {
            this.bannerImageCropped = output;
            this.$parent.formData.banner_image = this.bannerImageCropped;
        },
        bannerImageCroppie_update(val) {
            //// console.log('Icon postion');
            // console.log(val);
            this.bannerImageCrop();
        }
    }
}
</script>
<style lang="scss" scoped>    
    .banner_image_block{
        display: inline-block;
        width: 1720px;
        height: 587px;        
        position: relative;
        background-color: #eee;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        zoom: 35%;
    }
    .banner_image_block > div{
        width: 100%; height: 100%; text-align: center;
    }    
    .banner_image_block span.browse_btn{
        position: absolute;
        bottom: 40%;
        left: 0;
        right: 0;
        margin: 0 25%;
        display: inline-block;
        cursor: pointer;
        text-align: center;
        padding: 10px 15px;
        font-size: 32px;
        border-radius: 25px;
        background-color: rgba(0,0,0,0.50196);
        color: #ffffff;
        opacity: 0.4;
        transition: all 0.4s
    }

    .banner_image_block:hover span.browse_btn{
        background-color: purple;
        opacity: 1.0;
    }
    .banner_image_block :deep(img){
        width: 1690px;
        height: 557px;
        object-fit: contain;
        /* padding: 5px; */
        border-radius: 3px;
        border: 1px solid #ddd;
        background-color: #fff;
    }
    .banner_image_block :deep(.action_btn){
        position: absolute;
        right: 0;
        bottom: 0px;
        width: 48px;
        height: 48px;
        text-align: center;
        line-height: 48px;
        background-color: #fff;
        border: 1px solid #ddd;
        color: #CD0000;
        font-size: 24px;
        cursor: pointer;
        border-radius: 50%;
    }
    .banner_image_block :deep(.cancel_btn){
        right: -10px;
        top: -10px;        
    }
    input[type="file"]{
        display: none
    }
</style>