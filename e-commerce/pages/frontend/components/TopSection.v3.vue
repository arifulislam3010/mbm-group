<template>
    <div class="top_section_block">
        <div class="container">
            <div class="clearfix">
                <!-- <div class="left-section float-left">                    
                    <NavList />
                </div> -->
                <div class="right-section float-left">                    
                    <BannerSection class="mt-4" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
// import NavList from './NavList'
import BannerSection from './BannerList.v3'
export default {
    name: 'TopSectionBlock',
    components: {
        // NavList,
        BannerSection
    }
}
</script>
<style scoped>
    .left-section{
        display: block;
        width: 250px; height: 400px;   
        background-color: #fff;
        box-shadow: 0 0 5px #ccc;
        _overflow-y: auto;
    }
    .right-section{
        display: block;
        /* width: calc(100% - 275px); */
        width: 100%;
        /* margin-left: 25px; */
    }
    @media screen and (max-width: 426px) {
        .left-section{
            display: none
        }
        .right-section{
            width: 100%;
            margin: 0;
        }
    }
</style>