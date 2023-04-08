<template>
    <div class="icon_input_form_block">
        <div :class="['desktop_banner_image_block']">
            <div v-if="!bannerImageBrowse && $parent.formData.desktop_banner_image!=null">            
                <img :src="$parent.formData.desktop_banner_image" />
                <span class="browse_btn change_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Change</span>
            </div>
            <div v-else-if="!bannerImageBrowse">                
                <span class="browse_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>
            <vue-croppie v-show="bannerImageBrowse"
                ref="iconImageCroppie" 
                :enableOrientation="true"
                :viewport="{ width: 1110, height: 425 }"
                :showZoomer="true"
                :enableResize="true"
                @result="iconImageCroppie_result"
                @update="iconImageCroppie_update">
            </vue-croppie>
            <span v-if="bannerImageBrowse" class="action_btn cancel_btn" @click="cancel_upload_photo" title="Click for cancle"><i class="fa fa-times"></i></span>
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
            bannerImageBrowse: false,
        }
    },
    methods: {
        upload_photo: function(){
            this.$refs.icon_input_form.click();
        },        
        cancel_upload_photo: function(){      
            this.$parent.formData.desktop_banner_image = null
            this.bannerImageBrowse = false
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
                this.$parent.formData.desktop_banner_image = e.target.result;
                this.$refs.iconImageCroppie.bind({
                    url: e.target.result
                });
                this.bannerImageBrowse = true;
            };
            reader.readAsDataURL(file);
        },
        iconImageCrop() { 
            //bcvx
            // Here we are getting the result via callback function
            // and set the result to this.cropped which is being 
            // used to display the result above.
            let options = {
                format: 'png', 
                square: true
            }
            this.$refs.iconImageCroppie.result(options, (output) => {
                this.bannerImageCropped = output;
                this.$parent.formData.desktop_banner_image = this.bannerImageCropped;
            });
        },
        iconImageCroppie_result(output) {
            this.bannerImageCropped = output;
            this.$parent.formData.desktop_banner_image = this.bannerImageCropped;
        },
        iconImageCroppie_update(val) {
            //// console.log('Icon postion');
            // console.log(val);
            this.iconImageCrop();
        }
    }
}
</script>
<style lang="scss" scoped>    
    .desktop_banner_image_block{
        display: inline-block;
        width: 1170px;
        height: 485px;        
        position: relative;
        background-color: #eee;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        zoom: 51.5%;
        & > div{
            width: 100%; height: 100%; text-align: center;
        }    
        span.browse_btn{
            position: absolute;
            bottom: 40%;
            left: 0;
            right: 0;
            margin: 0 25%;
            display: inline-block;
            cursor: pointer;
            text-align: center;
            padding: 10px 8px;
            font-size: 18px;
            border-radius: 25px;
            background-color: rgba(0,0,0,0.50196);
            color: #ffffff;
            opacity: 0.4;
            transition: all 0.4s
        }

        &:hover span.browse_btn{
            background-color: purple;
            opacity: 1.0;
        }
        :deep(img){
            width: 1140px;
            height: 455px;
            object-fit: contain;
            /* padding: 5px; */
            border-radius: 3px;
            border: 1px solid #ddd;
            background-color: #fff;            
        }
        :deep(.action_btn){
            position: absolute;
            right: 0;
            bottom: 0px;
            width: 36px;
            height: 36px;
            text-align: center;
            line-height: 36px;
            background-color: #fff;
            border: 1px solid #ddd;
            color: #cd0000;
            font-size: 18px;
            cursor: pointer;
            border-radius: 50%;
            z-index: 5;
        }
        :deep(.cancel_btn){
            right: -10px;
            top: -10px;        
        }
    }
    input[type="file"]{
        display: none
    }
</style>