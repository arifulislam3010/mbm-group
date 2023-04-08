<template>
    <div class="content_list_view table-responsive">
        <div class="filter_option_block">
            <span>Filters</span>
            <ul>
                <li>
                    <label>Category</label>
                    <select v-model="$parent.filter.cat_id" @change="filter_by_type('cat_id')">
                        <option value="">All</option>
                        <option v-for="(item,i) in $parent.category_list" :value="item.id" :key="'product_type-' + i">{{ item.category_name }}</option>
                    </select>
                </li>
                <li>
                    <label>Status</label>
                    <select v-model="$parent.filter.status" @change="filter_by_type('status')">
                        <option value="">All</option>
                        <option value="1">Published</option>
                        <option value="0">Unpublished</option>
                    </select>
                </li>
            </ul>
        </div>
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>
                    <th style="text-align:center" width="50">Sl.</th>
                    <th>Photo</th>
                    <th>Info</th>
                    <th>Category</th>
                    <th style="text-align:center" width="80">Status</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td align="center">{{ index+1 }}</td>
                        <td>
                            <span class="content">
                                <img v-if="item.content" :src="item.content" />
                                <i v-else class="fa fa-image"></i>
                            </span>
                        </td>
                        <td>
                            <div>{{ item.content_title }}</div>
                        </td>
                        <td>
                            <div>{{ item.cat_info && item.cat_info.category_name?item.cat_info.category_name:'' }}</div>
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
                    <tr><td colspan="5" align="center"><i class="fa fa-info-circle"></i> There is no data</td></tr>
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
        filter_by_type: function(type){
            if(this.$route.query.page) delete this.$route.query['page']

            if(this.$parent.filter[type]==''){
                let query = Object.assign({}, this.$route.query);
                delete query[type];
                this.$router.replace({ query });
            }else{
                let obj = {}; obj[type] = this.$parent.filter[type];
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            }
        },
        edit_item: function(val){
            this.$parent.edit_content_id = val
            this.$parent.add_new_entity(true)
            this.$parent.$parent.$parent.edit_route(val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/media-galleries/delete/' + id, this.$parent.header_config).then( (response) => {
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
    .filter_option_block{
        display: flex;
        height: 60px;
        border: 1px solid #ddd;
        background-color: #eee;
        margin-bottom: 15px;
        padding: 0 15px;
        border-radius: 3px;
    }
    .filter_option_block > span,
    .filter_option_block > ul{
        align-self: center;
    }
    .filter_option_block > span{
        padding-right: 15px; color: #444; font-size: 14px; border-right: 1px solid #ccc;
    }
    .filter_option_block > ul{
        display: flex;
        list-style: none;
        margin: 0; padding: 0;
        margin-left: auto;
        height: 30px
    }
    .filter_option_block > ul > li{
        /* display: flex; */
        align-self: center;
        /* height: 100%; */
        font-size: 12px;
        cursor: pointer;
        user-select: none;
        margin-left: 10px
    }
    .filter_option_block > ul > li label{
        margin-bottom: 3px
    }
    .filter_option_block > ul > li select{
        display: block;
        border: 1px solid #ddd;
        padding: 1px 5px; border-radius: 3px;
        max-width: 185px;
    }
    .content_list_view > table{
        border: 1px solid #ddd
    }
    .content_list_view > table td{
        vertical-align: middle;
    }
    .content{
        display: inline-block;
        width: 66px; height: auto; padding: 5px;
        line-height: auto; text-align: center;
        border: 1px solid #ddd; background-color: #fff;
        border-radius: 5px; overflow: hidden;
    }
    .content > img{
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .content > i{
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
