<template>
    <div class="product_card_vew_pre_loader_block">
        <FormBlockLoader :cols="1" :height="150" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="140" :r2_h="0" />
        <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="20" :r2_h="0" />
        <FormBlockLoader :cols="1" :height="30" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="20" :r2_h="0" />
    </div>
</template>
<script>
export default {
    name: 'ProductCardViewPreLoaderBlock'
}
</script>