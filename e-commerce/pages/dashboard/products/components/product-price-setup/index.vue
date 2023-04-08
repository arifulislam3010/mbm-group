<template>
    <div class="product_price_setup_block">
        <label>Price Setup ({{ price_arr.length }})</label>
        <div class="info_form_block mt-1">
            <div v-if="price_arr.length==0" class="empty_block">
                <i class="fa fa-box-open fa-4x"></i>
                <div>No entry yet. Click on the button below to add one.</div>
            </div>
            <div v-else>
                <div class="info_list" v-for="(item,index) in price_arr" :key="index">
                    <HeaderBlock :cur_index="index" :title="item.title" />
                    <FromBlock v-if="selected_price_index==index" :data="item" :cur_index="index" />
                </div>
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
    name: 'ProductPriceSetupBlock',
    components: {
        HeaderBlock,
        FromBlock
    },
    data(){
        return {
            price_arr: this.$parent.formData.product_price_infos,
            selected_price_index: -1
        }
    },
    computed: {
        product_info_types: function(){
            let arr = [];
            this.$parent.product_price_type_list.forEach(v => {
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
        // this.$parent.load_product_price_types();
    },
    methods: {
        add_new_entry: function(){
            let obj = {
                index: '',
                id: '',
                title:'',
                price: 0,
                discount_price: 0,
                min_qty: 1,
                max_qty: 100,
                remakrs: ''
            }

            this.price_arr.push(obj);
            this.selected_price_index = this.price_arr.length - 1;
        },
        select_info_type: function(info_index){
            let get_index = this.price_arr[info_index].index
            this.price_arr[info_index].id = this.product_info_types[get_index].id
            this.price_arr[info_index].title = this.product_info_types[get_index].title
        },
        select_entry: function(index){
            if(this.selected_price_index==index) this.selected_price_index = -1
            else this.selected_price_index = index
        },
        del_entry: function(index){
            // delete this.price_arr[index]
            if(confirm('Are you sure to delete it?')) this.price_arr.splice(index, 1)
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
