<template>
    <div class="photo_content_pre_loader_block">
        <FormBlockLoader :cols="1" :height="265" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="250" :r2_h="0" />
        <div class="row">
            <div v-for="(n,i) in 4" class="col-3" :key="i">
                <FormBlockLoader :cols="1" :height="250" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="240" :r2_h="0" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'PhotoContentPreLoaderBlock'
}
</script>
<style scoped>
    .photo_content_pre_loader_block{
        position: absolute;
        width: 100%;
        height: 100%;
    }
</style>