<template>
    <div class="product_rpt_by_operator">
        <label>
            <span>Product Entry Report By Operator</span>
            <i class="fa fa-angle-right ml-2"></i>
        </label>
        <div class="date_rang_block">
            <div class="caption">Date Range</div>
            <div class="input_block">
                <date-range-picker v-model="req_date_rang" format="YYYY/MM/DD" :options="range_options" />
            </div>
            <div class="action_btn" @click="load_data()">Go</div>
        </div>
        <div v-if="content_loader" class="loader_block">
            <i class="fa fa-chart-pie"></i>
            <div class="mt-3"><i class="fa fa-cog fa-spin"></i> Content Loading...Wait</div>
        </div>
        <div v-else-if="contents.length>0">
            <!-- {{ contents }} -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="text-align:center" width="50">Sl.</th>
                        <th>E-mail</th>
                        <th>New Inserted</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(data,index) in contents" :key="index">
                        <td>{{ (index+1) }}</td>
                        <td>                            
                            <!-- <div>Name: <em>{{ data.user.user_info.email }}</em></div> -->
                            <div><em>{{ data.user_info.email }}</em></div>
                        </td>
                        <th>{{ data.total_insert?data.total_insert:0 }}</th>
                        <th>{{ data.total_update?data.total_update:0 }}</th>
                    </tr>
                </tbody>
            </table>            
        </div>
        <div v-else class="empty_block">
            <i class="fa fa-chart-pie"></i>
            <div class="mt-3">Sorry, No data found</div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
export default {
    name: 'ProductRptByOperatorBlock',
    data(){
        return {
            content_loader: false,
            contents: [],
            req_date_rang: [moment().format("YYYY/MM/DD"), moment().format("YYYY/MM/DD")],
            range_options: {
                // singleDatePicker: true,
                // showDropdowns: true,
                minYear: 2021,
                maxYear: +moment().format("YYYY")
            }
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        async load_data(){
            let url = '/api/report-data/data-entry-opt-rpt';

            this.content_loader = true
            this.contents = [];
            this.$axios.get(url + '?req_date=' + this.req_date_rang, this.$parent.header_config).then( (response) => {
                // console.log(response.data.data)                
                let getData = response.data.data
                let getObj = {}
                getData.total_insert_by_opt.forEach( v => {

                    if(!getObj[v.created_by]){
                        getObj[v.created_by] = {}
                        getObj[v.created_by].user_info = v.operator_info
                    }

                    getObj[v.created_by].total_insert = v.total_insert
                })

                getData.total_update_by_opt.forEach( v => {

                    if(!getObj[v.created_by]){
                        getObj[v.created_by] = {}
                        getObj[v.created_by].user_info = v.operator_info
                    }

                    getObj[v.created_by].total_update = v.total_update
                })                

                for(var index in getObj) this.contents.push(getObj[index])

                // console.log(getObj)

                this.content_loader = false
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false
            });
        }
    }
}
</script>
<style lang="scss" scoped>
    label{
        display: flex;
        font-size: 14px;
        height: 20px;
        font-weight: bold;
        color: #999
    }
    label > span, label > i{
        align-self: center;
    }
    .product_rpt_by_operator{
        display: block;
    }
    .product_rpt_by_operator > div{
        display: block;
        min-height: 200px;
        padding: 15px; background-color: #fff;
        border: 1px solid #ddd;
        /* border-radius: 5px; */
        /* box-shadow: 0 0 5px #ccc; */
    }
    .product_rpt_by_operator > div.loader_block,
    .product_rpt_by_operator > div.empty_block{
        text-align: center; padding: 35px 0; color: #999;
    }
    .product_rpt_by_operator > div.loader_block > i,
    .product_rpt_by_operator > div.empty_block > i{
        font-size: 72px; color: #ddd
    }
    .date_rang_block{
        display: flex !important;
        height: 60px;
        min-height: 60px !important;
    }
    .date_rang_block > div{
        align-self: center;
    }
    .date_rang_block > div.caption{
        margin-left: auto;
    }
    .date_rang_block > div.input_block{
        margin-left: 10px;
        :deep(input){
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 3px;
            font-size: 13px;
        }
    }
    .date_rang_block > div.action_btn{
        margin-left: 10px;
        display: inline-block;
        background-color: #006699;
        color: #fff;
        border-radius: 5px;
        padding: 5px 15px;
        font-size: 12px;
        cursor: pointer;
    }
</style>