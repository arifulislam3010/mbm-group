<template>
    <table class="table table-bordered">
        <thead>
            <tr class="table-header">
                <th v-for="(row,index) in cols" :key="index">
                    <vue-content-loading :height="25">
                        <rect x="0" y="0" rx="4" ry="4" width="100%" height="34" />
                    </vue-content-loading>
                </th>                
            </tr>
        </thead>
        <tbody>
            <tr>
                <td v-for="(row,index) in cols" :key="index">
                    <vue-content-loading :height="25">
                        <rect x="0" y="0" rx="4" ry="4" width="100%" height="30" />
                    </vue-content-loading>
                </td>                
            </tr>
        </tbody>
    </table>    
</template>
<script>
import VueContentLoading from 'vue-content-loading'
export default {
    name: 'TableContentLoader',
    props: ['cols'],
    components: {
        VueContentLoading
    }
}
</script>
<style scoped>
    .table-header{
        font-size: 14px;
        background-color: #f7f7f7 !important;
    }
</style>