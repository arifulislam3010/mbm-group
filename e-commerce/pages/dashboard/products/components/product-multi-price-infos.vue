<template>
    <div class="get_product_price_list_block">
        <div class="label"><b>Price :</b></div>
        <div :class="['price_item_list',{hide:!show_all && (index + 1) > show_limit}]" v-for="(item,index) in price_info" :key="index">
          <!-- <pre>{{ item }}</pre> -->
          <div class="price_type_block">
            <template v-for="(pv,pi) in item.product_price_contents">
                <template v-if="pv.product_price_type_data && pv.product_type_data">
                  {{ pv.product_type_data.type_title}} ({{ pv.product_type_name_data?pv.product_type_name_data.type_name:'Unknown' }}) /
                  <template v-if="(pi+1)==item.product_price_contents.length">{{ pv.product_price_type_data.type_title }}</template>
                </template>
            </template>
          </div>
          <div v-if="item.product_price_contents[0]" class="mt-1">
              <!-- <div>Regular: <em>Tk.{{ item.price }}</em></div>
              <div v-if="item.discount_price">Discount: <em>Tk.{{ item.discount_price }}</em></div> -->
              <select class="price_list_block">
                  <template v-for="(pv,pi) in JSON.parse(item.price_info)">
                      <option v-if="pv.price" :key="pi">Quantity {{ pv.quantity }} ({{ $store.state.currency_info.title }} <template v-if="pv.price_type==2">{{ pv.price?pv.price:'-' }}</template><template v-else>{{ item.unit_price - ((item.unit_price * pv.price) / 100) }}</template> / {{ item.product_price_contents[0].product_price_type_data.type_title }}<template v-if="pv.business_day">/{{ pv.business_day }} Business day</template>)</option>
                  </template>
              </select>
          </div>
        </div>
        <div v-if="price_info.length > show_limit" class="more_action_btn">
          <div>
            <div v-if="show_all" @click="show_all=false">
              <span>Less</span>
              <i class="fa fa-angle-up"></i>
            </div>
            <div v-else @click="show_all=true">
              <span>More</span>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'GetProductPriceListBlock',
    props: ['price_info'],
    data(){
      return {
        show_limit: 2,
        show_all: false
      }
    }
}
</script>
<style lang="scss" scoped>
    .get_product_price_list_block{
        display: block;
    }
    .get_product_price_list_block{
      & > .label{
          border-bottom: 1px solid #ccc;
          white-space: normal;
      }
      & > div{
        display: block;
        padding: 1px 0;
        margin: 1px 0;
        font-size: 12px;

        &.hide{
          display: none;
        }

        &.more_action_btn{
          display: block; text-align: right;
          margin-top: 3px;
          & > div{
            display: inline-block;
            cursor: pointer; color: #666;
            transition: all 0.4s;

            & > div{
              display: flex; align-items: center; gap: 3px
            }
            &:hover{
              color: $sys_brand_color
            }
          }
        }

        & > div {
          select.price_list_block{
            display: block;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 2px 5px;
            outline: none
          }
        }
      }
    }
</style>
