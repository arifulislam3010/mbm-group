<template>
    <div class="nav_list_block">
        <!-- {{ data }} -->
        <template v-if="pre_loader">
            <div v-for="(n,i) in limit" :key="i" class="item_list loader_list">                
                <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
            </div>
        </template>
        <template v-else-if="data.length>0">
            <div class="menuList">
                <div v-for="(item,index) in data" :key="index" :class="[{'submenu-option':item.sub_categories.length>0}]">
                    <nuxt-link :to="{path: '/'+ $store.state.search_page_path +'?category=' + item.slug}" class="item_list">
                        <span class="icon">
                            <img v-if="item.icon" :src="item.icon" :title="item.category_name" :alt="item.category_name" />
                            <!-- <img v-else src="~/assets/images/category-icon.png" title="Default Image" alt="Default Image"> -->
                            <i v-else class="bi bi-box"></i>
                        </span>
                        <span class="title text_overflow max_one_line_allow">{{ item.category_name }}</span>
                        <i v-if="item.sub_categories.length>0" class="fa fa-angle-right"></i>
                    </nuxt-link>
                    <div class="submenu">
                        <div v-for="(subitem,i) in item.sub_categories" :key="i" :class="[{'product-list-option':item.sub_categories.length>0}]">
                            <nuxt-link :to="{path: '/'+ $store.state.search_page_path +'?category=' + subitem.slug}" class="item_list">
                                <span class="icon">
                                    <img v-if="subitem.icon" :src="subitem.icon" :title="subitem.category_name" :alt="subitem.category_name" />
                                    <i v-else class="bi bi-box"></i>
                                </span>
                                <span class="title text_overflow max_one_line_allow">{{ subitem.category_name }}</span>
                            </nuxt-link>
                            <div class="product_list">
                                <div v-for="(product,pi) in subitem.product_infos" :key="pi">
                                    <nuxt-link :to="{path: $dtlProductUrl(product)}" class="item_list">
                                        <span class="icon">
                                            <i class="bi bi-bag"></i>
                                        </span>
                                        <span class="title text_overflow max_one_line_allow">{{ product.product_title }}</span>
                                        <em :class="['mode','mode-' + mi]" v-for="(mode_info,mi) in product.product_mode_infos" :key="mi">{{ $store.state.product_mode_types[mode_info.mode_id] }}</em>
                                    </nuxt-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import { mapActions, mapMutations } from 'vuex'
export default {
    name: 'NavListBlock',
    data(){
        return {
            pre_loader: false,
            data: [],
            user_access_token: this.$store.state.oauth_token.token,
            limit: 8
        }
    },
    mounted(){
        if($nuxt.isOnline){
            if(this.$store.state.category_info.get_data.length==0) this.load_data()
            else{
                this.data = this.$store.state.category_info.get_data
                localStorage.setItem('category_list', JSON.stringify(this.data))
            }
        }else{
            let getCacheData = localStorage.getItem('category_list')
            this.data = JSON.parse(getCacheData)
        }
        console.log(this.data);
    },
    methods: {
        ...mapActions({        
            getCategories: 'category_info/GET_DATA'
        }),
        async load_data(){
            // setup submitted data
            let config_data = {                
                access_token: this.user_access_token,
                limit: this.limit                
            }

            // content pre loader active
            this.pre_loader = true

            // call function for load data
            await this.getCategories(config_data)

            // get products list
            this.data = this.$store.state.category_info.get_data

            // cache store
            localStorage.setItem('category_list', JSON.stringify(this.data))

            // call for submit
            this.pre_loader = false
        }
    }
}
</script>
<style lang="scss" scoped>
    .menuList{ position: relative; z-index: 888;}
    .loader_list > div{
        width: 100%;
    }
    .item_list{
        display: flex;
        align-items: center;
        // flex-wrap: wrap;
        padding: 10px 15px;
        font-size: 13px;
        // border-bottom: 1px solid #ddd;
        cursor: pointer;
        & > .title{
            height: 20px;
            line-height: 22px;
            text-align: left;
            margin-left: 10px;
        }
        & > .mode{
            display: inline-block;
            padding: 1px 5px; margin-left: 5px; background-color: #666; color: #fff;
            font-size: 8px; border-radius: 25px;

            &.mode-0{
                background-color: #c3440a;
            }
            &.mode-1{
                background-color: #006699;
            }
            &.mode-2{
                background-color: #ff0000;
            }
            &.mode-3{
                background-color: #a400cd;
            }
        }
        & > .icon{
            display: inline-flex;
            align-self: center;        
            width: 20px; height: 20px;
            border-radius: 50%;
            img{
                width: 100%; height: 100%;
                object-fit: cover;
            }
            i{
                text-align: center;
                width: 100%;
                height: 100%;
                color: #ccc;
                line-height: 22px;
                border-radius: 50%;
                font-size: 20px;
            }
        }
        & > i{
            align-self: center;
            margin-left: auto;
        }
    }
    .submenu,.product_list{
        width: 100%;
        min-height: 400px;
        background-color: #fff;
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
        box-shadow: 0 0 4px #00000025;
    }
    .submenu-option:hover>div,.product-list-option:hover>div {
        display: block;
    }
</style>