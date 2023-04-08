<template>
    <div class="product_slider_list_view_block">
        <div v-if="loader" class="row">            
            <div v-for="(n,i) in 4" :key="i" class="col-md-3">                
                <FormBlockLoader :cols="1" :height="165" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="150" :r2_h="0" />
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </div>
        <div v-else class="list">
            <carousel
                :paginationActiveColor="'#007499'"
                :paginationColor="'#CCC'"
                :paginationPadding="3"
                :perPage="1"
                :perPageCustom="PerPageCustom"
                :minSwipeDistance="16"
                :loop="true">
                <slide v-for="(item,index) in data" :key="index">
                    <div class="m-1">
                        <CardViewProduct :item="item" />
                    </div>
                </slide>
            </carousel>
        </div>
    </div>
</template>
<script>
import CardViewProduct from './CardViewProduct'
export default {
    name: 'ProductSliderListViewBlock',
    props: ['loader','data','pp_custom'],
    components: {
        CardViewProduct
    },
    data(){
        return {
            PerPageCustom: this.pp_custom?this.pp_custom:[[425,2],[768,3],[990,4],[1200,5]]
        }
    }
}
</script>