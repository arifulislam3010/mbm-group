<template>
    <div class="extra_instruction_block">
        <textarea-autosize
            :placeholder="'Enter your extra instruction'"                    
            v-model="$parent.extra_instruction"
            :min-height="30"
            :max-height="450"
        />
        <div><small>Example: Deliver with extra protection</small></div>
    </div>
</template>
<script>
export default {
    name: 'ExtraInsructionInfoBlock'
}
</script>
<style scoped>
    .extra_instruction_block{
        display: block;
        margin: 0 15px;
    }
    .extra_instruction_block > textarea{
        padding: 10px; border: 1px solid #ddd; border-radius: 3px;
        align-self: baseline;
        width: 100%;
    }
</style>