<template>
    <div :class="['main_container',{mobile_device:$device.isMobile}]">
        <TopSection v-if="current_route=='index'" />
        <nuxt-child />
    </div>
</template>
<script>
import TopSection from './components/TopSection'
export default {
    name: 'MainBodyBlock',
    components: {        
        TopSection
    },
    computed: {
        current_route: function(){
            return this.$route.name
        }
    }
}
</script>
<style scoped>
    .mobile_device{
        margin-top: 60px;
    }
</style>