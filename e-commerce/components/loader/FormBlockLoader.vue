<template>
    <div class="form_block_loader">
        <div v-for="(row,index) in cols" :key="index">
            <vue-content-loading :height="height">
                <rect v-if="r1" x="0" y="0" rx="0" ry="0" :width="r1_w_percent" :height="r1_h" />
                <rect v-if="r2" x="0" :y="r1_h + 10" rx="0" ry="0" :width="r2_w_percent" :height="r2_h" />
            </vue-content-loading>
        </div>
    </div>
</template>
<script>
import VueContentLoading from 'vue-content-loading'
export default {
    name: 'FormBlockLoader',
    props: ['cols','height','r1','r2','r1_w','r1_h','r2_w','r2_h'],
    components: {
        VueContentLoading
    },
    computed: {
        r1_w_percent: function(){
            return this.r1_w + '%'
        },
        r2_w_percent: function(){
            return this.r2_w + '%'
        }
    }
}
</script>
<style scoped>
    
</style>