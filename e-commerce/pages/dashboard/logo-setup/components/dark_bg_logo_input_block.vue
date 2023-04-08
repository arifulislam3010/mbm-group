<template>
    <div class="dark_bg_logo_input_form_block">
        <div :class="['company_dark_bg_logo_block']">
            <div v-if="!darkLogoImageBrowse && $parent.formData.dark_logo!=null">            
                <img :src="$parent.formData.dark_logo" />
                <span class="browse_btn change_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Change</span>
            </div>
            <div v-else-if="!darkLogoImageBrowse">                
                <span class="browse_btn" @click="upload_photo"><i class="fa fa-cloud-upload-alt"></i> Upload</span>
            </div>
            <vue-croppie v-show="darkLogoImageBrowse"
                ref="darkLogoImageCroppie" 
                :enableOrientation="true"
                :viewport="{ width: 215, height: 90 }"
                :showZoomer="true"
                :enableResize="true"
                @result="darkLogoImageCroppie_result"
                @update="darkLogoImageCroppie_update">
            </vue-croppie>
            <span v-if="darkLogoImageBrowse" class="action_btn cancel_btn" @click="cancel_upload_photo" title="Click for cancle"><i class="fa fa-times"></i></span>
        </div>
        <input type="file" accept="image/*" ref="dark_bg_logo_input_form" @change="load_image" />
    </div>
</template>
<script>
export default {
    name: 'IconInputFormBlock',
    data(){
        return {
            darkLogoImageCropped: null,
            darkLogoImageBrowse: false,
        }
    },
    methods: {
        upload_photo: function(){
            this.$refs.dark_bg_logo_input_form.click();
        },        
        cancel_upload_photo: function(){      
            this.$parent.formData.dark_logo = null
            this.darkLogoImageBrowse = false
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
                this.$parent.formData.dark_logo = e.target.result;
                this.$refs.darkLogoImageCroppie.bind({
                    url: e.target.result
                });
                this.darkLogoImageBrowse = true;
            };
            reader.readAsDataURL(file);
        },
        darkLogoImageCrop() { 
            //bcvx
            // Here we are getting the result via callback function
            // and set the result to this.cropped which is being 
            // used to display the result above.
            let options = {
                format: 'png', 
                square: true
            }
            this.$refs.darkLogoImageCroppie.result(options, (output) => {
                this.darkLogoImageCropped = output;
                this.$parent.formData.dark_logo = this.darkLogoImageCropped;
            });
        },
        darkLogoImageCroppie_result(output) {
            this.darkLogoImageCropped = output;
            this.$parent.formData.dark_logo = this.darkLogoImageCropped;
        },
        darkLogoImageCroppie_update(val) {
            // console.log('Icon postion');
            // console.log(val);
            this.darkLogoImageCrop();
        }
    }
}
</script>
<style lang="scss" scoped>    
    .company_dark_bg_logo_block{
        display: inline-block;
        width: 275px;
        height: 150px;        
        position: relative;
        background-color: #eee;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .company_dark_bg_logo_block > div{
        width: 100%; height: 100%; text-align: center;
    }    
    .company_dark_bg_logo_block span.browse_btn{
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

    .company_dark_bg_logo_block:hover span.browse_btn{
        background-color: purple;
        opacity: 1.0;
    }
    .company_dark_bg_logo_block :deep(img){
        width: 145px;
        height: 120px;
        object-fit: contain;
        /* padding: 5px; */
        border-radius: 3px;
        border: 1px solid #ddd;
        background-color: #fff;
    }
    .company_dark_bg_logo_block :deep(.action_btn){
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
    .company_dark_bg_logo_block :deep(.cancel_btn){
        right: -10px;
        top: -10px;        
    }
    input[type="file"]{
        display: none
    }
</style>