<template>
    <div class="chart_view_block">
        <div class="chartContainer" :id="'chartContainer-' + content_type" ref="chartContainer"></div>
        <!-- <span class="hide_trial_top_left"></span>
        <span class="hide_trial_bottom_left"></span>
        <span class="hide_logo"></span> -->
    </div>
</template>
<script>	
    export default {
        name: 'PieChartBlock',
        props: ['chartOptions','content_type'],
        mounted() {
            if(this.content_type) this.load_data();
        },
        methods: {
            load_data(){
                var CanvasJS = require('~/assets/js/jquery.canvasjs.min');
                var chart = new CanvasJS.Chart("chartContainer-" + this.content_type, this.chartOptions);
                chart.render();
            }
        }
    }
</script>
<style scoped>
    .chart_view_block{
        position: relative;
        padding: 15px;
        width: 100%; height: 230px
    }
    .chartContainer{
        display: block;
        width: 100%;
        height: 100%;
    }
    .chart_view_block > .hide_trial_top_left{
        position: absolute;
        background-color: #fff;
        left: 0;
        top: 15px;
        width: 100px;
        height: 10px;
        z-index: 5;
    }
    .chart_view_block > .hide_trial_bottom_left{
        position: absolute;
        background-color: #fff;
        left: 0;
        bottom: 15px;
        width: 100px;
        height: 10px;
        z-index: 5;
    }
    .chart_view_block > .hide_logo{
        position: absolute;
        background-color: #fff;
        right: 0;
        bottom: 15px;
        width: 100px;
        height: 10px;
        z-index: 5;
    }
</style>