<template>
    <div :class="['bottom_block',{mobile_view:$device.isMobile}]">
        <div class="container">
            <div class="row">
                <div class="col-md-6 pt-2 pb-2" align="left">
                    <div class="copyright">All rights reserved © {{ cur_year }}</div>
                </div>
                <div class="col-md-6 pt-2 pb-2" align="right">
                    <div class="developer_info">
                        <span>Developed by <a href="https://orangebd.com" target="_blank"><img src="~/assets/images/orangebd.png" /></a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'BottomFooter',
    data() {
        return {
            cur_year: new Date().getFullYear()
        }
    }
}
</script>
<style scoped>
    .bottom_block{
        display: block;
        padding: 10px 0;
        border-top: 1px solid #000;
        background-color: #252d49;
        color: #eee;
    }
    .bottom_block.mobile_view{
        display: block;
        padding-bottom: 70px
    }
    .developer_info img{
        height: 18px;
    }
</style>