<template>
    <div class="content_list_view table-responsive">
        <TableContentLoader v-if="content_loader" :cols="5" />
        <template v-else-if="data.length>0">
            <div class="head_section">
                <div>Prescription list</div>
                <div>Total: {{ data.length }}</div>
            </div>
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <div class="prescription_title">{{ item.prescription_title }}</div>
                    <div class="list">
                        <div class="content_block">
                            <div class="patient_info">
                                <div class="patient_name">{{ item.patient_name }}</div>
                                <div>Gender: {{ $store.state.gender_list[item.patient_gender_id] }}</div>
                                <div>Age: {{ item.patient_age }} Year(s)</div>
                            </div>
                            <div class="prescription_img">
                                <div class="img" @click="$parent.prescription_view(item)">
                                    <img :src="item.file_content" :title="item.prescription_title" :alt="item.prescription_title" />
                                    <div class="view_block">
                                        <i class="fa fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div align="right" class="bottom_block">                            
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
            swiperOption: {
                slidesPerView: this.per_view?this.per_view:1,
                spaceBetween: 10,
                freeMode: true,
                grabCursor: true
            }
        }
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
    .list .content_block{
        display: flex;        
    }    
    .list .content_block > div{
        align-self: center
    }
    .list .content_block > div.patient_info{
        margin-right: 15px;
    }
    .list .content_block > div.prescription_img{
        width: 80px; height: 80px; background-color: #ccc; margin-left: auto
    }
    .list .content_block > div.prescription_img > .img{
        position: relative;
        width: 100%; height: 100%;
    }
    .list .content_block > div.prescription_img > .img img{
        width: 100%;
        height: 100%;
        object-fit: contain;
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
    .prescription_title{
        display: block;
        font-size: 14px;
        background-color: #f16837;
        color: #fff;        
        margin-top: 5px;
        border-radius: 5px 5px 0 0 ;
        padding: 8px 15px
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
    .action_block{
        margin-left: auto;
        text-align: right;
    }
    .action_block > span{
        display: inline-block;
        margin: 0 5px;
    }
</style>