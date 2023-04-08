<template>
    <div :class="['product_price_entry_form_block',{mobile_device:$device.isMobile}]">
        <div class="black_mask_overlay" @click="close_price_form_block"></div>
        <div class="content clearfix">

            <table class="table">
              <thead>
                <tr>
                  <th rowspan="2">#</th>
                  <th rowspan="2">Quantity</th>
                  <!-- <th colspan="2"><template v-if="$parent.price_arr[cur_index].title">Less price {{ $parent.price_arr[cur_index].title }}</template> (%)</th> -->
                  <th colspan="3"><template v-if="$parent.price_arr[cur_index].title">Price setup ({{ $parent.price_arr[cur_index].title }})</template></th>
                  <!-- <th rowspan="2"><template v-if="$parent.price_arr[cur_index].title">Less price {{ $parent.price_arr[cur_index].title }}</template> (%)</th> -->
                  <th rowspan="2">Within Delivery</th>
                  <th rowspan="2">Hour(s)<br>in<br>Dhaka</th>
                  <th rowspan="2"></th>
                </tr>
                <tr>
                  <th>Type</th>
                  <th>Regular</th>
                  <th>Discount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item,index) in $parent.price_arr[cur_index].price_infos" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>
                    <input type="number" min="0" step="1" v-model="item.quantity" placeholder="i.e. 1000" />
                    <div :class="['recommended_block','mt-1',{active:item.recommended}]" @click="recommendedPriceInfo(item.recommended, cur_index, index)">
                      <i v-if="item.recommended" class="far fa-check-circle"></i>
                      <i v-else class="far fa-circle"></i>
                      <span>Recommended</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <select v-model="item.price_type">
                        <option value="1">%</option>
                        <option value="2">{{ $store.state.currency_info.title }}</option>
                      </select>
                    </div>
                    <div :class="['re_price_block','mt-1']">
                      <template v-if="item.price_type==1">Less price</template>
                      <template v-else-if="item.price_type==2">Fixed price</template>
                      <template v-else>Less price</template>
                    </div>
                  </td>
                  <td>
                    <div>
                      <input type="number" min="0" step="0.01" v-model="item.price" placeholder="i.e. 10" />
                    </div>
                    <div :class="['re_price_block','mt-1']">{{ $store.state.currency_info.title }} <template v-if="item.price_type==2">{{ item.price?item.price:'-' }}</template><template v-else>{{ $parent.price_arr[cur_index].unit_price - (($parent.price_arr[cur_index].unit_price * item.price)/100).toFixed(2) }}</template></div>
                  </td>
                  <td>
                    <div>
                      <input type="number" min="0" step="0.01" v-model="item.discount_price" placeholder="i.e. 10" />
                    </div>
                    <div :class="['re_price_block','mt-1']">{{ $store.state.currency_info.title }} <template v-if="item.price_type==2">{{ item.discount_price?item.discount_price:'-' }}</template><template v-else>{{ $parent.price_arr[cur_index].unit_price - (($parent.price_arr[cur_index].unit_price * item.discount_price)/100).toFixed(2) }}</template></div>
                  </td>
                  <td>
                    <div>
                      <input type="number" min="0" step="1" v-model="item.business_day" placeholder="i.e. 10" />                                            
                    </div>                    
                    <div :class="['re_price_block','flex','gap-1','mt-1']">
                      <span>{{ item.business_day?item.business_day:0 }}</span>
                      <span v-if="item.hrs_in_dhaka">Hour(s) in Dhaka</span>
                      <span v-else>Business day(s)</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <i v-if="item.hrs_in_dhaka" class="far fa-square-check cursor-pointer" @click="sel_hrs_in_dhaka(index,'')"></i>
                      <i v-else class="far fa-square cursor-pointer" @click="sel_hrs_in_dhaka(index,1)"></i>
                    </div>
                  </td>
                  <td>
                    <div v-if="$parent.price_arr[cur_index].price_infos.length>1" class="action_block">
                        <span class="del_btn" @click="del_entry(index)"><i class="fa fa-trash-alt"></i></span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="add_btn_block">
                <span @click="add_new_entry"><i class="fa fa-plus"></i> Add New Entry</span>
            </div>
        </div>
        <div style="display: none">{{ $parent.eventChanged }}</div>
    </div>
</template>
<script>
export default {
    name: 'ProductPriceEntryFormBlock',
    props: ['cur_index'],
    mounted(){
       document.body.classList.add('popup_open')
    },
    methods: {
      recommendedPriceInfo: function(recommended, cur_index, index){
        // console.log(recommended, cur_index, index)
        this.$parent.price_arr[cur_index].price_infos[index]['recommended'] = recommended?false:true
        this.$parent.eventChanged = this.$parent.eventChanged?false:true
      },
      close_price_form_block: function(){
          this.$parent.price_form_block_show = false
          document.body.classList.remove('popup_open')
      },
      add_new_entry: function(){
        let obj = {
          quantity: 1,
          price_type: 1,
          price: 1,
          discount_price: '',
          business_day: 1,
          hrs_in_dhaka: '',
          recommended: false
        }

        this.$parent.price_arr[this.cur_index].price_infos.push(obj)
      },
      sel_hrs_in_dhaka: function(index,value){
        this.$parent.price_arr[this.cur_index].price_infos[index].hrs_in_dhaka = value
        this.$parent.eventChanged = this.$parent.eventChanged?false:true
      },
      del_entry: function(index){
        if(confirm('Are you sure to delete it?')) this.$parent.price_arr[this.cur_index].price_infos.splice(index, 1)
      }
    }
}
</script>
<style lang="scss" scoped>
    .product_price_entry_form_block{
        position: relative;
        z-index: 1001;

        & > .black_mask_overlay{
          position: fixed;
          left: 0; bottom: 0;
          width: 100%; height: 100%;
          background-image: linear-gradient(to top, #000000cc, #66666680);
        }

        & > .content{
          position: fixed;
          color: #333;
          background-color: #eee;
          min-width: 450px;
          padding: 15px;
          height: 100%;
          overflow-y: auto;
          right: 0;
          top: 0;
          box-shadow: 0 0 15px #333;
          transition: all 0.4s;

          & > .table{
            background-color: #fff;
            border: 1px solid #ddd;
            margin: 0;

            & > thead {
              background-color: #e6f0fb;
              border-color: #aed4fb;
              color: #007eff;

              & > tr > th{
                font-size: 12px;
                padding: 7px 10px;
                border: 1px solid #ddd;
                vertical-align: middle;
                text-align: center
              }
            }

            & > tbody{
              & > tr > td{
                vertical-align: middle;
                text-align: center;
                input,select{
                  max-width: 100px;
                  padding: 1px 0 1px 10px;
                  border: 1px solid #ddd;
                  border-radius: 25px;
                  text-align: center;
                  outline: none;
                }
                & > div{
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  gap: 3px;

                  &.recommended_block,&.re_price_block{
                    justify-content: center;
                    font-size: 10px;
                    color: #999;
                    cursor: pointer;
                    &.active{
                      color: #363
                    }
                  }

                  & > input{
                    max-width: 80px
                  }
                  & > select{
                    text-align: left;
                    padding: 1px 10px;
                    height: 25px;
                  }
                }

                & > .action_block{
                    align-self: center;
                    margin-left: auto;
                    & > .del_btn{
                      display: inline-block;
                      font-size: 10px;
                      color: #CD0000;
                      cursor: pointer;
                    }
                }
              }
            }
          }

          & > .add_btn_block{
              padding: 10px 15px;
              text-align: center;
              background-color: #fbf6ff;
              border: 1px solid #ddd;
              border-top: none;
              & > span{
                  display: block;
                  font-size: 12px; font-weight: bold; color: blueviolet;
                  text-transform: uppercase; cursor: pointer;
                  transition: all 0.4s;

                  &:hover{
                    color: #006699;
                  }
              }
          }
        }

        &.mobile_device .content{
            width: 300px;
        }
    }
</style>
