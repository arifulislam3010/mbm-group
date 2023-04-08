<template>
    <div class="product_info_by_category">
        <label>
            <span>Category wise products</span>
            <i class="fa fa-angle-right ml-2"></i>
        </label>
        <div v-if="content_loader" class="loader_block">
            <i class="fa fa-chart-pie"></i>
            <div class="mt-3"><i class="fa fa-cog fa-spin"></i> Content Loading...Wait</div>
        </div>
        <div v-else-if="contents.length>0">
            <!-- <GChart
                    type="PieChart"
                    :data="chartData"
                    :options="chartOptions"
                /> -->
            <PieChart :chartOptions="canvasChartOptions" :content_type="'ProductCategory'" ref="pie_chart" />
        </div>
        <div v-else class="empty_block">
            <i class="fa fa-chart-pie"></i>
            <div class="mt-3">Sorry, No data found</div>
        </div>
    </div>
</template>
<script>
import PieChart from '~/components/chart/pie'
export default {
    name: 'ProductInfoByCategoryBlock',
    components: {
        PieChart
    },
    data(){
        return {
            content_loader: false,
            contents: [],
            chartData: [],
            chartOptions: {
                chart: {
                    title: 'Total products by category',
                    subtitle: 'Category and total post'                    
                },
                // is3D: true,
                // pieHole: 0.4,
                slices: {},
                legend: {
                    position: 'right',
                    textStyle: {
                        color: '#006699',
                        fontSize: 12
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'in',
                    startup: true
                }
            },
            canvasChartOptions: {
                theme: "light1",
                animationEnabled: true,
                // title:{
                //     text: "Chart of polling result"
                // },
                legend:{
                    cursor: "pointer",
                    // fontFamily: "SolaimanLipi",
                    // fontSize: 14,
                    // itemclick: explodePie
                },
                // subtitles: [{
                //     text: "Performance",
                //     fontStyle: "bold",
                //     verticalAlign: "center",
                //     dockInsidePlotArea: true
                // },{
                //     text: "Legend (On Hover)",
                //     fontStyle: "bold",
                //     verticalAlign: "top",
                //     horizontalAlign: "right",
                //     dockInsidePlotArea: true
                // }],
                toolTip:{
                    enabled: true,
                },
                data: [{
                    type: "doughnut",
                    innerRadius: '30',
                    showInLegend: false,
                    // toolTipContent: "{name}: <strong>{avg}%</strong>",
                    indexLabel: "{name} - {value}",
                    // indexLabelFontFamily: "SolaimanLipi",
                    // mousemove: function(e){
                    //     // Update the subtitle
                    //     e.chart.subtitles[0].set("text", e.dataPoint.label + " (" + e.dataPoint.y + "%)");
                    //     e.chart.subtitles[0].set("fontColor", e.dataPoint.color);

                    //     // Update the legend
                    //     e.chart.subtitles[1].set("text", e.dataPoint.legendLabel);
                    //     e.chart.subtitles[1].set("fontColor", e.dataPoint.color);
                    // },                    
                    dataPoints: [/*{
                        "y": 90,
                        "label": "In Range",
                        "color": "#218340",
                        // "legendLabel": "(Temp ≤ + 1°F)"
                    },{
                        "y": 7,
                        "label": "In Tolerance",
                        "color": "#F7B731",
                        "legendLabel": "(+ 1°F < Temp <= 2°F)"
                    },{
                        "y": 3,
                        "label": "Above Setpoint",
                        "color": "#A62337",
                        "legendLabel": "(Temp > + 2°F)"
                    }*/]
                }]
            }
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        async load_data(){
            let url = '/api/report-data/products-by-category';

            this.content_loader = true
            this.contents = [];
            this.$axios.get(url, this.$parent.header_config).then( (response) => {
                // console.log(response)
                if(response.data.data.length>0){
                    this.contents = response.data.data

                    /**
                     * CHART OPTIONS
                     */                    
                    var i = 0, max_index = -1, max_val = -1;
                    this.chartData[i++] = ['Category','Total Post'];
                    this.canvasChartOptions.data[0].dataPoints = []
                    this.contents.forEach(v => {                        
                        let get_total_post = parseInt(v.total_inserted);
                        if(get_total_post>max_val){
                            max_index = i; max_val = get_total_post;
                        }
                        if(v.category) this.chartData[i++] = [v.category.category_name,get_total_post];
                        else this.chartData[i++] = ['Unknown',get_total_post];

                        let getAvgValue = get_total_post; //((get_total_post * 100) / get_total_post).toFixed(2);

                        let obj = {
                            "y": getAvgValue,
                            "avg": getAvgValue, //this.$enToBnNumber(getAvgValue),
                            "value": get_total_post,
                            "label": v.category.category_name,
                            "name": v.category.category_name,
                            // "color": "#F7B731",
                            // "legendLabel": "(+ 1°F < Temp <= 2°F)"
                        }
                        this.canvasChartOptions.data[0].dataPoints.push(obj);
                    });

                    this.chartOptions.slices[max_index - 1] = {offset: 0.2}
                    this.canvasChartOptions.data[0].dataPoints[max_index - 1].exploded = true;
                    // console.log('ChartData', this.chartData)
                    // console.log('ChartOptions',this.chartOptions)                    
                }
                this.content_loader = false
            }).catch(e => {
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false
            });
        }
    }
}
</script>
<style scoped>
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
    .product_info_by_category{
        display: block;
    }
    .product_info_by_category > div{
        display: block;
        min-height: 200px;
        padding: 15px; background-color: #fff;
        border: 1px solid #ddd;
        /* border-radius: 5px; */
        /* box-shadow: 0 0 5px #ccc; */
    }
    .product_info_by_category > div.loader_block,
    .product_info_by_category > div.empty_block{
        text-align: center; padding: 35px 0; color: #999;
    }
    .product_info_by_category > div.loader_block > i,
    .product_info_by_category > div.empty_block > i{
        font-size: 72px; color: #ddd
    }
</style>