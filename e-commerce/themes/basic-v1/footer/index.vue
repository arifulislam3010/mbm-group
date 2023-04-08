<template>
    <div class="footer_block mt-4">
        <!-- <div class="footer-theme-bg"></div> -->
        <GoogleMapSection v-if="$store.state.google_map_allow" />
        <ContentFooterSection :current_route="$parent.current_route" />
        <BottomFooterSection />
        <FBChat />
    </div>
</template>
<script>
import ContentFooterSection from './ContentFooter'
import BottomFooterSection from './BottomFooter'
import FBChat from '@/components/chat/FBChat'
import GoogleMapSection from '@/components/GoogleMap'
export default {
    name: 'FooterBlock',
    components: {
        GoogleMapSection,
        ContentFooterSection,
        BottomFooterSection,
        FBChat
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
