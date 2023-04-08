<template>
    <div class="product_price_setup_block">
      <!-- <pre>{{ price_arr }}</pre> -->
        <label>Price Setup ({{ price_arr.length }})</label>
        <div class="info_form_block mt-1">
            <div v-if="price_arr.length==0" class="empty_block">
                <i class="fa fa-box-open fa-4x"></i>
                <div>No entry yet. Click on the button below to add one.</div>
            </div>
            <div v-else>
                <!-- <draggable v-model="price_arr" group="product_price_setup" @start="drag=true" @end="drag=false">
                  <transition-group>
                      <div v-for="item in price_arr" :key="item.title + '-' + item.unit_price">
                          {{ item.title }} / {{ item.unit_price }}
                      </div>
                  </transition-group>
                </draggable> -->
                <draggable v-model="price_arr" handle=".drag_cross" group="product_price_setup" @start="drag=true" @end="drag=false" @change="change_activity()">
                    <div class="info_list" v-for="(item,index) in price_arr" :key="index">
                          <HeaderBlock :cur_index="index" :title="item.title" :product_type_infos="item.product_type_infos" :event_changed="eventChanged" />
                          <FromBlock v-if="selected_price_index==index" :data="item" :cur_index="index" />
                    </div>                    
                </draggable>                
            </div>
            <div class="add_btn_block">
                <span @click="add_new_entry"><i class="fa fa-plus"></i> Add New Entry</span>
            </div>
        </div>
        <PriceFormBlock v-if="price_form_block_show" :cur_index="selected_price_index" />
        <div style="display: none">{{ eventChanged }}</div>
    </div>
</template>
<script>
import HeaderBlock from './header-block'
import FromBlock from './form-block'
import PriceFormBlock from './price-form-block'
export default {
    name: 'ProductPriceSetupBlock',
    components: {
        HeaderBlock,
        FromBlock,
        PriceFormBlock
    },
    data(){
        return {
            price_form_block_show: false,
            // product_type_info_arr: this.$parent.formData.product_type_infos,
            price_arr: this.$parent.formData.product_price_infos,
            selected_price_index: -1,
            drag: false,
            eventChanged: false
        }
    },
    watch: {
      price_arr: function(data) {
        this.$parent.formData.product_price_infos = data
      },      
      product_type_info_arr: {
        handler(data){
          // alert('')
          this.append_new_product_type(data)
        },
        deep: true,
        immediate: true
      },
      product_type_names_info_arr: {
        handler(data){
          // alert('')
          this.append_new_product_name(data)
        },
        deep: true,
        immediate: true
      }
    },
    computed: {
        product_type_names_info_arr: function(){
          return this.$parent.product_type_names_info
        },
        product_type_info_arr: function(){
          return this.$parent.formData.product_type_infos
        },
        product_color_info_arr: function(){
          // console.log(this.$parent.product_color_list)
          return this.$parent.product_color_list
        },
        product_price_types_info_arr: function(){
          return this.$parent.product_price_type_list
        },        
        product_price_types: function(){
            let arr = {};
            this.$parent.product_price_type_list.forEach((v,i) => {
                let obj = {
                    // id: v.id,
                    index: i,
                    title: v.type_title
                };
                // arr.push(obj);
                arr[v.id] = obj
            })

            return arr;
        }
    },
    mounted(){
        // this.$parent.load_product_price_types();
        if(this.price_arr.length > 0) {
          this.price_arr.map((item,index) => {
            // console.log('Item',item)
            if(item.product_type_infos && Object.keys(item.product_type_infos).length > 0) {
              for(var i in item.product_type_infos) {
                let v = item.product_type_infos
                if(v[i].title){
                  // console.log('Item V',v)
                  this.product_type_names_info_arr[i].map((pv,pi) => {
                    // console.log('Item PV',pv.product_type_names_data_info.id,v[i].id)
                    if(pv.id==v[i].id) this.price_arr[index].product_type_infos[i].index = pi
                  })
                }
              }
            }

            this.product_price_types_info_arr.map((ppv,ppi) => {
              if(ppv.id=item.id) this.price_arr[index].index = ppi
            })
          })
        }
    },
    methods: {
        add_new_entry: function(){
            let obj = {
                index: '',
                id: '',
                title:'',
                product_type_infos: {},
                cart_qty_mode: 1,
                unit_price: 0,
                min_qty: '',
                max_qty: '',
                price_infos: [{
                  quantity: 1,
                  price_type: 1,
                  price: 1,
                  discount_price: '',
                  business_day: 1,
                  hrs_in_dhaka: '',
                  recommended: false
                }],
                photo_infos: [],
                color_type_id: '',
                size_type_id: '',
                remarks:'',
                status: true
            }

            /**
             * Product type info setup
             */
            if(this.product_type_info_arr.length > 0){
              this.product_type_info_arr.map(data => {
                let po = {
                  index: '',
                  id: '',
                  title: ''
                }
                obj.product_type_infos[data.id] = po
              })
            }

            this.price_arr.push(obj);
            this.selected_price_index = this.price_arr.length - 1;
        },
        change_activity: function(){
          this.eventChanged = this.eventChanged?false:true
        },
        append_new_product_type: function(data){
          this.price_arr.forEach( async (item,index) => {
            data.forEach( async (v) => {
              if(v.id && !item.product_type_infos[v.id]) {
                let po = {
                  index: '',
                  id: '',
                  title: ''
                }
                this.price_arr[index].product_type_infos[v.id] = po
              }
            })
          })
        },
        append_new_product_name: function(data){
          this.price_arr.forEach( async (item,index) => {
            if(item.product_type_infos && Object.keys(item.product_type_infos).length > 0) {
              for(var i in item.product_type_infos) {
                let v = item.product_type_infos
                if(v[i].title){
                  // console.log('Item V',v)
                  data[i].forEach( async (pv,pi) => {
                    // console.log('Item PV',pv.product_type_names_data_info.id,v[i].id)
                    if(pv.id==v[i].id) this.price_arr[index].product_type_infos[i].index = pi
                  })
                }
              }
            }
          })
        },
        select_price_type: function(price_index){
            if(this.price_arr[price_index].id!==''){
              let get_id = this.price_arr[price_index].id
              this.price_arr[price_index].index = this.product_price_types[get_id].index
              this.price_arr[price_index].title = this.product_price_types[get_id].title
            }else{
              this.price_arr[price_index].id = ''
              this.price_arr[price_index].title = ''
            }
        },
        select_product_type: function(price_index,product_type_index,product_type_id){
          if(this.price_arr[price_index].product_type_infos[product_type_id].index!==''){
            let get_index = this.price_arr[price_index].product_type_infos[product_type_id].index
            this.price_arr[price_index].product_type_infos[product_type_id].title = this.product_type_info_arr[product_type_index].type_name[get_index]
            this.price_arr[price_index].product_type_infos[product_type_id].id = this.product_type_names_info_arr[product_type_id][get_index].id
          }else{
            this.price_arr[price_index].product_type_infos[product_type_id].title = ''
            this.price_arr[price_index].product_type_infos[product_type_id].id = ''
          }
        },
        select_color_item: function(price_index,item){
            this.price_arr[price_index].color_type_id = item.id
        },
        select_entry: function(index){
            if(this.selected_price_index==index) this.selected_price_index = -1
            else this.selected_price_index = index
        },
        clone_entry: function(getObjData){
            // var getObjData = this.price_arr[index]
            let getData = Object.assign({}, JSON.parse(getObjData))
            let obj = {
                index: getData.index,
                id: getData.id,
                title: getData.title,
                product_type_infos: Object.assign({},getData.product_type_infos),
                cart_qty_mode: getData.cart_qty_mode,
                unit_price: getData.unit_price,
                min_qty: getData.min_qty,
                max_qty: getData.max_qty,
                price_infos: getData.price_infos,
                color_type_id: getData.color_type_id,
                size_type_id: getData.size_type_id,
                photo_infos: getData.photo_infos,
                remarks: getData.remarks,
                status: getData.status?getData.status:false
            }

            this.price_arr.push(obj)
            this.selected_price_index = this.price_arr.length - 1
        },
        del_entry: function(index){
            // delete this.price_arr[index]
            if(confirm('Are you sure to delete it?')) this.price_arr.splice(index, 1)
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
