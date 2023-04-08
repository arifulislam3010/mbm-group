<template>
    <div class="logo_percentage_bar_block" v-if="loadingPercent <= 100">
        <div class="logo">
            <img src="/_ipx/images/logo.png" title="Logo - Preloader" alt="Logo - Preloader" />
        </div>
        <div v-if="slogan" class="slogan mt-2">
            <span>{{ slogan }}</span>
        </div>
        <div class="progress-bar">
            <div class="fill" :style="{width:loaded + '%'}"></div>
            <div class="center-bar-block">
                <div class="number">{{ loaded }}%</div>
                <div class="center-bar">
                    <div class="center-bar-fill" :style="{width:center_loaded + '%'}"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LogoPercentageBarBlock',
    props: ['slogan'],
    data(){
        return {
            loadingPercent: 0,
            loadTime: 0,
            mainBarLen: 250,
            centerLoaderLen: 55,
            centerLoaderStart: 40,            
            interval: null
        }
    },
    computed: {
        loaded() {
            return this.loadingPercent
        },
        center_loader_len() {
            return (this.centerLoaderLen * 100) / this.mainBarLen
        },
        center_loaded() {
            let getCenterPercent = 0
            if(this.loadingPercent>this.centerLoaderStart){
                getCenterPercent = ((this.loadingPercent - this.centerLoaderStart) * 100) / this.center_loader_len
            }
            return (getCenterPercent<=100 ? getCenterPercent : 100)
        }
    },
    watch: {
        loadingPercent(val) {
            if (val >= 100) {
                // console.log('complete');
                this.$parent.page_loader = false
                // this.$parent.pre_loader = false
                clearInterval(this.interval)
            }
        }
    },
    mounted() {
        let perfData = window.performance.timing;
        let estimatedTime = Math.abs(perfData.loadEventEnd - perfData.navigationStart);
        this.loadTime = parseInt((estimatedTime / 1000) % 60) * 100;
        this.doProgress();
        // console.log(this.loadTime)
    },
    methods: {
        doProgress() {
            let step = this.loadTime / 100;
            this.interval = setInterval(() => {
                this.loadingPercent++
            }, step);
        }
    }
}
</script>
<style lang="scss" scoped>
    .logo_percentage_bar_block{
        display: block;
    }
    .init_loader .logo{
        margin-top: 5%;        
    }
    .init_loader .slogan{
        font-size: 12px
    }
    .init_loader .logo img{
        width: 150px; object-fit: contain
    }
    .progress-bar{
        display: inline-block;
        position: relative;
        height: 5px;
        width: 250px;
        margin-top: 35px;
        background-color: $progress_bar_color;
        color: $sys_brand_color;
        border-radius: 25px;    
        text-align: center
    }    
    .progress-bar .fill{
        position: absolute;
        border-radius: 25px;
        left: 0; top: 0; height: 100%;
        background-color: $progress_bar_fill_color;    
    }
    .progress-bar .center-bar-block{
        position: relative;
        display: inline-block;
        top: -25px;
        width: 55px;
        height: 55px;        
    }
    .progress-bar .number{        
        position: absolute;
        width: 45px;
        height: 45px;
        top: 0px;
        text-align: center;
        background-color: $progress_number_bg_color;
        font-size: 11px;
        font-weight: bold;
        line-height: 45px;
        border-radius: 50%;
        color: $progress_number_color;
        margin: 5px;
        z-index: 1;
    }
    .progress-bar .center-bar{
        position: absolute;
        left: 0;
        bottom: 0;
        height: 100%;
        width: 100%;
        border-radius: 50%;
        background-color: $ash;
        overflow: hidden
    }
    .progress-bar .center-bar-fill{
        position: absolute;
        left: 0;
        bottom: 0;
        height: 100%;
        width: 100%;
        // border-radius: 50%;
        background-color: $progress_bar_fill_color;
    }
</style>