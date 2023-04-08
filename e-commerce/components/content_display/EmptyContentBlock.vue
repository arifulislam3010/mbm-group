<template>
    <div class="empty_cart_block">
        <div><i class="fa fa-box-open fa-4x"></i></div>        
        <div class="mt-3">
            <h6>Sad no result</h6>
            <span>We cannot find the item you are searching for, maybe a little spelling mistake?</span>
        </div>
    </div>
</template>
<script>
export default {
    name: 'EmptyCartBlock'
}
</script>
<style scoped>
    .empty_cart_block{
        display: block;
        margin: 8% 0;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        color: #999;
    }
    .empty_cart_block > div:first-child{
        font-size: 42px;
    }
    .empty_cart_block > div > i{
        color: #ddd;
    }
</style>