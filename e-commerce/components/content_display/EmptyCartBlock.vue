<template>
    <div class="empty_cart_block">
        <!-- <div><i class="fa fa-box-open fa-4x"></i></div> -->
        <div><img src="~/assets/images/empty.svg" title="Empty Cart" alt="Empty Cart" /></div>
        <!-- <div class="mt-3">CART EMPTY</div> -->
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
        padding: 15% 0;
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        color: #999;
    }
    .empty_cart_block > div:first-child{
        font-size: 32px;
    }
    .empty_cart_block > div > i{
        color: #ddd;
    }
</style>