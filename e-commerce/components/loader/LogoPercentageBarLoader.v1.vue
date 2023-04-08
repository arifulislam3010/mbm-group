<template>
    <div class="logo_percentage_bar_block" v-if="loadingPercent < 100">
        <div class="logo">
            <img src="/_ipx/images/logo.png" title="Logo - Preloader" alt="Logo - Preloader" />
        </div>
        <div class="progress-bar">
            <div class="fill" :style="{width:loaded}"></div>
            <div class="number">{{ loaded }}</div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LogoPercentageBarBlock',
    data(){
        return {
            loadingPercent: 0,
            loadTime: 0,
            interval: null
        }
    },
    computed: {
        loaded() {
            return this.loadingPercent + '%'
        }
    },
    watch: {
        loadingPercent(val) {
            if (val >= 100) {
                // console.log('complete');
                this.$parent.page_loader = false
                this.$parent.pre_loader = false
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
        margin-top: 6%;
        margin-bottom: 35px
    }
    .init_loader .logo img{
        width: 150px; object-fit: contain
    }
    .progress-bar{
        display: inline-block;
        position: relative;
        height: 8px;
        width: 250px;
        background-color: $green_op;
        color: $orange;
        border-radius: 25px;    
        text-align: center
    }
    .progress-bar .fill{
        position: absolute;
        border-radius: 25px;
        left: 0; top: 0; height: 100%;
        background-color: $orange;    
    }
    .progress-bar .number{
        position: relative;
        top: -7px;
        width: 45px;
        display: inline-block;
        background-color: $light_ash;
        font-size: 11px;
        font-weight: bold;
        line-height: 16px;
        color: $orange;
        z-index: 1;
    }
</style>