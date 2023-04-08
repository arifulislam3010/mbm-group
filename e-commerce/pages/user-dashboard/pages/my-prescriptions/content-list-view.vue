<template>
    <div class="content_list_view table-responsive">        
        <TableContentLoader v-if="content_loader" :cols="5" />
        <table v-else class="table table-striped">
            <thead>
                <tr>                    
                    <th>Title</th>
                    <th>Patient info</th>
                    <th>Image</th>                    
                    <th style="text-align:center" width="80">Status</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.length>0">
                    <tr v-for="(item,index) in data" :key="index">
                        <td>{{ item.prescription_title }}</td>
                        <td>
                            <div>{{ item.patient_name }}</div>
                            <div>Gender: {{ $store.state.gender_list[item.patient_gender_id] }}</div>
                            <div>Age: {{ item.patient_age }} Year(s)</div>
                        </td>
                        <td>
                            <div class="img" @click="$parent.prescription_view(item)">
                                <img :src="item.file_content" :title="item.prescription_title" :alt="item.prescription_title" />
                                <div class="view_block">
                                    <i class="fa fa-eye"></i>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="action_block">
                                <span @click="edit_item(item.id)"><i class="fa fa-edit"></i></span>
                                <span @click="del_item(item.id)"><i class="fa fa-trash-alt"></i></span>                                
                            </div>
                        </td>
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
        return {}
    },
    methods: {        
        edit_item: function(val){
            this.$parent.entry_form_open = true
            this.$parent.edit_content_id = val
            
            this.$router.push(this.$parent.cur_path + '?action=edit&id=' + val)
        },
        del_item: function(id){
            if(confirm('Are you sure to delete it?')){
                this.$axios.get('/api/my-prescriptions/delete/' + id, this.$parent.header_config).then( (response) => {
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
    table th,
    table td{
        font-size: 12px;
    }
    .img{
        display: inline-block;
        position: relative;
        width: 150px;
        height: 100px;
        background-color: #eee;
        border: 1px solid #ddd
    }
    .img > img{
        width: 100%;
        height: 100%;
        object-fit: contain
    }
    .img > .view_block{
        position: absolute;        
        width: 100%; height: 100%;
        left: 0; top: 0;
        background-color: #00000080;
        cursor: pointer;
        text-align: center;
        opacity: 0;
        z-index: 1;
        transition: all 0.4s
    }
    .img > .view_block > i{
        color: #fff;
        font-size: 16px;
        margin-top: 25%
    }
    .img:hover > .view_block{
        opacity: 1
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