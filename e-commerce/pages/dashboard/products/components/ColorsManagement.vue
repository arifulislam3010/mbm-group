<template>
    <div class="ColorsManagementBlock">
        <!-- <pre>{{ color_list }}</pre> -->
        <div class="item" v-for="(item,index) in color_list" :key="index" :title="item.color_title" @click="sel_color_list(item)">
            <i :class="['fa','fa-check',{active:$parent.product_color_list[item.id]}]"></i>
            <div class="bg_color" :style="{'background-color':item.color_code}"></div>
        </div>
        <div style="display: none">{{ eventChanged }}</div>
    </div>
</template>
<script>
export default {
    name: 'ColorManagementBlock',
    props: ['color_list'],
    data(){
        return {
            eventChanged: false
        }
    },
    methods: {
        sel_color_list: function(data){
            if(this.$parent.product_color_list[data.id]) delete this.$parent.product_color_list[data.id]
            else this.$parent.product_color_list[data.id] = data
            this.eventChanged = this.eventChanged?false:true
        },
        sel_all_color: function(){
            this.color_list.forEach(v => {
                this.$parent.product_color_list[v.id] = v
                this.eventChanged = this.eventChanged?false:true
            });
        }
    }
}
</script>
<style lang="scss" scoped>
    .ColorsManagementBlock{
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;

        & > .item{
            display: flex;            
            position: relative;
            width: 46px; height: 46px;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 0 10px #ccc;
            border-radius: 50%;

            & > *{
                position: absolute;
                width: 100%; height: 100%;                                
            }

            & > i{
                opacity: 0;
                color: cyan;
                mix-blend-mode: difference;
                text-align: center;
                line-height: 46px;
                z-index: 5;
                transition: all 0.4s;
                &.active{
                    opacity: 1.0;
                }
            }
        }
    }
</style>