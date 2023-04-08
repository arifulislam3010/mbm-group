<template>
    <div id="popup_ads_modal_block">
        <div class="black_overlay">
            <div class="white_overlay">
                <span class="close_btn" @click="popup_close">x</span>
                <div class="content">
                    <nuxt-link :to="{ path: 'nextgenedu' }">
                        <img src="~/assets/images/ads/nextGenEDU.png" title="nextGenEDU" alt="nextGenEDU" />
                    </nuxt-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'PopupAdsModalBlock',
    mounted(){        
        document.body.classList.add('popup_open')
    },
    methods:{        
        popup_close: function(){
            this.$parent.popup_ads_show = false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style scoped>
    #popup_ads_modal_block{
        position: relative;
        height: 100%;
    }
    .white_overlay{
        width: 42%;
        left: 29%;
        top: 5%;
        /* height: 450px; */
        max-height: 100%;
    }    
    .content > a{
        display: block;
        width: 100%;
        height: 100%;
        font-family: "Hind Siliguri Light";
    }
    .content > a > img{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    @media(max-width: 480px){
        .white_overlay{
            width: 80%;
            left: 10%;
        }
    }
</style>