<template>
    <div :class="['product_type_list_block','mt-4',{mobile_device:$device.isMobile}]">
        <!-- <label>Product Types</label> -->
        <template v-if="pre_loader">
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in swiperOption.slidesPerView" :key="index">
                    <FormBlockLoader :cols="1" :height="300" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="300" :r2_h="0" />
                </swiper-slide>            
            </swiper>
        </template>
        <template v-else-if="data.length>0">
            <swiper class="swiper" :options="swiperOption">
                <swiper-slide v-for="(item,index) in data" :key="index">
                    <nuxt-link :to="{path: '/search?product-type=' + item.slug}" class="item_list">
                        <div class="icon">
                            <img v-if="item.icon" :src="item.icon" :title="item.type_title" :alt="item.type_title" />
                            <img v-else src="~/assets/images/empty-product.png" title="Default Image" alt="Default Image">
                            <!-- <i v-else class="fa fa-check"></i> -->
                        </div>                
                        <div class="title text_overflow max_two_lines_allow">{{ item.type_title }}</div>
                    </nuxt-link>
                </swiper-slide>
            </swiper>
        </template>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
export default {
    name: 'ProductTypeListBlock',
    data(){
        return {
            pre_loader: false,
            data: [],
            user_access_token: this.$store.state.oauth_token.token,
            swiperOption: {
                slidesPerView: this.$device.isMobile?4:12,
                spaceBetween: this.$device.isMobile?5:10,
                freeMode: true,
                grabCursor: true
            }           
        }
    },
    mounted() {
        if($nuxt.isOnline){
            if(this.$store.state.product_type.get_data.length==0) this.load_data();
            else this.data = this.$store.state.product_type.get_data;
        }else{
            let getCacheData = localStorage.getItem('product_type_list')
            this.data = JSON.parse(getCacheData)
        }
    },
    methods: {
        ...mapActions({        
            getProductTypes: 'product_type/GET_DATA'
        }),
        async load_data(){
            // setup submitted data
            let config_data = {                
                access_token: this.user_access_token                              
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getProductTypes(config_data)

            // get products list
            this.data = this.$store.state.product_type.get_data

            // cache store
            localStorage.setItem('product_type_list', JSON.stringify(this.data))

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style scoped>
    .product_type_list_block{
        display: block;
    }
    .product_type_list_block > label{
        font-size: 16px; font-weight: bold; color: #007499;
    }
    .item_list{
        display: block;
        width: 90px
    }
    .mobile_device .item_list{
        width: 80px
    }
    .item_list .icon{
        display: block;
        width: 100%; height: 60px;
        padding: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-bottom: none;
    }
    .item_list .icon img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .item_list .title{
        font-size: 10px;
        height: 40px;
        background-color: #fff;
        padding: 5px 10px;
        border: 1px solid #ddd;
        text-align: center
    }
</style>