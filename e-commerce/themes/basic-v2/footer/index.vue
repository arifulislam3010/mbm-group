<template>
    <div class="footer_block mt-4">
        <div class="footer-theme-bg"></div>
        <ContentFooterSection :current_route="$parent.current_route" />        
        <BottomFooterSection />
    </div>
</template>
<script>
import ContentFooterSection from './ContentFooter'
import BottomFooterSection from './BottomFooter'
export default {
    name: 'FooterBlock',
    components: {
        ContentFooterSection,
        BottomFooterSection
    }
}
</script>
<style scoped>
    .footer-theme-bg{
        display: block;
        width: 100%; height: 250px;
        background-image: url(.~assets/images/footer-theme-bg.png);
        background-size: cover;
        background-position: bottom center;
    }
</style>