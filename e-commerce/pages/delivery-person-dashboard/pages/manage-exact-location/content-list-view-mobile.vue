<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <template v-else-if="data.length>0">
            <div class="head_section">
                <div>Address list</div>
                <div>Total: {{ data.length }}</div>
            </div>
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <div class="list">
                        <!-- <td>{{ item.full_name }}</td> -->
                        <div>                            
                            <span>{{ item.address }}</span>
                            <template v-if="$parent.sel_default_setup_index=='' && item.default_shipping_address">
                                <hr class="mt-2 mb-1" />
                                <div class="default_address_block" v-if="item.default_shipping_address"><small><i class="fa fa-check"></i> Active address</small></div>
                            </template>
                        </div>
                        <div>{{ item.area_info.title }} - {{ item.city_info.title }} - {{ item.region_info.title }}</div>
                        <!-- <div>{{ item.contact_no }}</div> -->
                        <div align="left" class="status">
                            <i :class="['far','fa-check-circle',{active:item.status}]"></i>
                            <span v-if="item.status">Active</span>
                            <span v-else>Inactive</span>
                        </div>
                        <hr />
                        <div align="right" class="bottom_block">
                            <div v-if="$parent.sel_default_setup_index" :class="['default_setup_block',{active:sel_default_id==item.id}]"  @click="sel_default_address(item)">
                                <span class="icon">
                                    <i v-if="sel_default_id==item.id" class="far fa-check-circle fa-2x active"></i>
                                    <i v-else class="far fa-circle fa-2x"></i>
                                </span>
                                <span class="title">Default</span>
                            </div>
                            <div class="action_block">
                                <span @click="edit_item(item.id)"><i class="fa fa-edit"></i> Edit</span>
                                <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i> Remove</span>                                
                            </div>
                        </div>
                    </div>
                </swiper-slide>
            </swiper>
        </template>
        <template v-else>
            <div><i class="fa fa-info-circle"></i> There is no data</div>
        </template>
    </div>
</template>
<script>
export default {
    name: 'ContentListView',
    props: ['data','content_loader','per_view'],
    data(){
        return {
            sel_address_data: '',
            sel_default_id: '',
            formData: {
                default_shipping_address: 0,
                default_billing_address: 0
            },
            swiperOption: {
                slidesPerView: this.per_view?this.per_view:1,
                spaceBetween: 10,
                freeMode: true,
                grabCursor: true
            }
        }
    },    
    methods: {
        sel_default_address: function(data){
            if(this.$parent.sel_default_setup_index==1) this.formData.default_shipping_address = 1
            else if(this.$parent.sel_default_setup_index==2) this.formData.default_billing_address = 1

            this.sel_address_data = data
            this.sel_default_id = data.id
        },
        select_address: function(){
            // alert('')
            // console.log(this.sel_address_data)
            // this.$parent.sel_address_info = this.sel_address_data
            this.$parent.$parent.sel_address_info = this.sel_address_data
        },
        edit_item: function(val){
            this.$parent.entry_form_open = true
            this.$parent.edit_content_id = val
            
            this.$router.push(this.$parent.cur_path + '?action=edit&id=' + val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/manage-address/delete/' + id, this.$parent.header_config).then( (response) => {
                    console.log('Get Data', response.data)
                    this.$swal("Good job!", "Data has been deleted successfully.", "success");
                    this.$parent.load_data();
                }).catch(e => {
                    console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})                
                });
            }
        },
        submit_default_setup: function(){
            if(this.sel_default_id==''){
                this.$toast.error('Please choose default address', {icon: "Warning"});
                return false;
            }

            if(confirm('Are you sure to submit it?')){
                this.$parent.content_loader = true;
                
                this.$axios.post('/api/manage-address/default-address-update/' + this.sel_default_id + '?type=' + this.$parent.sel_default_setup_index, this.formData, this.$parent.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    await this.$swal("Good job!", "Data has been saved successfully.", "success")

                    this.$parent.load_data()                    
                    
                    this.$parent.content_loader = false
                }).catch(e => {
                    // console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.$parent.content_loader = false;
                });
            }
        }
    }
}
</script>
<style scoped>
    .head_section{
        display: flex;
        height: 30px
    }
    .head_section > div{
        font-size: 14px;
        font-weight: bold;
        color: #333;
        align-self: center
    }
    .head_section > div:last-child{
        margin-left: auto
    }
    .list{
        display: block;
        padding: 15px;
        background-color: #f7f7f7;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .address_lbl{
        display: inline-block;
        padding: 1px 8px; margin-right: 2px;
        background-color: #666;
        color: #fff;
        border-radius: 25px;
        font-size: 10px;
    }
    .address_lbl > em{
        margin-right: 2px;
    }
    .address_lbl.home{
        background-color: #006699;
    }
    .address_lbl.office{
        background-color: #CD0000;
    }
    .default_address_block{
        display: block;
    }
    .default_address_block > small{
        display: inline-block;
        border-bottom: 1px dashed #ccc;        
    }
    .status{
        display: flex;
        height: 20px;
        margin-top: 5px
    }
    .status > span{
        align-self: center
    }
    .status > i{
        align-self: center;
        font-size: 18px;
        margin-right: 5px;
        color: #ccc
    }
    .status > i.active{
        color: #5dad5d
    }
    .bottom_block{
        display: flex;
        height: 30px;
    }
    .bottom_block > div{
        align-self: center
    }
    .default_setup_block{
        display: flex;
        height: 30px
    }
    .default_setup_block.active{
        color: #363
    }
    .default_setup_block > span{
        align-self: center
    }
    .default_setup_block > .icon{
        margin-right: 5px
    }
    .action_block{
        margin-left: auto;
        text-align: right;
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
    }
</style>