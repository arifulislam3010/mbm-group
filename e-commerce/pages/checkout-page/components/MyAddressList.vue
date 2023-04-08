<template>
    <div class="my_address_list_block">
        <div class="black_mask_overlay" @click="$parent.my_address_list_open=false"></div>
        <div class="content clearfix">
            <div class="info_header">
                <div class="info">
                    <h5>My address list</h5>
                </div>
                <div class="img">                    
                    <i class="fa fa-buildings" />
                </div>
            </div>
            <div class="list">
                <ManageAddressBlock :req_default_setup_index="$parent.req_default_setup_index" />
            </div>
        </div>
    </div>
</template>
<script>
import ManageAddressBlock from '~/pages/user-dashboard/pages/manage-address'
export default {
    name: 'MyaddressListBlock',
    data(){
        return {
            sel_address_info: ''
        }
    },
    watch: {
        sel_address_info: function(data){
            console.log('Get Address', data)
            this.$parent.change_address(data)
        }
    },
    components: {
        ManageAddressBlock
    }    
}
</script>
<style lang="scss" scoped>
    .my_address_list_block{
        display: block;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        z-index: 999;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content{
        position: fixed;
        background-color: #fff;
        min-width: 300px;
        max-width: calc(100% - 350px);
        height: 100%;
        overflow-y: auto;
        left: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
        z-index: 1000;
    }
    .info_header{
        display: flex;
        height: 60px; padding: 0 15px;
        border-bottom: 1px solid #ddd;
        .img{                
            align-self: center;
            margin-left: auto;
            :deep(img){
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            :deep(i){
                font-size: 24px;
                color: #aaa;        
            }
        }
        .info{        
            align-self: center;
            h4{
                margin: 0;
                font-size: 18px;
            }
            sup{
                font-size: 10px;
                padding: 3px 10px;
                /* border: 1px solid #ddd; */
                border-radius: 25px;
                background-color: #f16837;
                color: #fff;
            }
        }
    }
</style>