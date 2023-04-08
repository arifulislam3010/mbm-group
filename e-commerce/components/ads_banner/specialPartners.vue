<template>
    <div class="container">
        <div class="specialPartners mt-5 mb-4">
            <div class="special_title mb-3">
                <div class="sp_title_logos">
                    <div class="row">
                        <div class="col pr-0">
                            <img src="~/assets/images/sp_top_1.png" class="img-fluid">
                        </div>
                        <div class="col pr-0">
                            <img src="~/assets/images/sp_top_2.png" class="img-fluid">
                        </div>
                        <div class="col">
                            <img src="~/assets/images/sp_top_3.png" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="special_content">
                <div class="row">
                    <div class="col-6 col-md-3">
                        <a href="https://www.youtube.com/channel/UCQOcnZ9ARYumlNIJGqmY1nw" target="_blank"><img src="~/assets/images/Ghora-Boshe-Shekha.png" class="img-fluid d-block mx-auto"></a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="http://konnect.edu.bd/my-school" target="_blank"><img src="~/assets/images/amar-ghora-amar-school.png" class="img-fluid d-block mx-auto"></a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="http://skills.gov.bd/tvet" target="_blank"><img src="~/assets/images/ghora-boshe-karigori-gori-shekha.png" class="img-fluid d-block mx-auto"></a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="http://konnect.edu.bd/my-school" target="_blank"><img src="~/assets/images/amar-ghora-manar-madrasa.png" class="img-fluid d-block mx-auto"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'specialPartnersBlock'
}
</script>
<style scoped>
    .specialPartners{ width: 100%;}
    .special_title{ border-top: 5px solid #5b075f;}
    .sp_title_logos{ max-width: 215px;margin: -28px 0 0 20px; background: #fff; padding: 0 15px;}
    .special_content{ border: 1px solid #5a065e; padding: 15px;}
    .special_content a{ border:1px solid #b997ba; display: block; padding: 15px;}
    .special_content a img{ max-height: 120px;}

    @media (max-width: 575.98px) {
        .sp_title_logos{ max-width: 180px; margin: -22px 0 0 10px;}
        .special_content a img{ max-height: 45px;}
        .special_content{ padding: 15px 15px 0 15px;}
        .special_content a{ margin: 0 0 15px 0; padding: 10px;}
    }
    @media (min-width: 576px) and (max-width: 767.98px) {
        .special_content a img{ max-height: 70px;}
    }
    @media (min-width: 768px) and (max-width: 991.98px) {
        .special_content a img{ max-height: 62px;}
    }
</style>