<template>
    <div class="image_input_block">
        <div v-if="$parent.formData[column]" class="image_preview_block">
            <img :src="$parent.formData[column]" />
            <div class="action_btn_block">
                <span class="remove" @click="$parent.formData[column]=''"><i class="fa fa-times"></i> Remove</span>
                <span class="change" @click="upload_image"><i class="fa fa-exchange-alt"></i> Change</span>
            </div>
        </div>
        <div v-else class="upload_image_block" @click="upload_image">
            <i class="fa fa-cloud-upload-alt fa-4x"></i>
            <div>
                <small>Click here to upload your prescriptin photo</small>
            </div>
        </div>
        <input class="image_file_input" type="file" ref="input_file" @change="load_image" accept="image/*" capture />
    </div>
</template>
<script>
export default {
    name: 'ImageInputBlock',
    props: ['column'],
    methods: {
        upload_image: function(){
            this.$refs.input_file.click();
        },
        load_image(e){
            var files = e.target.files || e.dataTransfer.files; 
            if (!files.length) return;
            console.log('File info', files[0]);
            this.CreateImage(files[0]);
        },
        CreateImage(file){
            var reader = new FileReader();

            reader.onload = (e) => {
                this.$parent.formData[this.column] = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
}
</script>
<style scoped>
    .image_file_input{
        display: none;
    }
    .image_preview_block{
        position: relative;
        background-color: #f7fff7;
        border: 1px solid #ddd;
        height: 200px;
        text-align: center;
        line-height: 200px;
    }
    .image_preview_block > img{
        max-width: 100%;
        max-height: 100%;
        padding: 5px;
        object-fit: contain;
    }
    .image_preview_block > .action_btn_block{
        position: absolute;
        width: 80%;
        left: 10%;
        bottom: 10px;
        line-height: 20px;
        text-align: center;
        z-index: 5;
    }
    .image_preview_block > .action_btn_block > span{
        display: inline-block;
        padding: 2px 14px 2px 10px;
        margin: 2px;
        cursor: pointer;
        background-color: #363;
        color: #fff;
        font-size: 10px;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .image_preview_block > .action_btn_block > span i{
        margin-right: 5px;
    }
    .image_preview_block > .action_btn_block > span:hover{
        background-color: rgb(34, 90, 112);
    }
    .image_preview_block > .action_btn_block > span.remove{
        background-color: #CD0000;
    }
    .image_preview_block > .action_btn_block > span.remove:hover{
        background-color: #ac2c2c;
    }
    .upload_image_block{
        width: 100%;
        height: 100%;
        background-color: #eee;
        border: 1px solid #ddd;
        text-align: center;
        padding: 20% 15px;
        cursor: pointer;
        transition: all 0.4s;
    }
    .upload_image_block:hover{
        background-color: #f7f7f7;
    }
    .upload_image_block > i{
        color: #ccc; margin-bottom: 10px;
    }
    .upload_image_block:hover > i{
        color: #cdcdcd;
    }
</style>