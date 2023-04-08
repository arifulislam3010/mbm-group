<template>
    <div class="watermark_logo_input_form_block">
        <div :class="['company_watermark_logo_block']">
            <div v-if="!watermarkLogoImageBrowse && $parent.formData.watermark_logo!=null">            
                <img :src="$parent.formData.watermark_logo" />
                <span class="browse_btn change_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Change</span>
            </div>
            <div v-else-if="!watermarkLogoImageBrowse">                
                <span class="browse_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>
            <vue-croppie v-show="watermarkLogoImageBrowse"
                ref="watermarkLogoImageCroppie" 
                :enableOrientation="true"
                :viewport="{ width: 90, height: 90 }"
                :showZoomer="true"
                :enableResize="true"
                @result="watermarkLogoImageCroppie_result"
                @update="watermarkLogoImageCroppie_update">
            </vue-croppie>
            <span v-if="watermarkLogoImageBrowse" class="action_btn cancel_btn" @click="cancel_upload_photo" title="Click for cancle"><i class="fa fa-times"></i></span>
        </div>
        <input type="file" accept="image/*" ref="watermark_logo_input_form" @change="load_image" />
    </div>
</template>
<script>
export default {
    name: 'IconInputFormBlock',
    data(){
        return {
            watermarkLogoImageCropped: null,
            watermarkLogoImageBrowse: false,
        }
    },
    methods: {
        upload_photo: function(){
            this.$refs.watermark_logo_input_form.click();
        },        
        cancel_upload_photo: function(){      
            this.$parent.formData.watermark_logo = null
            this.watermarkLogoImageBrowse = false
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
                this.$parent.formData.watermark_logo = e.target.result;
                this.$refs.watermarkLogoImageCroppie.bind({
                    url: e.target.result
                });
                this.watermarkLogoImageBrowse = true;
            };
            reader.readAsDataURL(file);
        },
        watermarkLogoImageCrop() { 
            //bcvx
            // Here we are getting the result via callback function
            // and set the result to this.cropped which is being 
            // used to display the result above.
            let options = {
                format: 'png', 
                square: true
            }
            this.$refs.watermarkLogoImageCroppie.result(options, (output) => {
                this.watermarkLogoImageCropped = output;
                this.$parent.formData.watermark_logo = this.watermarkLogoImageCropped;
            });
        },
        watermarkLogoImageCroppie_result(output) {
            this.watermarkLogoImageCropped = output;
            this.$parent.formData.watermark_logo = this.watermarkLogoImageCropped;
        },
        watermarkLogoImageCroppie_update(val) {
            //// console.log('Icon postion');
            // console.log(val);
            this.watermarkLogoImageCrop();
        }
    }
}
</script>
<style lang="scss" scoped>    
    .company_watermark_logo_block{
        display: inline-block;
        width: 150px;
        height: 150px;        
        position: relative;
        background-color: #eee;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .company_watermark_logo_block > div{
        width: 100%; height: 100%; text-align: center;
    }    
    .company_watermark_logo_block span.browse_btn{
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
        transition: all 0.4s
    }

    .company_watermark_logo_block:hover span.browse_btn{
        background-color: purple;
        opacity: 1.0;
    }
    .company_watermark_logo_block :deep(img){
        width: 120px;
        height: 120px;
        object-fit: contain;
        /* padding: 5px; */
        border-radius: 3px;
        border: 1px solid #ddd;
        background-color: #fff;
    }
    .company_watermark_logo_block :deep(.action_btn){
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
    }
    .company_watermark_logo_block :deep(.cancel_btn){
        right: -10px;
        top: -10px;        
    }
    input[type="file"]{
        display: none
    }
</style>