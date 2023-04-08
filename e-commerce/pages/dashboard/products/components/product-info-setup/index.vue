<template>
    <div class="product_info_setup_block">
        <label>Information Setup ({{ info_arr.length }})</label>
        <div class="info_form_block mt-1">
            <div v-if="info_arr.length==0" class="empty_block">
                <i class="fa fa-box-open fa-4x"></i>
                <div>No entry yet. Click on the button below to add one.</div>
            </div>
            <div v-else>
                <draggable v-model="info_arr" handle=".drag_cross" group="product_info_setup" @start="drag=true" @end="drag=false" @change="reorder_product_infos()">
                    <div class="info_list" v-for="(item,index) in info_arr" :key="index">
                        <HeaderBlock :cur_index="index" :title="item.title" />
                        <FromBlock v-if="selected_info_index==index" :data="item" :cur_index="index" />
                    </div>
                </draggable>
            </div>
            <div class="add_btn_block">
                <span @click="add_new_entry"><i class="fa fa-plus"></i> Add New Entry</span>
            </div>
        </div>
    </div>
</template>
<script>
import HeaderBlock from './header-block'
import FromBlock from './form-block'
export default {
    name: 'ProductInfoSetupBlock',
    components: {
        HeaderBlock,
        FromBlock
    },
    data(){
        return {
            info_arr: this.$parent.formData.product_infos,
            selected_info_index: -1,
            drag: false
        }
    },
    computed: {
        product_info_types: function(){
            let arr = [];
            this.$parent.product_info_type_list.forEach(v => {
                let obj = {
                    id: v.id,
                    title: v.type_title
                };
                arr.push(obj);
            })

            return arr;
        }
    },
    mounted(){
        // this.$parent.load_product_info_types();
    },
    methods: {
        add_new_entry: function(){
            let obj = {
                index: '',
                id: '',
                title:'',
                // content: {
                //     html: '',
                //     css: ''
                // }
                content: []
            }

            this.info_arr.push(obj);
            this.selected_info_index = this.info_arr.length - 1;
        },
        reorder_product_infos: function(){
            this.$parent.formData.product_infos = this.info_arr
        },
        select_info_type: function(info_index){
            let get_index = this.info_arr[info_index].index
            this.info_arr[info_index].id = this.product_info_types[get_index].id
            this.info_arr[info_index].title = this.product_info_types[get_index].title
        },
        select_entry: function(index){            
            if(this.selected_info_index==index) this.selected_info_index = -1
            else this.selected_info_index = index            
        },
        del_entry: function(index){
            // delete this.info_arr[index]
            if(confirm('Are you sure to delete it?')) this.info_arr.splice(index, 1)
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
