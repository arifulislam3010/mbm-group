<template>
    <div :class="['bottom_block',{mobile_view:$device.isMobile}]">
        <div class="container">
            <div class="row">
                <div class="col-md-4 pt-2 pb-2" align="left">
                    <div class="copyright">All rights reserved © {{ cur_year }}</div>
                </div>
                <div class="col-md-4 pt-2 pb-2" align="center">
                    <!-- <div class="powered_by">
                        <span>Powered by <a href="https://tamannapharmacy.com" target="_blank"><img src="/_ipx/images/powered-by.png" title="Powered by image" alt="Powered by image" /></a></span>
                    </div> -->
                </div>
                <div class="col-md-4 pt-2 pb-2" align="right">
                    <div class="developer_info">
                        <span>Developed by <a href="https://orangebd.com" target="_blank"><img src="/_ipx/images/developer.logo.png" title="Developer logo" alt="Developer logo" /></a></span>
                    </div>
                </div>
            </div>
        </div>
        <span :class="['back_to_top',{active:back_to_top}]" @click="scrollToTop"><i class="fa-solid fa-up-long"></i></span>
    </div>
</template>
<script>
export default {
    name: 'BottomFooter',
    data() {
        return {
            cur_year: new Date().getFullYear(),
            back_to_top: false,
        }
    },
    methods: {
        scrollToTop: function(){
            // window.scrollTo(0,0)
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }
    },
    mounted() {
        window.addEventListener("scroll", () => {
            let pageYOffset = window.pageYOffset
            if(pageYOffset>100) this.back_to_top = true
            else this.back_to_top = false
            // console.log(pageYOffset)
        });
    }
}
</script>
<style lang="scss" scoped>
    .bottom_block{
        display: block;
        padding: 10px 0;
        border-top: 1px solid #000;
        // background-color: #252d49;
        background-image: linear-gradient(to right, $sys_brand_gcolor_1, $sys_brand_gcolor_2);
        color: #eee;

        .developer_info img,
        .powered_by img{
            height: 18px
        }
        .powered_by img{
            height: 24px
        }

        .back_to_top{
            position: fixed;
            right: 20px; bottom: 20px;
            width: 40px; height: 40px;
            background-color: $sys_brand_color;
            color: #fff; line-height: 40px;
            border-radius: 50%;
            cursor: pointer;
            text-align: center;
            opacity: 0;
            transition: all 0.4s;
            z-index: -1;

            &.active{
                z-index: 9999;
                opacity: 0.6;
            }

            &:hover{
                background-color: purple;
            }
        }

        &.mobile_view{
            display: block;
            padding-bottom: 70px;

            .back_to_top{
                right: 10px; bottom: 60px;
            }
        }
    }
</style>
