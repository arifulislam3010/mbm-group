<template>
    <div :class="['photo_content_fuller_block',{mobile_device:$device.isMobile}]">
        <!-- {{ product_type_photo_list }} -->
        <template v-if="product_photo_list.length>0">
            <div class="view_photo">
              <nuxt-img
                :src="get_url()"
                :title="$parent.data.product_title"
                :alt="$parent.data.product_title"
                sizes="sm:300px md:800px lg:1200px"
                format="webp"
              />
            </div>
            <div class="thumb_list_block mt-3">
                <carousel
                    :paginationActiveColor="'#007499'"
                    :paginationColor="'#CCC'"
                    :paginationPadding="3"
                    :perPage="20"
                    :minSwipeDistance="16"
                    :loop="true">
                    <template v-for="(item,index) in product_photo_list">
                        <slide v-if="item.product_photo_data" :key="index">
                            <div :class="['thumb_item',{active:sel_photo_index==index}]" @click="sel_photo_index=index">
                                <nuxt-img
                                  :src="item.product_photo_data.content"
                                  :title="$parent.data.product_title + '-thumb-' + index"
                                  :alt="$parent.data.product_title + '-thumb-' + index"
                                  sizes="sm:40px md:80px lg:120px"
                                />
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
        product_photo_list: [],
        sel_photo_index: 0
      }
    },
    watch: {
      product_type_photo_list: function(data){
        let getMainObj = JSON.parse(this.data)
        let getExtLen = this.product_photo_list.length - getMainObj.length
        this.product_photo_list.splice(getMainObj.length,getExtLen)
        // console.log('Get main obj', getMainObj)
        if(data.length>0){
          this.sel_photo_index = this.product_photo_list.length
          data.forEach( async v => {
            this.product_photo_list.push(v)
          })
        }else this.sel_photo_index = 0
      }
    },
    computed: {
      product_type_photo_list: function(){
        // console.log('ptp -info', this.$parent.product_type_photo_infos)
        return this.$parent.product_type_photo_infos
      }
    },
    mounted(){
      this.product_photo_list = JSON.parse(this.data)
      // this.product_photo_list.forEach( async (v,i) => {
      //   if(v.product_photo_data){
      //     this.sel_photo_index = i; return;
      //   }
      // });
    },
    methods: {
      get_url: function(){
        return this.product_photo_list[this.sel_photo_index].product_photo_data['content'];
      }
    }
}
</script>
<style lang="scss" scoped>
    .photo_content_fuller_block{
        display: block;
        position: absolute;
        left: 0; top: 0;
        height: 100%; width: 100%;

        .view_photo{
            width: 100%;
            height: 450px;
            background-color: #eee;
            /* border: 1px solid #ddd; */
            & > img{
                width: 100%; height: 100%;
                object-fit: cover;
                object-position: left;
            }
        }

        &.mobile_device{
            position: relative;

            .view_photo{
              height: 200px
            }
        }
    }    
    .thumb_list_block{
      display: block;
      :deep(.VueCarousel-inner){
        gap: 10px
      }
    }
    .thumb_item{
        display: block;
        width: 40px;
        height: 40px;
    }
    .thumb_item.active{
        border: 2px solid #444;
        border-radius: 3px;
    }
    .thumb_item > img{
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: -10px
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
</style>
