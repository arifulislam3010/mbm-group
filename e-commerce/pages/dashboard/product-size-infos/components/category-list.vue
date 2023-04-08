<template>
    <ul>
        <li v-for="(item,index) in items" :key="index">
            <div :class="['item_block clearfix',(cat_ids && cat_ids[item.id]?'active':'inactive')]">
                <div class="left_block" @click="select_category(item.id)">
                    <i v-if="cat_ids && cat_ids[item.id]" class="far fa-check-square active"></i>
                    <i v-else class="far fa-square"></i>
                    <span>{{ item.category_name }}</span>
                </div>
                <!-- <div>{{ cat_ids }}</div> -->
            </div>
            <CategoryDisplayLayout v-if="item.sub_categories" :cat_parent_ids="cat_parent_ids" :cat_ids="cat_ids" :items="item.sub_categories" />
        </li>
    </ul>
</template>
<script>
export default {
    name: 'CategoryDisplayLayout',
    props: {
        items: Array,
        cat_parent_ids: Object,
        cat_ids: Object
    },
    data(){
        return {
        }
    },
    mounted() {
        this.items.forEach((v,i) => {
            if(v.sub_categories.length>0){
                this.get_sub_parent_ids(v.id, v.sub_categories);
            }
        });        
    },
    methods: {
        get_sub_parent_ids: function(pid, items){            
            items.forEach((v,i) => {
                this.cat_parent_ids[v.id] = pid;
                if(v.sub_categories.length>0) this.get_sub_parent_ids(v.id, v.sub_categories);
            });
        },
        select_category: function(id){
            if(this.cat_ids[id]) delete this.cat_ids[id];
            else this.cat_ids[id] = true;
            if(this.cat_parent_ids[id])
                this.select_parent_category(id);
        },
        select_parent_category: function(id){
            let get_pid = this.cat_parent_ids[id];
            this.select_category(get_pid);
        }
    }
}
</script>
<style lang="scss" scoped>
    ul{
        margin: 0; padding: 0;

        li{
            list-style: none;
            & > ul{
                margin-left: 20px
            }

            & > div{
                margin: 5px;
            }
            :deep(i){
                position: relative;
                top: 2px;
                color: #bbb;
                margin-right: 5px;
                font-size: 18px;        
                transition: all 0.4s ease-in-out
            }
            &:hover > div {
                color: #444444;
                cursor: pointer;
                & > div :deep(i){
                    color: #666
                }
            }
        }
        :deep(i.active),
        & > div :deep(i:active),
        &:hover > div :deep(i:active){
            color: #b79324
        }
    }
</style>