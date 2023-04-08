<template>
    <div class="basic_configuration_block">
        <div class="row">
            <div class="col-sm-6 header-title">
                <h4>{{ $strCapitalize(title) }}</h4>
            </div>
        </div>
        <nuxt-child ref="container_block" />
    </div>
</template>
<script>
import ContentHeader from './header-content-block'
export default {
    name: 'BasicConfigurationBlock',
    props: ['title'],
    components: {
        ContentHeader
    },
    data(){
        return {            
            total_rows: 0
        }
    }
}
</script>
<style scoped>

</style>