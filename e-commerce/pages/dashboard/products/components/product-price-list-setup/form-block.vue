<template>
    <div class="product_price_form_block">
        <div class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label>Choose Price Type</label>
                    <div class="mb-4">
                        <!-- <pre>{{ $parent.$parent.price_arr[cur_index] }}</pre> -->
                        <select v-model="$parent.$parent.price_arr[cur_index].id" class="form-control" @change="$parent.$parent.select_price_type(cur_index)">
                            <option value="">Select One</option>
                            <template v-if="Object.keys($parent.$parent.product_price_types).length>0 || $parent.$parent.product_price_types.length>0">
                                <option v-for="(item,id) in $parent.$parent.product_price_types" :key="item.index" :value="id">{{ item.title }}</option>
                            </template>
                        </select>
                    </div>
                </div>
                <template v-if="$parent.$parent.product_type_info_arr.length > 0">
                  <div v-for="(product_type,product_index) in $parent.$parent.product_type_info_arr" class="col-md-4" :key="product_index">
                    <template v-if="$parent.$parent.price_arr[cur_index].product_type_infos[product_type.id]">
                      <label>Choose {{ product_type.title }}</label>
                      <div class="mb-4">
                        <select v-model="$parent.$parent.price_arr[cur_index].product_type_infos[product_type.id].index" class="form-control" @change="$parent.$parent.select_product_type(cur_index,product_index,product_type.id)">
                          <option value="">Select One</option>
                          <template v-if="product_type.type_name.length>0">
                            <option v-for="(content,index) in product_type.type_name" :key="index" :value="index">{{ content }}</option>
                          </template>
                        </select>
                      </div>
                    </template>
                  </div>
                </template>                
                <div class="col-md-2">
                  <label>Cart Qty. Mode</label>
                  <div class="mb-4">
                    <select v-model="$parent.$parent.price_arr[cur_index].cart_qty_mode" class="form-control">
                      <option value="1">Fixed</option>
                      <option value="2">Open</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <label>Unit Price ({{ $store.state.currency_info.title }})</label>
                  <div class="mb-4">
                    <input v-model="$parent.$parent.price_arr[cur_index].unit_price" class="form-control" type="number" min="0" step="0.1" />
                  </div>
                </div>                
                <template v-if="$parent.$parent.price_arr[cur_index].cart_qty_mode==2">
                  <div class="col-md-2">
                      <label>Minimum Order</label>
                      <div class="mb-4">
                          <input v-model="$parent.$parent.price_arr[cur_index].min_qty" placeholder="E.g. 1" class="form-control" type="number" min="1" step="1" />
                      </div>
                  </div>
                  <div class="col-md-2">
                      <label>Maximum Order</label>
                      <div class="mb-4">
                          <input v-model="$parent.$parent.price_arr[cur_index].max_qty" placeholder="E.g. 1000" class="form-control" type="number" min="1" step="1" />
                      </div>
                  </div>
                </template>
                <div class="col-md-4" v-if="$parent.$parent.price_arr[cur_index].title && $parent.$parent.price_arr[cur_index].price_infos.length > 0">
                  <label>Price List</label>
                  <div class="mb-4">
                    <select class="form-control">
                      <option v-for="(item,key) in $parent.$parent.price_arr[cur_index].price_infos" :key="'pi-' + key">Quantity {{ item.quantity }} ({{ $store.state.currency_info.title }} <template v-if="item.price_type==2">{{ item.price?item.price:'-' }}</template><template v-else>{{ $parent.$parent.price_arr[cur_index].unit_price - (($parent.$parent.price_arr[cur_index].unit_price * item.price) / 100) }}</template>/{{ $parent.$parent.price_arr[cur_index].title }}<template v-if="item.business_day">/{{ item.business_day }} Business day</template>)</option>
                    </select>
                  </div>
                </div>
                <template v-if="Object.keys($parent.$parent.product_color_info_arr).length > 0">
                  <div :class="[{'col-md-4':$parent.$parent.price_arr[cur_index].cart_qty_mode==1},{'col-md-8':$parent.$parent.price_arr[cur_index].cart_qty_mode==2}]">
                    <label>Choose color</label>
                    <div class="color_list_block mb-4">
                      <div :class="['item',{active:$parent.$parent.price_arr[cur_index].color_type_id==[item.id]}]" v-for="(item,index) in $parent.$parent.product_color_info_arr" :key="index" :title="item.color_title" @click="$parent.$parent.select_color_item(cur_index,item)">
                          <i class="fa fa-check"></i>
                          <div class="bg_color" :style="{'background-color':item.color_code}"></div>
                      </div>
                    </div>                    
                  </div>
                </template>
                <div :class="[{'col-md-4':$parent.$parent.price_arr[cur_index].cart_qty_mode==1},{'col-md-8':$parent.$parent.price_arr[cur_index].cart_qty_mode==2}]">
                    <label>Remarks</label>
                    <div class="text_editor_block">
                        <textarea-autosize
                            placeholder="Enter price remarks"
                            v-model="$parent.$parent.price_arr[cur_index].remarks"
                            :min-height="30"
                            :max-height="150"
                        />
                    </div>
                </div>
            </div>
            <div v-if="$parent.$parent.price_arr[cur_index].title" class="price_list_open_btn mt-4" @click="$parent.$parent.price_form_block_show=true">
              <i class="fa fa-plus"></i>
              <span>Setup Multiple Price with Quantity</span>
            </div>
            <div class="photo_management_block mt-2">
                <ProductPhotoGallery :product_photo_infos="$parent.$parent.price_arr[cur_index].photo_infos" ref="product_photo_list" />
            </div>
        </div>
    </div>
</template>
<script>
import ProductPhotoGallery from '../product-photo-gallery/index'
export default {
    name: 'ProductPriceFormBlock',
    props: ['data','cur_index'],
    components: {
        ProductPhotoGallery
    },
    methods: {
        replace_update_sort_list(type,data){
            if(type=='photo') this.$parent.$parent.price_arr[this.cur_index].photo_infos = data
        },
    }
}
</script>
<style lang="scss" scoped>
    .product_price_form_block{
        display: block;
        padding: 15px; background-color: $light_ash;
        border-bottom: 1px solid $default_border_color;
        transition: all 0.4s;

        .text_editor_block{
            background-color: $white;
        }
        textarea{
            width: 100%; border: none; outline: none; padding: 10px;
        }

        .price_list_open_btn{
            display: flex;
            align-items: center;
            height: 40px;
            background-color: $white;
            border: 1px solid $default_border_color;
            border-radius: 5px;
            justify-content: center;
            gap: 10px;
            color: blueviolet;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.4s;

            &:hover{
                background-color: $white;
                color: #006699
            }
        }
        .photo_management_block{
            background-color: $white;
            padding: 15px;
            border: 1px solid $default_border_color;
            border-radius: 5px;
        }
        .color_list_block{
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          align-items: center;

          & > .item{
              display: flex;            
              position: relative;
              width: 36px; height: 36px;
              overflow: hidden;
              cursor: pointer;
              box-shadow: 0 0 10px #ccc;
              border-radius: 50%;
              opacity: 0.4;
              transition: all 0.4s;

              & > *{
                  position: absolute;
                  width: 100%; height: 100%;                                
              }              

              & > i{
                  opacity: 0;
                  color: cyan;
                  mix-blend-mode: difference;
                  text-align: center;
                  line-height: 36px;
                  z-index: 5;
                  transition: all 0.4s;
              }

              &.active{
                opacity: 1.0;
                & > i{
                  opacity: 1.0;
                }
              }
              &:hover{
                opacity: 1.0;
              }
          }
        }
    }
</style>
