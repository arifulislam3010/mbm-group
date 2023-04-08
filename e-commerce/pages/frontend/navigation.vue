<template>
    <div class="nav_bar_block">
        <div class="container">
            <div class="nav_item_list_block">
                <div class="category_label_block" @click="nav_list_view?nav_list_view=false:nav_list_view=true">
                    <i class="fa fa-bars"></i>
                    <span>Categories</span>
                    <i class="fa fa-angle-down"></i>

                    <div v-if="$route.name!=='index' && nav_list_view" class="nav_list_block">
                        <NavList />
                    </div>
                </div>            
                <div class="static_nav_list">
                    <template v-if="pre_loader">
                        <ul class="row clearfix">
                            <li class="col-2" v-for="(n,i) in 3" :key="i">
                                <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="15" :r2_h="0" />
                            </li>
                        </ul>
                    </template>
                    <template v-else-if="$store.state.static_page_info.get_data.length>0">
                        <ul class="static_page_list">
                            <template v-for="(static_item,static_index) in $store.state.static_page_info.get_data">
                                <template v-if="static_item.display_on==2">
                                    <li :key="static_index">
                                        <nuxt-link :to="{path: '/page/' + static_item.slug}">{{ static_item.page_title }}</nuxt-link>
                                    </li>
                                </template>
                            </template>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import NavList from './components/NavList'
export default {
    name: 'NavBarBlock',
    components: {
        NavList
    },
    data(){
        return {
            nav_list_view: false,
            pre_loader: false,
            data: []
        }
    }    
}
</script>
<style lang="scss" scoped>
    .nav_bar_block{
        display: block; position: relative; margin-top: 60px;        
        background-color: $nav_bar_block_color;
        z-index: 10;
    }
    .nav_item_list_block{
        display: flex;
        height: 45px; line-height: 45px;
    }
    .nav_item_list_block > div{
        align-self: center;
    }
    .category_label_block{
        display: block;
        position: relative;
        width: 250px;
        padding: 0 25px;
        text-align: center;
        background-color: $nav_label_bg_color;
        color: $white;
        font-size: 16px;
        cursor: pointer;
        z-index: 99;
    }
    .category_label_block > span{
        display: inline-block; padding: 0 15px;
        text-transform: uppercase;
    }
    .nav_list_block{
        position: absolute;
        line-height: 12px;
        width: 100%; left: 0;
        min-height: 250px;   
        background-color: $white;
        // box-shadow: 0 0 5px #ccc;
        border: 1px solid #ddd;
        transition: all 0.4s;
    }
    ul.static_page_list{
        display: flex;
        padding: 0 15px;
        margin: 0;
        list-style: none;
    }
    ul.static_page_list > li > a{
        display: block; color: #fff; padding: 0 15px
    }
    ul.static_page_list > li > a:hover{
        background-color: $skyblue
    }
</style>