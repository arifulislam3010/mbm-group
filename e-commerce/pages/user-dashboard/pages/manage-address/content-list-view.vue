<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th v-if="$parent.sel_default_setup_index" style="text-align:center" width="50">Sl.</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Region Info</th>
                    <th>Contact Number</th>
                    <th style="text-align:center" width="80">Status</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td v-if="$parent.sel_default_setup_index" align="center">
                            <span @click="sel_default_address(item)">
                                <i v-if="sel_default_id==item.id" class="far fa-dot-circle fa-2x active"></i>
                                <i v-else class="far fa-circle fa-2x"></i>
                            </span>
                        </td>
                        <td>{{ item.full_name }}</td>
                        <td>
                            <span :class="['address_lbl',{home:item.label_id==1},{office:item.label_id==2}]"><em v-html="$store.state.shipping_addr_labels[item.label_id].icon"></em> {{ $store.state.shipping_addr_labels[item.label_id].title }}</span>
                            <span>{{ item.address }}</span>
                            <template v-if="$parent.sel_default_setup_index=='' && (item.default_shipping_address>0 || item.default_billing_address>0)">
                                <hr class="mt-2 mb-1" />
                                <div class="default_address_block" v-if="item.default_shipping_address"><small><i class="fa fa-check"></i> Default shipping address</small></div>
                                <div class="default_address_block" v-if="item.default_billing_address"><small><i class="fa fa-check"></i> Default billing address</small></div>
                            </template>
                        </td>
                        <td>{{ item.area_info.title }} - {{ item.city_info.title }} - {{ item.region_info.title }}</td>
                        <td>{{ item.contact_no }}</td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.status}]"></i>
                            </span>
                        </td>
                        <td align="center">
                            <div class="action_block">
                                <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>                                
                            </div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td v-if="$parent.sel_default_setup_index" align="center">-</td>
                        <td colspan="6" align="center"><i class="fa fa-info-circle"></i> There is no data</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    data(){
        return {
            sel_address_data: '',
            sel_default_id: '',
            formData: {
                default_shipping_address: 0,
                default_billing_address: 0
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
    .content_list_view > table{
        border: 1px solid #ddd
    }
    table th,
    table td{
        font-size: 12px;
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
    .status > i{
        font-size: 18px;
        color: #ccc
    }
    .status > i.active{
        color: #5dad5d
    }
    .action_block{
        display: block;
        text-align: right;
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
    }
</style>