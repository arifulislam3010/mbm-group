<template>
    <div class="photo_content_block">
        <template v-if="data.length>0">
            <div class="view_photo">
              <img :src="get_url()" :title="data.product_title" :alt="data.product_title" />
            </div>
            <div class="thumb_list_block mt-3">
                <carousel
                    :paginationActiveColor="'#007499'"
                    :paginationColor="'#CCC'"
                    :paginationPadding="3"
                    :perPage="5"
                    :minSwipeDistance="16"
                    :loop="true">
                    <template v-for="(item,index) in data">
                        <slide v-if="item.product_photo_data" :key="index">
                            <div :class="['thumb_item',{active:sel_photo_index==index}]" @mouseover="sel_photo_index=index">
                                <img :src="item.product_photo_data.content" :title="data.product_title + '-thumb-' + index" :alt="data.product_title + '-thumb-' + index" />
                            </div>
                        </slide>
                    </template>
                </carousel>
            </div>
        </template>
        <div v-else class="photo_empty_block">
            <i class="fa fa-images"></i>
        </div>
    </div>
</template>
<script>
export default {
    name: 'PhotoContentBlock',
    props: ['data'],
    data() {
      return {
        sel_photo_index: 0
      }
    },
    mounted(){
      this.data.forEach((v,i) => {
        if(v.product_photo_data){
          this.sel_photo_index = i; return;
        }
      });
    },
    methods: {
      get_url: function(){
        return this.data[this.sel_photo_index].product_photo_data['content'];
      }
    }
}
</script>
<style scoped>
    .photo_content_block{
        display: block;
        height: 100%
    }
    .view_photo{
        width: 100%;
        height: 300px;
        background-color: #eee;
        border: 1px solid #ddd;
    }
    .view_photo > img{
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .thumb_item{
        display: block;
        width: 90px;
        height: 60px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .thumb_item.active{
        border-color: #CD0000;
    }
    .thumb_item > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .photo_empty_block{
        width: 100%;
        height: 100%;
        padding-top: 25%;
        background: #eee;
        text-align: center;
        font-size: 112px;
        color: #ccc;
    }
    .img-zoom-container {
        position: relative;
    }

    .img-zoom-lens {
        position: absolute;
        border: 1px solid #d4d4d4;
        /*set the size of the lens:*/
        width: 60px;
        height: 60px;
    }
    .img-zoom-result {
        border: 1px solid #d4d4d4;
        /*set the size of the result div:*/
        width: 500px;
        height: 500px;
    }
</style>
