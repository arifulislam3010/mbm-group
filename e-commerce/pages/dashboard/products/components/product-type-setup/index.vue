<template>
    <div class="product_type_setup_block">
        <label>Product Type Setup ({{ product_type_arr.length }})</label>
        <div class="info_form_block mt-1">
            <div v-if="product_type_arr.length==0" class="empty_block">
                <i class="fa fa-box-open fa-4x"></i>
                <div>No entry yet. Click on the button below to add one.</div>
            </div>
            <div v-else>
                <draggable v-model="product_type_arr" handle=".drag_cross" group="product_type_setup" @start="drag=true" @end="drag=false" @change="reorder_product_types()">
                    <div class="info_list" v-for="(item,index) in product_type_arr" :key="index">
                        <HeaderBlock :cur_index="index" :title="item.title" />
                        <FromBlock v-if="selected_product_type_index==index" :data="item" :cur_index="index" />
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
    name: 'ProductTypeSetupBlock',
    components: {
        HeaderBlock,
        FromBlock
    },
    data(){
        return {
            product_type_arr: this.$parent.formData.product_type_infos,
            selected_product_type_index: -1,
            ProductTypeNames: [],
            drag: false
        }
    },
    computed: {
        product_info_types: function(){
            let arr = {};
            this.$parent.product_type_list.forEach( (v,i) => {
                let obj = {
                    index: i,
                    id: v.id,
                    title: v.type_title,
                    open_entry: v.open_entry
                };
                arr[v.id] = obj
            })

            return arr;
        }
    },
    mounted() {
        if(this.product_type_arr.length>0) {
            this.product_type_arr.forEach((v,i) => {
                let getProductNames = [];                
                for(var ti=0;v.type_name[ti];ti++){
                    // console.log(v.type_name[ti],'-', this.$parent.product_type_names_info[v.id][ti])
                    getProductNames[ti] = {}
                    if(this.$parent.product_type_names_info[v.id][ti])
                    getProductNames[ti]['id'] = this.$parent.product_type_names_info[v.id][ti].id
                    getProductNames[ti]['tiClasses'] = []
                    getProductNames[ti]['text'] = v.type_name[ti]
                    getProductNames[ti]['tiClasses'][0] = 'ti-valid'
                }

                this.ProductTypeNames.push(getProductNames);
            })            
        }
    },
    methods: {        
        add_new_entry: function(){
            let obj = {
                index: '',
                id: '',
                title:'',
                type_name:'',
                open_entry: false
            }

            this.product_type_arr.push(obj);
            this.selected_product_type_index = this.product_type_arr.length - 1;
            this.ProductTypeNames.push([]);
        },
        reorder_product_types: function(){
            this.$parent.formData.product_type_infos = this.product_type_arr
        },
        select_info_type: function(info_index){
            let get_id = this.product_type_arr[info_index].id
            if(get_id === ''){
                this.product_type_arr[info_index].index = ''
                this.product_type_arr[info_index].title = ''
                this.product_type_arr[info_index].type_name = ''
                this.product_type_arr[info_index].open_entry = false
            }else{
                this.product_type_arr[info_index].index = this.product_info_types[get_id].index
                this.product_type_arr[info_index].title = this.product_info_types[get_id].title
                this.product_type_arr[info_index].type_name = ''
                this.product_type_arr[info_index].open_entry = this.product_info_types[get_id].open_entry
            }
        },
        select_entry: function(index){
            if(this.selected_product_type_index==index) this.selected_product_type_index = -1
            else this.selected_product_type_index = index
        },
        del_entry: function(index){
            // delete this.product_type_arr[index]
            if(confirm('Are you sure to delete it?')){                
                this.product_type_arr.splice(index, 1)
                this.ProductTypeNames.splice(index, 1)
            }
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
        font-size: 12px;
        font-weight: bold;
        color: #2d7686;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.4s;
    }
    .add_btn_block > span:hover{
        color: #006699;
    }
</style>