<template>
    <div class="media_categories_list_block">
        <ContentHeader :title="title" :total_rows="total_rows" />
        <nuxt-child ref="container_block" />
    </div>
</template>
<script>
import ContentHeader from './header-content-block'
export default {
    name: 'MediaCategoryListBlock',
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