<template>
    <div class="info_content_pre_loader_block">
        <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="15" :r2_h="0" />
        <FormBlockLoader :cols="1" :height="100" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="80" :r2_h="0" />
        <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="15" :r2_h="0" />
        <FormBlockLoader :cols="1" :height="100" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="80" :r2_h="0" />
    </div>
</template>
<script>
export default {
    name: 'InfoContentPreLoaderBlock'
}
</script>