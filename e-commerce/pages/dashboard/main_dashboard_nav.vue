<template>
    <nav class="main-dashboard-nav">
        <nuxt-link :to="{ path: '/' + $store.state.dashboard.prefix }" :class="['nav-item',{active:this.$route.name==
        $store.state.dashboard.prefix}]"><span class="icon"><i class="fa fa-chart-line"></i></span> {{ $store.state.dashboard.name }}</nuxt-link>
        <template v-for="(list,caption) in dashboard_featured_list">
            <label :key="caption">{{ caption }}</label>
            <template v-for="(item,index) in list">
                <template v-if="access_featured_list[index]">
                    <nuxt-link v-if="item.position=='dashboard-left-nav'"
                        :key="caption + '-' + index"
                        :to="{ path: item.path }"
                        :class="['nav-item','text_overflow','max_one_line_allow',{active:current_route==item.route}]">
                        <span class="icon" v-html="item.icon"></span> {{ item.name }}
                    </nuxt-link>
                </template>
            </template>
        </template>
        <nuxt-link :to="{ path: '/' + $store.state.dashboard.prefix + '/change-password' }" :class="['nav-item',{active:this.$route.name==
        $store.state.dashboard.prefix + '-change-password'}]"><span class="icon"><i class="fa fa-exchange-alt"></i></span> Change Password</nuxt-link>
    </nav>
</template>
<script>
export default {
    name: 'DashboardNavigation',
    computed: {
        current_route: function(){
            return this.$route.name
        },
        dashboard_featured_list(){            
            return this.$store.state.dashboard_featured_list;
        },
        access_featured_list(){
            return this.$store.state.feature_access_list;
        }
    }
}
</script>
<style lang="scss" scoped>
    .main-dashboard-nav{
        position: relative;
        width: 100%; height: calc(100% - 60px);
        overflow-y: auto;
        :deep(label){
            display: block;
            padding: 5px 20px 3px;
            margin: 2px;
            color: lightblue;
            font-weight: bold;
            /* border-bottom: 1px solid #dddddd; */
        }
        .nav-item{
            display: block;
            padding: 10px 10px 8px 20px;
            font-size: 14px;
            text-decoration: none;
            color: #666;
            font-weight: 600;
            /* height: 31px; */
            transition: all 0.4s;
            &:hover,
            &.active{
                border-left: 3px solid #0097f7;
                padding-left: 17px;
                color: white !important;
            }
            .icon{
                display: inline-block;
                width: 20px;
                height: 20px;
                text-align: left;
            }
            i{
                color: #888;
            }
            &:hover i,
            &.active i{
                color: #0097f7;
            }
        }
    }
</style>