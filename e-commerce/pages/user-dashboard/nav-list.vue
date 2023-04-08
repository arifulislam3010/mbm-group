<template>
    <div class="nav-list-block">
        <ul>
            <li :class="{active:$route.params.page==undefined}">
                <nuxt-link :to="{path: '/' + $store.state.user_dashboard_path}">
                    <span class="icon"><i class="fa fa-bars icon"></i></span>
                    <span class="text">Dashboard</span>
                    <i class="fa fa-angle-right next"></i>
                </nuxt-link>
            </li>
            <template v-for="(item,index) in $store.state.user_dashboard_nav_list">
                <template v-if="item.allow || item.allow_user_type==$store.state.auth_info.user_data.user_type">
                    <li :key="index" :class="{active:$route.params.page==item.page}">
                        <nuxt-link :to="{path: item.path}">
                            <span class="icon" v-html="item.icon"></span>
                            <span class="text">{{ item.name }}</span>
                            <i class="fa fa-angle-right next"></i>
                        </nuxt-link>    
                    </li>
                </template>
            </template>
        </ul>
    </div>
</template>
<script>
export default {
    name: 'NavListBlock'
}
</script>
<style lang="scss" scoped>
    .nav-list-block{
        display: block;
        & > ul{
            margin: 0; padding: 0; width: 250px;
            & > li{
                display: block;
                :deep(a){
                    display: flex; border-bottom: 1px solid #ddd;
                    padding: 8px 10px 8px 0;
                    .icon{
                        align-self: center;
                        width: 30px; height: 30px;
                        text-align: center; line-height: 30px;
                    }
                    .text{
                        align-self: center; margin-left: 10px;
                    }
                    .next{
                        align-self: center; margin-left: auto; color: #999;
                    }
                }
                &.active :deep(a){
                    background-image: linear-gradient(to left, #fff, transparent);
                    color: #ac3e13;
                    .next{
                        color: #ac3e13;
                    }
                }
            }
        }
    }
</style>