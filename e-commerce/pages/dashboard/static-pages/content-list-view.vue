<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Photo</th>
                    <th>Page Title</th>
                    <th>Slug</th>
                    <th>Display On</th>
                    <th style="text-align:center" width="80">Google Map On</th>
                    <th style="text-align:center" width="80">Status</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>
                            <div class="photo_block">
                                <template v-if="item.photo_infos.length>0">
                                    <carousel :paginationEnabled="false" :perPage="1">
                                        <template v-for="(photo_item,mg_index) in item.photo_infos">
                                            <template v-if="photo_item.page_photo_data">
                                                <slide :key="mg_index">
                                                    <img :src="photo_item.page_photo_data.content" />
                                                </slide>
                                            </template>
                                            <div class="empty_slide" v-else :key="mg_index" align="center"><i class="fa fa-images"></i></div>
                                        </template>
                                    </carousel>
                                </template>
                                <i v-else class="fa fa-images"></i>
                            </div>
                        </td>
                        <td>
                            <div>{{ item.page_title }}</div>
                        </td>
                        <td>
                            <div>{{ item.slug }}</div>
                        </td>
                        <td>
                            <div>{{ $store.state.display_on_list[item.display_on] }}</div>
                        </td>
                        <td align="center">
                            <span class="status">
                                <i :class="['far','fa-check-circle',{active:item.google_map_display}]"></i>
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
                                <template v-if="($parent.$parent.$parent.role_access.delete_others || ($parent.$parent.$parent.get_login_user_id==item.created_by && $parent.$parent.$parent.role_access.delete))">
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
                this.$axios.get('/api/static-page-infos/delete/' + id, this.$parent.header_config).then( (response) => {
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
<style lang="scss" scoped>
    .content_list_view > table{
        border: 1px solid #ddd
    }
    .photo_block{
        display: inline-block;
        width: 90px; height: 65px; padding: 5px;
        line-height: 50px; text-align: center;
        border: 1px solid #ddd; background-color: #fff;
        border-radius: 5px; overflow: hidden;
        .empty_slide{
            width: 100%;   
        }
        :deep(img){
            width: 100%; height: 100%;
            object-fit: contain;
        }
        :deep(i){
            font-size: 36px; line-height: 55px; color: #ccc
        }
    }
    .status > i{
        font-size: 18px;
        color: #ccc;
        &.active{
            color: #5dad5d
        }
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
    }
</style>