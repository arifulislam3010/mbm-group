<template>
    <div :class="['logout_popup_view_block',{mobile_device:$device.isMobile}]">
        <div class="black_overlay"></div>
        <div class="white_overlay">
            <div>                    
                <div class="loader">
                    <div><i class="fa fa-cog fa-spin"></i> Wait...Logging off</div>                        
                </div>
                <div class="icon"><img src="~/assets/images/logout-icon.jpg" /></div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LogoutPopupViewBlock'
}
</script>
<style scoped>
    .white_overlay{
        position: fixed;
        width: 20%;
        left: 40%;        
        top: 25%;        
        z-index: 10000;
    }
    .mobile_device .white_overlay{
        width: 80%;
        left: 10%;
        top: 40%;
    }
    .white_overlay > div{
        display: flex;
        height: 70px
    }
    .white_overlay > div .icon{
        align-self: center;
        margin-left: auto;
        height: 100%;        
    }
    .white_overlay > div .icon > img{
        height: 100%;        
    }
    .white_overlay > div .loader{
        align-self: center; margin: 15px;
    }
</style>