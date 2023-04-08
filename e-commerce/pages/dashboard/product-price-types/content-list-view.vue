<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Icon</th>
                    <!-- <th>Category</th> -->
                    <th style="text-align:center" width="80">Status</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>{{ item.type_title }}</td>
                        <td>{{ item.slug }}</td>
                        <td>
                            <span v-if="item.icon" class="icon">
                                <img :src="item.icon" />                
                            </span>
                        </td>
                        <!-- <td>{{ item.cat_info.category_name }}</td> -->
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
                                <template v-if="($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
                                    <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr><td colspan="6" align="center"><i class="fa fa-info-circle"></i> There is no data</td></tr>
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
                this.$axios.get('/api/product-price-types/delete/' + id, this.$parent.header_config).then( (response) => {
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
    .content_list_view > table td{
        vertical-align: middle;
    }
    .icon{
        display: inline-block;
        width: 52px; height: 52px; padding: 5px;
        line-height: 40px; text-align: center;
        border: 1px solid #ddd; background-color: #fff;
        border-radius: 5px; overflow: hidden;
    }
    .icon > img{
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .icon > i{
        font-size: 26px; line-height: 40px; color: #ccc
    }
    .status > i{
        font-size: 18px;
        color: #ccc
    }
    .status > i.active{
        color: #5dad5d
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
    }
</style>