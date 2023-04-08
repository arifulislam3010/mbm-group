<template>
    <div class="star_rating_block">        
        <template v-if="allow_edit">
            <span v-for="(n,i) in len" :key="i" :class="['rating_point_icon_item',size?size:'']" @mouseover="move_star_rating(n)" @mouseout="move_star_rating(0)" @click="$parent.select_star_rating(n)">
                <i v-if="(rating_mouse_over && n <= $parent.sel_rating) || (!rating_mouse_over && n <= cur_rating?cur_rating:0)" class="active fa fa-star fa-2x" />
                <i v-else class="far fa-star fa-2x" />
            </span>
        </template>
        <template v-else>
            <span v-for="(n,i) in len" :key="i" :class="['rating_point_icon_item',size?size:'']">
                <i v-if="n <= cur_rating?cur_rating:0" class="active fa fa-star fa-2x" />
                <i v-else class="far fa-star fa-2x" />
            </span>
        </template>
    </div>
</template>
<script>
export default {
    name: 'StarRatingBlock',
    props: ['cur_rating','len','size','editable'],
    data(){
        return {
            allow_edit: this.editable?true:false,
            rating_mouse_over: false
        }
    },
    methods: {
        move_star_rating: function(val){
            this.$parent.sel_rating = val

            if(this.$parent.sel_rating>0) this.rating_mouse_over = true
            else this.rating_mouse_over = false
        }    
    },
}
</script>
<style scoped>
    .star_rating_block{
        display: inline-block;
        white-space: nowrap;
    }
    .rating_point_icon_item{
        display: inline-block;
        font-size: 16px;
        color: #ccc;
        padding: 2px;
        cursor: pointer;
    }
    .rating_point_icon_item.small{
        font-size: 10px;
    }
    .rating_point_icon_item.medium{
        font-size: 13px;
    }
    .rating_point_icon_item i.active,
    .rating_point_icon_item:hover{
        color: rgb(192, 106, 0);
    }
</style>