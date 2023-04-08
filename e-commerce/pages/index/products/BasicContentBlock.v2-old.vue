<template>
    <div :class="['basic_content_block',{mobile_device:$device.isMobile}]">
        <!-- <pre>{{ data }}</pre> -->
        <div class="row">
            <div class="col-9">
                <div class="rating_view">
                    <star-rating
                        :rating="0.0"
                        :padding="5"
                        :read-only="true"
                        :animate="false"
                        :active-color="['#eeeeee','#f05a24']"
                        :active-border-color="['#ffffff','#f05a24']" :border-width="2"
                        :rounded-corners="false"
                        :show-rating="false"
                        :increment="0.1"
                        :star-size="14">
                    </star-rating>
                    <div class="total_rating">0.0 <small>0 Reviews</small></div>
                </div>
            </div>
            <div class="col-3" align="right">
                <div class="social-links">
                    <span @click="social_share('fb')" class="facebook"><i class="fab fa-facebook-f"></i></span>
                    <span @click="social_share('twitter')" class="twitter"><i class="fab fa-twitter"></i></span>
                    <!-- <span><i class="fa fa-heart"></i></span> -->
                </div>
            </div>
        </div>
        <!-- <hr /> -->
        <h1 class="product_title mt-3 mb-3">{{ data.product_title }}</h1>
        <!-- <hr /> -->
        <div class="category_info mb-1"><small>
            <template v-for="(item,index) in data.cat_info">
              <template v-if="item.cat_data_info && item.cat_data_info.category_name">
                <span :key="'cat-title-' + index" class="cat_title" @click="goto_search('category',item.cat_data_info.slug)">{{ item.cat_data_info.category_name }}</span><span v-if="data.cat_info[index + 1]" :key="'cat-sep-' + index" class="mr-1">,</span>
              </template>
            </template>
        </small></div>
        <!-- <div v-if="data.product_price_infos" class="row">
            <div class="col-md-12">
                <div class="price_type_list">
                    <label>Price Type</label>
                    <template v-for="(item,index) in data.product_price_infos">
                        <span :class="[{active:sel_price_type_index==index},{'ml-2':index>0}]" :key="'ppt-' + index" @click="sel_price_type_index=index">{{ item.product_price_type_data.type_title }}<i v-if="sel_price_type_index==index" :key="'a-ppt-' + index" class="fa fa-check"></i></span>
                    </template>
                </div>
            </div>
            <div v-if="data.product_price_infos && data.product_price_infos[sel_price_type_index] && data.product_price_infos[sel_price_type_index].remarks" class="col-md-12">
                <div class="remark_info mt-3">
                    <label>Remarks</label>
                    <p>{{ data.product_price_infos[sel_price_type_index].remarks }}</p>
                </div>
            </div>
            <div v-if="data.product_price_infos && data.product_price_infos[sel_price_type_index]" class="col-5">
                <div class="price_info mt-3">
                    <label>Price</label>
                    <div v-if="data.product_price_infos[sel_price_type_index].discount_price">
                        <PriceViewBlock :data="data.product_price_infos[sel_price_type_index].discount_price" />
                    </div>
                    <div v-if="data.product_price_infos[sel_price_type_index].price">
                        <PriceViewBlock :data="data.product_price_infos[sel_price_type_index].price" :discount="data.product_price_infos[sel_price_type_index].discount_price?true:false" />

                        <template v-if="data.product_price_infos[sel_price_type_index].discount_price">
                            <DiscountPercentageBlock :regular_price="data.product_price_infos[sel_price_type_index].price" :discount_price="data.product_price_infos[sel_price_type_index].discount_price" />
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="quantity_info mt-3">
                    <label>Quantity</label>
                    <QuantityActionBlock :quantity="quantity" />
                    <div class="mt-2" v-if="quantity>1">
                        <span v-if="$store.state.sel_lang=='bn'">মোট </span>
                        <span v-else>Total</span>
                        <template v-if="data.product_price_infos[sel_price_type_index].discount_price">
                            <PriceViewBlock :data="(data.product_price_infos[sel_price_type_index].discount_price * quantity).toFixed(2)" :lang="$store.state.sel_lang" />
                        </template>
                        <template v-else>
                            <PriceViewBlock :data="(data.product_price_infos[sel_price_type_index].price * quantity).toFixed(2)" :lang="$store.state.sel_lang" />
                        </template>
                    </div>
                </div>
            </div>
        </div> -->
        <hr />
        <!-- <pre>{{ data.product_color_infos }}</pre> -->
        <div v-if="data.product_color_infos.length > 0" class="row">
          <div class="col-md-12">
            <div class="product_type_block">
              <div class="label">Color:</div>
              <div>{{ data.product_color_infos[sel_color_index].color_title }}</div>
            </div>
            <div class="product_type_block mb-3">
              <div class="color_list">
                <div :class="{active:sel_color_index==color_index}" v-for="(color_item,color_index) in data.product_color_infos" @click="sel_color_index=color_index">
                  <span :style="{'background-color': color_item.color_code}"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="Object.keys(sel_product_type_infos).length > 0 && data.product_type_info.length > 0" class="row">
          <div class="col-md-12">
            <!-- {{ sel_product_type_infos }} -->
            <div v-for="(pt_item,pt_index) in data.product_type_info" class="product_type_block mb-3" :key="'pt-' + pt_index">
              <div class="label">{{ pt_item.product_type_data_info.type_title }}</div>
              <!-- <pre>{{ pt_item.product_type_names_info }}</pre> -->
              <div v-if="pt_item.product_type_names_info.length>1" class="dropdown_list">
                <select v-model="sel_product_type_infos[pt_item.product_type_data_info.id]" @change="getProductPriceList">
                  <!-- <option value="">Select</option> -->
                  <option v-for="(ptn_item,ptn_index) in pt_item.product_type_names_info" :value="ptn_item.product_type_names_data_info.id" :key="'ptn-' + ptn_index">{{ ptn_item.product_type_names_data_info.type_name }}</option>
                </select>
              </div>
              <div v-else-if="pt_item.product_type_names_info.length==1" class="single_item">
                  <div v-for="(ptn_item,ptn_index) in pt_item.product_type_names_info" :key="'ptn-' + ptn_index">{{ ptn_item.product_type_names_data_info.type_name }}</div>
                  <input type="hidden" v-model="sel_product_type_infos[pt_item.product_type_data_info.id]" />
              </div>
              <div v-else class="single_item">
                  <div>None</div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="Object.keys(price_type_infos).length > 0" class="row">
            <div class="col-md-12">
                <!-- {{ price_type_infos }} -->
                <div class="product_type_block mb-3">
                    <div class="label">Price Type</div>
                    <div v-if="Object.keys(price_type_infos).length>1" class="dropdown_list">
                        <select v-model="sel_price_type_id">
                          <option v-for="(pti_item,pti_index) in price_type_infos" :value="pti_item.id" :key="'pti-' + pti_index">{{ pti_item.type_title }}</option>
                        </select>
                    </div>
                    <div v-else class="single_item">
                      <div v-for="(pti_item,pti_index) in price_type_infos" :key="'pti-' + pti_index">{{ pti_item.type_title }}</div>
                      <input type="hidden" v-model="sel_price_type_id" />
                    </div>
                </div>

                <div class="product_type_block mb-3">
                    <div class="label">Unit Price</div>
                    <div class="single_item">
                        <PriceViewBlock :data="sel_unit_price" />
                    </div>
                </div>

                <template v-if="sel_price_list_infos.length > 0">
                  <div class="product_type_block mb-3">
                      <div class="label">Quantity</div>
                      <div v-if="cart_qty_mode==2" class="quantity_info">                        
                        <QuantityActionBlock :quantity="quantity" />
                      </div>
                      <div v-else class="dropdown_list">
                        <!-- {{ data.product_price_infos[sel_price_type_index] }} -->
                        <select v-model="sel_price_type_index">
                          <option v-for="(pp_item,pp_index) in sel_price_list_infos" :value="pp_index" :key="'pp-' + pp_index">{{ pp_item.quantity }} ( {{ $store.state.currency_info.symbol }} {{ sel_unit_price - ((sel_unit_price * pp_item.price) / 100) }} / {{ sel_price_type_title }} ) <template v-if="pp_item.recommended">(Recommended)</template></option>
                        </select>
                      </div>                      
                  </div>
                  
                  <div v-if="cart_qty_mode==2" class="net_price_info_block">                    
                    <template v-if="quantity>1">
                        <span v-if="$store.state.sel_lang=='bn'">মোট </span>
                        <span v-else>Total</span>
                        <PriceViewBlock :data="sel_total_quantity_price.toFixed(2)" :lang="$store.state.sel_lang" />
                    </template>
                  </div>

                  <div v-else class="net_price_info_block">
                    <!-- {{ sel_price_list_infos[sel_price_type_index] }} -->
                    <span>{{ sel_price_list_infos[sel_price_type_index].quantity }}</span> starting at <PriceViewBlock :data="(sel_unit_price - ((sel_unit_price * sel_price_list_infos[sel_price_type_index].price) / 100)) * sel_price_list_infos[sel_price_type_index].quantity" :discount="sel_price_list_infos[sel_price_type_index].discount_price?true:false" :resize="false" /> <template v-if="sel_price_list_infos[sel_price_type_index].discount_price"><PriceViewBlock :data="(sel_unit_price - ((sel_unit_price * sel_price_list_infos[sel_price_type_index].discount_price) / 100)) * sel_price_list_infos[sel_price_type_index].quantity" /></template>
                  </div>
                  <div v-if="sel_unit_price > sel_unit_price_by_qty" class="net_price_info_block current_price_info">
                      ( <PriceViewBlock :data="sel_unit_price" :discount="sel_unit_price_by_qty?true:false" :resize="false" />
                      <PriceViewBlock :data="sel_unit_price_by_qty" />
                      <span> for each</span> )
                  </div>
                </template>
            </div>
        </div>
        <div v-if="sel_upload_file_content" class="upload_file_view mt-3">
            <img :src="sel_upload_file_content" />
            <div><span @click="upload_design">Change</span></div>
            <i class="fa fa-times" @click="sel_upload_file_content=''"></i>
        </div>        
        <div v-if="sel_price_list_infos.length>0" class="row">
            <div class="col-6 mt-4 pr-2">
                <span class="action_btn" @click="buy_now(data)"><i class="fa fa-dollar-sign"></i> Buy Now</span>
            </div>
            <div class="col-6 mt-4 pl-2">
                <span class="action_btn cart" @click="add_to_cart(data)"><i class="fa fa-shopping-cart"></i> Add to Cart</span>
            </div>
        </div>
        <div v-if="!sel_upload_file_content" class="upload_product_design mt-3">
            <div @click="upload_design">
              <i class="fa fa-cloud-upload-alt"></i>
              <span>Upload your design for order</span>
            </div>            
        </div>
        <input class="upload_file_input" type="file" ref="input_file" @change="load_image" accept="image/*" capture />
        <div style="display: none">{{ eventChanged }}</div>
        <!-- <div style="max-width: 300px"><pre>{{ data.product_price_infos }}</pre></div> -->
    </div>
</template>
<script>
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import DiscountPercentageBlock from '@/components/content_display/price/DiscountPercentageBlock'
import QuantityActionBlock from '@/components/content_display/QuantityActionBlock'
import { mapMutations } from 'vuex'
import $ from 'jquery'
import { async } from 'q'
export default {
    name: 'BasicContentBlock',
    props: ['data'],
    components: {
        PriceViewBlock,
        DiscountPercentageBlock,
        QuantityActionBlock
    },
    data(){
        return {
            price_loader: false,
            price_type_infos: {},
            product_type_infos: [],
            product_type_titles: {},
            product_type_value_titles: {},
            sel_product_type_price_index: {},
            sel_product_type_infos: {},
            sel_product_type_info_values: {},
            sel_price_type_id: '',
            sel_price_type_title: '',
            sel_price_list_infos: [],
            sel_price_type_index: 0,
            cart_qty_mode: 1,            
            sel_unit_price: 0,
            sel_unit_price_by_qty: 0,
            sel_total_quantity_price: 0,
            sel_color_index: 0,
            sel_upload_file_content: '',
            min_qty: 1,
            max_qty: 100,
            quantity: 1,
            eventChanged: false
        }
    },
    watch: {
        sel_price_type_id: function(val){
            this.min_qty = 1
            this.max_qty = ''
            this.quantity = 1
            this.check_qty_restrict()
        }
        /*,sel_price_type_index: function(val){
            this.min_qty = 1
            this.max_qty = ''
            this.quantity = 1
            this.check_qty_restrict()
        }*/
    },
    mounted() {
        this.check_qty_restrict()
        this.data_info_setup()
        this.checkCurrentUnitPrice()
    },
    methods: {
        ...mapMutations({
            add_cart_item: 'cart_info/ADD_CART_ITEMS'
        }),
        goto_search: function(keyword,value){
            this.$router.push('/'+ this.$store.state.search_page_path +'?'+ keyword +'=' + value)
        },
        upload_design: function(){
            this.$refs.input_file.click();
        },
        load_image(e){
            var files = e.target.files || e.dataTransfer.files; 
            if (!files.length) return;
            console.log('File info', files[0]);
            this.CreateImage(files[0]);
        },
        CreateImage(file){
            var file_size = file.size / 1024
            if(file_size>100){
              alert('The selected file size 100 KB exceeded.\nPlease upload less or equal 100 KB')
              return false
            }
            var reader = new FileReader();            
            console.log('file size', file_size)
            reader.onload = (e) => {
                this.sel_upload_file_content = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        getProductPriceList: function(){
          // console.log('sel product type infos', this.sel_product_type_infos)
          // console.log('Product type infos', this.product_type_infos)
          let index = -1
          this.sel_price_type_index = 0
          this.sel_unit_price = 0
          this.cart_qty_mode = 1
          this.min_qty = 1
          this.max_qty = 100
          this.sel_price_list_infos = []
          this.$parent.product_type_photo_infos = []
          this.product_type_infos.forEach( async (unit_item,i) => {
            let checkItem = false
            let checkObj = unit_item[this.sel_price_type_id]
            for(var pt_id in this.sel_product_type_infos){
              // console.log(pt_id,checkObj,this.sel_product_type_infos)
              if(checkObj[pt_id] && checkObj[pt_id] == this.sel_product_type_infos[pt_id]) checkItem = true
              else {
                checkItem = false
                return true
              }
            }
            if(checkItem) {
              index = i
              return true
            }
          })

          if(index>-1){
            let getProductTypePriceObj = this.data.product_price_infos[index]
            if(getProductTypePriceObj.unit_price) this.sel_unit_price = getProductTypePriceObj.unit_price
            if(getProductTypePriceObj.cart_qty_mode) this.cart_qty_mode = getProductTypePriceObj.cart_qty_mode
            if(getProductTypePriceObj.min_qty) this.min_qty = getProductTypePriceObj.min_qty
            if(getProductTypePriceObj.max_qty) this.max_qty = getProductTypePriceObj.max_qty
            this.sel_price_list_infos = JSON.parse(getProductTypePriceObj.price_info)

            if(getProductTypePriceObj.product_photo_infos.length>0){
              getProductTypePriceObj.product_photo_infos.forEach( async (photo_item) => {
                this.$parent.product_type_photo_infos.push(photo_item)
              })
            }
          }

          // this.sel_price_type_index = index

          // alert(index)
          // this.price_loader = true;

          // this.$axios.post('/api/products/price-list/' + this.data.id, {
          //   product_price_type_id: this.sel_price_type_id,
          //   product_type_list: this.sel_product_type_infos
          // },this.$parent.$parent.header_config).then( (response) => {
          //     console.log('Get Data', response.data)
          // }).catch(e => {
          //     console.log(e)
          //     this.$toast.error('Failed!!!', {icon: "error_outline"})
          //     this.price_loader = false;
          // });
        },
        data_info_setup: function(){
          this.data.product_type_info.forEach( async (v) => {
            this.sel_product_type_infos[v.product_type_data_info.id] = ''
            this.product_type_titles[v.product_type_data_info.id] = v.product_type_data_info.type_title
            if(v.product_type_names_info.length>0){
              v.product_type_names_info.forEach( async (ptv) => {
                this.product_type_value_titles[ptv.product_type_name_id] = ptv.product_type_names_data_info.type_name
              })
            }
          })

          this.data.product_price_infos.forEach( async (v,i) => {
            // console.log('Product price contents',v.product_price_contents)
            v.product_price_contents.forEach( async (pv,pi) => {
              // console.log('product price type data',pv.product_price_type_data)
              this.price_type_infos[pv.product_price_type_data.id] = pv.product_price_type_data
              // console.log(this.price_type_infos)

              if(!this.product_type_infos[i]) this.product_type_infos[i] = {}
              if(!this.product_type_infos[i][pv.product_price_type_data.id]) this.product_type_infos[i][pv.product_price_type_data.id] = {}
              this.product_type_infos[i][pv.product_price_type_data.id][pv.product_type_data.id] = pv.product_type_name_data.id
              if(i==0){
                this.sel_price_type_id = pv.product_price_type_data.id
                this.sel_price_type_title = pv.product_price_type_data.type_title
                this.sel_price_list_infos = JSON.parse(v.price_info)
                this.sel_product_type_infos[pv.product_type_data.id] = pv.product_type_name_data.id
                this.sel_unit_price = v.unit_price
                this.cart_qty_mode = v.cart_qty_mode
                this.min_qty = v.min_qty
                this.max_qty = v.max_qty
              }
            })

            if(i==0 && v.product_photo_infos.length>0){
              v.product_photo_infos.forEach( async (photo_item) => {
                this.$parent.product_type_photo_infos.push(photo_item)
              })
            }
          })

          this.eventChanged = this.eventChanged?false:true
        },
        checkCurrentUnitPrice: function(){
            if(this.quantity >= this.sel_price_list_infos[this.sel_price_type_index].quantity) {
              do{
                this.sel_price_type_index += 1
              }while(this.quantity > this.sel_price_list_infos[this.sel_price_type_index].quantity)

            }else if(this.sel_price_type_index && this.quantity < this.sel_price_list_infos[this.sel_price_type_index - 1].quantity) {
              do{
                this.sel_price_type_index -= 1
              }while(this.sel_price_type_index && this.quantity < this.sel_price_list_infos[this.sel_price_type_index - 1].quantity)
            }
            // else this.sel_price_type_index = 0
            
            // alert(this.sel_price_type_index)
            
            if(this.sel_price_type_index) this.sel_unit_price_by_qty = this.sel_unit_price - ((this.sel_unit_price * this.sel_price_list_infos[this.sel_price_type_index].price) / 100)
            else this.sel_unit_price_by_qty = this.sel_unit_price

            // alert(this.sel_unit_price_by_qty)

            this.sel_total_quantity_price = this.sel_unit_price_by_qty * this.quantity
        },
        quantity_submit: function(val){            
            if(val<this.min_qty || isNaN(val)) this.quantity = this.min_qty
            else if(this.max_qty && val>this.max_qty) this.quantity = this.max_qty
            else this.quantity = val

            // alert(this.quantity)

            this.checkCurrentUnitPrice()
        },
        buy_now: function(item){
            this.add_to_cart(item)
            this.$router.push('/'+ this.$store.state.checkout_page_path +'?time=' + Date.now())
        },
        async product_type_value_organize(){
            if(this.data.product_color_infos.length>0){
              let getColorInfo = this.data.product_color_infos[this.sel_color_index]              

              this.sel_product_type_info_values['color'] = {
                'title': 'Color',
                'pid': getColorInfo.id,
                'values': {
                  'color_code': getColorInfo.color_code,
                  'color_title': getColorInfo.color_title
                }
              }
            }

            for(var pt_id in this.sel_product_type_infos){
              let getPtTitle = this.product_type_titles[pt_id]
              let getPtvId = this.sel_product_type_infos[pt_id]              
              this.sel_product_type_info_values[pt_id] = {                
                'title': getPtTitle,
                'pid': getPtvId,
                'values': this.product_type_value_titles[getPtvId]
              }
            }
            
            console.log(this.sel_product_type_info_values)
        },
        async add_to_cart(item){
            console.log('Get request item', item)
            await this.product_type_value_organize()
            // item.req_quantity = this.quantity
            // item.req_quantity = this.sel_price_list_infos[this.sel_price_type_index].quantity
            item.sel_price_type_id = this.sel_price_type_id
            item.price_type_infos = this.price_type_infos
            item.sel_product_type_infos = this.sel_product_type_infos
            item.sel_price_type_index = this.sel_price_type_index
            item.sel_unit_price = this.sel_unit_price
            item.sel_cart_qty_mode = this.cart_qty_mode
            item.quantity = this.cart_qty_mode==2?this.quantity:''
            item.sel_min_qty = this.min_qty
            item.sel_max_qty = this.max_qty
            item.sel_price_list_infos = JSON.stringify(this.sel_price_list_infos)
            item.sel_product_type_info_values = JSON.stringify(this.sel_product_type_info_values)
            item.sel_upload_file_content = this.sel_upload_file_content
            // if(item.product_price_infos[this.sel_price_type_index].discount_price) item.product_price_infos[this.sel_price_type_index].price = item.product_price_infos[this.sel_price_type_index].discount_price
            // alert(this.sel_price_type_index)
            console.log('Cart Item', item)

            await this.add_cart_item(item)

            // let get_pos = $('#item-' + item.id).offset().top;
            // console.log(get_pos)

            // this.$toast.success('Item added to cart', {icon: "Success"});
            this.$notify.success({
                title: 'Success',
                message: 'Item added to cart',
                timeout: 1500
            })
        },
        check_qty_restrict: function(){
            if(this.cart_qty_mode==2){
              if(this.data.product_price_infos && this.data.product_price_infos[this.sel_price_type_index] && this.data.product_price_infos[this.sel_price_type_index].min_qty) this.min_qty = this.data.product_price_infos[this.sel_price_type_index].min_qty
              if(this.data.product_price_infos && this.data.product_price_infos[this.sel_price_type_index] && this.data.product_price_infos[this.sel_price_type_index].max_qty) this.max_qty = this.data.product_price_infos[this.sel_price_type_index].max_qty

              this.quantity = this.min_qty>this.quantity?this.min_qty:this.quantity
              // alert(this.quantity)
            }
        },
        social_share: function(type){
            let url = ''
            if(type=='fb'){
                url = 'https://www.facebook.com/sharer.php?t=' + encodeURIComponent(this.data.product_title) + '&u=' + encodeURIComponent(process.env.BASE_URL + this.$route.fullPath)
                // alert(url)
            }else if(type=='twitter'){
                url = 'http://twitter.com/share?text='+ encodeURIComponent(this.data.product_title) +'&url='+ encodeURIComponent(process.env.BASE_URL + this.$route.fullPath) + (this.data.tag_info.length>0?'&hashtags=' + this.data.tag_info[0].tag_title:'')
            }

            window.open (url,'','width=500, height=500, scrollbars=yes, resizable=no');
        }
    }
}
</script>
<style lang="scss" scoped>
    $lbl_width: 150;
    .basic_content_block{
      background-color: #fff;
      padding: 20px 30px;
      border: 1px solid #ddd;
      border-top: none; border-right: none;
      border-radius: 0 0 0 15px;

      &.mobile_device{
        padding: 0; border: none;
      }
    }
    .category_info .cat_title:hover{
        color: purple;
        cursor: pointer
    }
    .product_title{
        display: block;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    .rating_view{
      display: flex;      
      gap: 3px;
      height: 36px;
      align-items: center;

      & > .total_rating{
        display: flex;
        align-items: baseline;
        font-size: 20px;
        color: #666;

        & > small{
           margin-left: 10px;
           font-size: 13px;
        }
      }
    }
    .social-links{
        display: flex;
        justify-content: right;
        align-items: center;
        height: 36px; gap: 10px;

        & > span{
          display: flex;
          color: #999;
          transition: all 0.4s;
          width: 30px;
          height: 30px;
          background-color: #f7f7f7;
          border: 1px solid #eee;
          border-radius: 50%;
          justify-content: center;
          align-items: center;
          transition: all 0.4s;
          cursor: pointer;

          &.facebook{
            color: #1877f2;
            border-color: #1877f2;
            &:hover{
              background-color: #1877f2;
              color: #fff
            }
          }
          &.twitter{
            color: #1da1f2;
            border-color: #1da1f2;
            &:hover{
              background-color: #1da1f2;
              color: #fff
            }
          }
        }
    }
    .product_type_block{
      display: flex;
      height: 36px; align-items: center;
      gap: 15px;

      & > .color_list{
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
        height: 100%;

        & > div{
          display: flex;
          width: 32px; height: 32px;
          align-items: center;
          justify-content: center;
          border: 1px solid #fff;
          border-radius: 50%;
          cursor: pointer;

          &:hover{
            border: 1px solid #ccc
          }

          &.active{
            border: 1px solid #666;
            box-shadow: 0 0 5px #888;
          }

          & > span{
            display: inline-block;
            width: 20px; height: 20px;
            border: 1px solid #ddd;
            border-radius: 50%;
          }
        }
      }
    }
    .product_type_block > div.label{
      font-size: 12px; width: #{$lbl_width}px; color: #666; font-weight: 600;
    }
    .product_type_block > div.dropdown_list,
    .product_type_block > div.quantity_info,
    .product_type_block > div.single_item{
      margin-left: auto; width: calc(100% - #{$lbl_width}px); height: 100%; padding: 0 15px;
      border: 1px solid #ddd; border-radius: 25px;

      &.quantity_info{
        border: none;
        padding: 0;
        height: auto;
        border-radius: unset;

        & > :deep(.quantity_action_block){
            border: 1px solid #ddd;
            border-radius: 15px;
            overflow: hidden;
        }
      }
    }
    .product_type_block > div.single_item{
      border: none; padding: 0;
      & > div{
        display: flex;
        height: 100%;
        align-items: center;
        gap: 5px;

        &.price_view_block{
          font-weight: 600;
          font-size: 18px;
        }
      }
    }
    .product_type_block > div.dropdown_list > select{
      height: 100%; display: flex; width: 100%;
      border: none; outline: none
    }
    .product_type_info{
        display: block; text-align: left; height: 20px;
        line-height: 20px; font-size: 11px;
    }
    .product_type_info > div,
    .product_type_info > div > span{
        display: inline-block;
    }
    .product_type_info > div > .icon{
        width: 20px; height: 20px;
    }
    .product_type_info > div > .icon > img{
        display: inline-block;
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .product_type_info:hover > div > .title{
        color: purple;
        cursor: pointer
    }
    .net_price_info_block{
      display: flex;      
      align-items: center;
      margin-left: #{$lbl_width}px;
      font-size: 14px;
      // justify-content: center;
      gap: 5px
    }
    .net_price_info_block :deep(.price_view_block){
      color: #006699; font-weight: 600
    }
    .current_price_info :deep(.price_view_block){
      color: #666;
    }
    .net_price_info_block :deep(.price_view_block.discounted){
      color: #999; font-weight: normal
    }
    .company_info{
        display: inline-block; height: 20px;
        line-height: 20px; font-size: 11px;
        transition: all 0.4s
    }
    .company_info > .logo{
        display: inline-block;
        width: 20px; height: 20px;
    }
    .company_info > .logo > img{
        display: inline-block;
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .company_info:hover > .name{
        color: purple;
        cursor: pointer
    }
    .price_type_list{
        display: block;
    }
    .price_type_list > label,
    .remark_info > label,
    .price_info > label,
    .quantity_info > label{
        display: block; font-size: 12px; margin-bottom: 5px;
    }
    .price_type_list > span{
        display: inline-block; position: relative; background-color: #fff; font-size: 10px; height: 30px; line-height: 30px; white-space: nowrap; padding: 0 10px; text-align: center; border: 1px solid #ddd; border-radius: 3px; cursor: pointer;
    }
    .price_type_list > span.active{
        border-color: #CD0000;
    }
    .price_type_list > span > i{
        position: absolute; margin: 0 -1px -1px 0; padding: 2px 2px 2px 2px; border-radius: 3px 0 3px 0; font-size: 7px;
        bottom:0; right: 0; background-color: #CD000099; color: #fff;
    }
    .remark_info > p{
        background-image: linear-gradient(to right, #f7f7f7, transparent); padding: 8px 12px; font-size: 12px;
    }
    .price_info :deep(.price_view_block){
        font-size: 34px;
        line-height: 34px;
        color: #e5733a;
    }
    .price_info :deep(.price_view_block.discounted){
        font-size: 16px;
        line-height: 16px;
        color: #999;
        text-decoration: line-through;
        /* margin-left: 5px */
    }
    .price_info :deep(.discount_percentage_block) span{
        font-size: 16px;
        line-height: 16px;
    }

    .upload_file_view{
        position: relative;
        width: 100%;
        height: 250px;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 5px;

        & > img{
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        & > i{
          position: absolute;
          width: 24px;
          height: 24px;
          line-height: 24px;
          text-align: center;
          background-color: #fff;
          border-radius: 0 0 0 10px;
          border: 1px solid #ccc;
          z-index: 1;
          right: -1px;
          top: -1px;
          color: #CD0000;
          transition: all 0.4s;
          cursor: pointer;

          &:hover{
            background-color: #CD0000; border-color: #CD0000; color: #fff;
          }
        }

        & > div{
            position: absolute;
            left: 0;
            right: 0;
            bottom: 25px;
            
            & > span{
              display: inline-block;
              padding: 0 15px;
              line-height: 24px;              
              background-color: $sys_brand_color;
              color: #fff;
              border-radius: 25px;
              font-size: 10px;
              cursor: pointer;
              box-shadow: 0 0 5px #ccc;
              transition: all 0.4s;

              &:hover{
                background-color: #006699;
              }
            }
        }
    }

    .upload_product_design{
        display: flex;
        align-items: center;
        justify-content: center;

        & > div{
            display: flex;
            align-items: center;
            width: 100%;
            grid-gap: 10px;
            gap: 10px;
            height: 36px;
            padding: 0 15px;
            background-color: #59294e;
            color: #fff;
            justify-content: center;
            border-radius: 5px;
            text-transform: capitalize;
            cursor: pointer;
            & > i{
                width: 24px;
                height: 24px;
                line-height: 24px;
                text-align: center;
                border-radius: 50%;
                background-color: #9c4d8a;
                font-size: 10px;
            }
        }        
    }

    .upload_file_input{
        display: none;
    }
    .action_btn{
        display: block;
        height: 36px; line-height: 36px; text-align: center;
        background-color: #e5733a; color: #fff;
        cursor: pointer; border-radius: 3px; transition: all 0.4s;
    }
    .action_btn.cart{
        background-color: #006699;
    }
    .action_btn > i{
        display: inline-block; position: relative;
        background-color: #ff7f40; font-size: 10px;
        width: 24px; height: 24px; margin-right: 5px;
        line-height: 24px; border-radius: 50%;
    }
    .action_btn.cart > i{
        background-color: #0088cc;
    }
</style>
