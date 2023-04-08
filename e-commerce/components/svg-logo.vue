<template>
    <div class="svg_logo_block">
        <!-- <div v-html="svgRawLogo"></div> -->
        <!-- <svgRawLogo /> -->
    </div>
</template>
<script>
// import svgRawLogo from "~/assets/images/main-site-logo.svg?inline";
export default {
    name: 'SvgLogoBlock',
    components: {
        // svgRawLogo
    },
    data(){
        return {
        }
    },
    mounted(){
        this.animatePath()
    },
    methods: {
        animatePath(){
            const anime = this.$anime
            // var path = anime.path('.svg_logo_block path')

            // anime({
            //     targets: '.svg_logo_block .lines path',
            //     strokeDashoffset: [anime.setDashoffset, 0],
            //     easing: 'easeInOutSine',
            //     duration: 1500,
            //     delay: function(el, i) { return i * 250 },
            //     direction: 'alternate',
            //     loop: true
            // })
        }
    }
}
</script>
<style scoped>
    .svg_logo_block{
        display: inline-block;
        width: 105px;
        height: 60px;
        line-height: 60px;
    }
</style>