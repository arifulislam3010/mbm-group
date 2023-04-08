<template>
    <div class="live_link_block">
        <span>
            <a target="_blank" href="http://konnect.edu.bd/my-school">
                <img class="img-fluid" src="~/assets/images/konnect-live.jpg">
            </a>
        </span>
        <span>
            <a target="_blank" href="http://skills.gov.bd/tvet">
                <img class="img-fluid" src="~/assets/images/skills-logo-sonshodtv.png">
            </a>
        </span>
    </div>
</template>
<script>
export default {
    name: 'LiveLinkBlock'
}
</script>
<style scoped>
    .live_link_block{
        text-align: center;margin: -35px 0 0 -100px;position: relative;
    }
    .live_link_block :deep(span){
        display: inline-block; margin: 0 10px;
    }
    .live_link_block img{ max-height: 40px;}
    @media (max-width: 575.98px) {
        .live_link_block{
            margin: 15px auto;
        }
        .live_link_block img{ max-height: 30px;}
    }
    @media (min-width: 768px) {
        .live_link_block{
            margin: -10px auto 0;
        }
    }
    @media (min-width: 992px) {
        .live_link_block{
            margin: -10px auto 0;
        }
    }
</style>