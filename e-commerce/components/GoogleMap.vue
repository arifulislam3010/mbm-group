<template>
    <div class="google_map_display_block">        
        <iframe :src="$store.state.site_basic_config_data.google_map_page" title="Location on Google Map" frameborder="0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</template>
<script>
import { mapMutations } from 'vuex'
export default {
    name: "GoogleMapDisplayBlock",
    watch: {
        $route(to, from ) {
            this.google_map_allow_status(false)
        }
    },
    methods: {
        ...mapMutations({
            google_map_allow_status: 'GOOGLE_MAP_STATUS'
        })
    }
}
</script>
<style lang="scss" scoped>
    iframe{
        width: 100%;
        aspect-ratio: 16/9
    }
</style>