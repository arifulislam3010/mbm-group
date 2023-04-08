<template>
    <div class="product_photo_gallery_block">
        <label>Photos ({{ photo_arr.length }})</label>
        <div class="info_form_block mt-1">
            <div v-if="photo_arr.length==0" class="empty_block">
                <i class="fa fa-box-open fa-4x"></i>
                <div>No entry yet. Click on the button below to add one.</div>
            </div>
            <div v-else>
                <div class="info_list">
                    <carousel
                        :paginationActiveColor="'#8800cd'"
                        :paginationColor="'#CCC'"
                        :paginationPadding="3"
                        :perPage="4"
                        :minSwipeDistance="16"
                        :loop="true">
                        <slide v-for="(item,index) in photo_arr" :key="index">
                            <div class="photo_block">
                                <div class="img">
                                    <img :src="item.content" />
                                </div>
                                <div class="info">
                                    <div class="title">{{ item.content_title }}</div>
                                </div>
                                <span class="close_btn" @click="del_entry(index)">x</span>
                            </div>
                        </slide>
                    </carousel>
                </div>
            </div>
            <div class="add_btn_block">                
                <span @click="add_new_entry"><i class="fa fa-plus"></i> Add New Photo</span>
            </div>
        </div>
        
        <PopupBlock ref="popup_block" />
    </div>
</template>
<script>
import PopupBlock from './popup-block'
export default {
    name: 'ProductPhotoGalleryBlock',
    components: {
        PopupBlock
    },
    data(){
        return {
            photo_arr: this.$parent.formData.photo_infos
        }
    },
    computed: {        
    },
    methods: {        
        add_new_entry: function(){
            document.body.classList.add('popup_open');
            this.$refs.popup_block.popup_content_show = true
        },
        del_entry: function(index){
            // delete this.photo_arr[index]
            if(confirm('Are you sure to delete it?')) this.photo_arr.splice(index, 1)
        }
    }
}
</script>
<style scoped>
    .info_form_block{
        display: block;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .info_list{
        display: block;        
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }
    .photo_block{
        display: block; position: relative;
        width: 100%;
        border: 1px solid #ddd
    }    
    .photo_block .close_btn{
        position: absolute;
        width: 20px; height: 20px; line-height: 18px;
        right: 5px; top: -5px; text-align: center;
        font-size: 12px;
        border: 1px solid #ddd;
        border-radius: 50%;
        background-color: #fff;
        color: #8800cd;
        cursor: pointer;
        transition: all 0.4s;
    }
    .photo_block .close_btn:hover{        
        color: #CD0000;
    }
    .photo_block .img{
        display: block;
        background-color: #eee;
        width: 100%;
        height: 100px;
    }
    .photo_block .img > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .photo_block .info{
        display: flex; height: 50px;
        border-top: 1px solid #ddd;
        padding: 5px 8px; text-align: center;
    }
    .photo_block .info > div{        
        align-self: center;
        margin: auto;
    }
    .empty_block{
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    .empty_block > i{
        margin: 5px 0 15px;
        color: #ccc;
    }    
    .add_btn_block{
        padding: 10px 15px;        
        text-align: center;
    }    
    .add_btn_block > span{
        display: block;
        font-size: 12px; font-weight: bold; color: blueviolet;
        text-transform: uppercase; cursor: pointer;
        transition: all 0.4s;
    }
    .add_btn_block > span:hover{
        color: #006699;
    }
</style>