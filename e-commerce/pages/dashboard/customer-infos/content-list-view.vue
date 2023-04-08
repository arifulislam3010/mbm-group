<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Login ID</th>                    
                    <th>User Info</th>
                    <th style="text-align:center" width="80">Verified</th>
                    <th style="text-align:center" width="80">Status</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                 <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>
                            <div v-if="item.email">Email: {{ item.email }}</div>
                            <div v-if="item.mobile">Mobile: {{ item.mobile }}</div>
                        </td>
                        <td>
                            <template v-if="item.user_info">
                                <div v-if="item.user_info.full_name">Name: {{ item.user_info.full_name }}</div>
                                <div v-if="item.user_info.gender_id">Gender: {{ $store.state.gender_list[item.user_info.gender_id] }}</div>
                            </template>
                        </td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.verified}]"></i>
                            </span>
                        </td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.status}]"></i>
                            </span>
                        </td>
                        <td align="center">
                            <div class="action_block">
                                <template v-if="($parent.$parent.$parent.role_access.edit_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.edit))">
                                    <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                </template>
                                <template v-if="(item.id>1 || $store.state.auth_info.user_data.id!=item.id) && ($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
                                    <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>
                                </template>
                            </div>
                        </td>
                    </tr>
                 </template>
                 <template v-else>
                    <tr><td colspan="7" align="center"><i class="fa fa-info-circle"></i> There is no data</td></tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    name: 'ContentListView',
    props: ['data','content_loader'],
    methods: {
        edit_item: function(val){            
            this.$parent.edit_content_id = val
            this.$parent.add_new_entity(true)
            this.$parent.$parent.$parent.edit_route(val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/customer-infos/delete/' + id, this.$parent.header_config).then( (response) => {
                    console.log('Get Data', response.data)
                    this.$swal("Good job!", "Data has been deleted successfully.", "success");
                    this.$parent.load_data();
                }).catch(e => {
                    console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})                
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