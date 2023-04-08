<template>
    <div class="photo_content_block">
        <template v-if="data.length>0">
            <div class="view_photo">
              <template v-if="images">
                <ProductZoomer
                  :base-images="images"
                  :base-zoomer-options="zoomerOptions"
                />
              </template>
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
            images: '',
            zoomerOptions: {
              zoomFactor: 3,
              pane: "pane",
              hoverDelay: 300,
              namespace: "zoomer-left",
              move_by_click: false,
              scroll_items: 4,
              choosed_thumb_border_color: "#dd2c00",
              scroller_button_style: "line",
              scroller_position: "left",
              zoomer_pane_position: "right"
            }
        }
    },
    mounted(){
        this.data.forEach((v,i) => {
            if(v.product_photo_data){
                this.sel_photo_index = i; return;
            }
        });

        this.load_images()
    },
    methods: {
        load_images: function(){
          let thumbs = [], normal_size = [], large_size = []
          this.data.forEach((v,i) => {
            let obj = {
              id: v.product_photo_data.id,
              url: v.product_photo_data.content
            }

            thumbs.push(obj)
            normal_size.push(obj)
            large_size.push(obj)
          })

          this.images = {
            thumbs: thumbs,
            normal_size: normal_size,
            large_size: large_size
          }
        }
    }
}
</script>
<style lang="scss" scoped>
    .photo_content_block{
        display: block;
        height: 100%
    }
    .view_photo{
      width: 100%;
      /* height: 300px; */
      /* background-color: #eee; */
      /* border: 1px solid #ddd; */
      & > img{
        width: 100%; height: 100%;
        object-fit: contain;
      }
      :deep(.thumb-list){
        margin-right: 5px;
        img{
          max-width: 100%; object-fit: contain;
        }
      }
    }          
    .thumb_item{
      display: block;
      width: 90px;
      height: 60px;
      border: 1px solid #ddd;
      border-radius: 3px;
      &.active{
        border-color: #CD0000;
      }
      & > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
      }
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
