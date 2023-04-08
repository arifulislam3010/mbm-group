<template>
    <div id="content_block" class="mt-3">
        <div class="tab_list">
            <label><i class="far fa-handshake"></i> সহযোগী প্রতিষ্ঠান</label>
        </div>
        <div class="content_list mt-3">
            <div v-if="pre_loader">
                <div class="pre_loader">
                    <div class="row">
                        <div class="col-4 col-sm-2 mb-3" v-for="(n,i) in 12" :key="'list-' + i">
                            <FormBlockLoader :cols="1" :height="100" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="100" :r2_h="0" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="row justify-content-center mb-4">
                <template v-if="$store.state.partner_info.public_data.length>0">
                    <div v-for="(item,index) in $store.state.partner_info.public_data" :key="index" class="col-4 col-sm-2 d-flex justify-content-center">
                        <a class="item d-flex align-items-center mb-2 mb-md-4" :href="'/all-partners/' + item.username">
                            <img v-if="item.profile_photo!==null" class="img-fluid" :src="$imageUrl('user-profile-photo' + '/' + item.profile_photo)" :alt="item.name" :title="item.name" />
                            <span v-else>{{ item.name }}</span>
                        </a>
                    </div>
                </template>
                <div class="col-md-12">
                </div>
            </div>
            <!-- <div class="row justify-content-center mb-4">
                <div class="col d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/10-Minute-School.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/1200px-BRAC_logo.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/alokito-hridoy.jpg" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/durbinacademy.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/JAAGO-LOGO-300x211.jpg" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/teacher-batayon.png" />
                    </a>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/light-of-hope.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/MuktoPaath.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/Plan-logo.jpg" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/roomtoread.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/save-the-children.png" />
                    </a>
                </div>
                <div class="col-4 col-sm-2 d-flex justify-content-center">
                    <a class="item d-flex align-items-center" href="#">
                        <img class="img-fluid" src="~/assets/images/partners/UNICEF.png" />
                    </a>
                </div>
            </div> -->
        </div><!--end .content_list-->

        <!-- <div class="more_btn_block">
            <nuxt-link :to="{ path: '/all-partners' }"><i class="fa fa-link"></i> সব পার্টনার <i class="fa fa-angle-right"></i></nuxt-link>
        </div> -->
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
export default {
    name: 'LinkBlock',
    data(){
        return {
            pre_loader: false,
            items: [],
            limit: 12
        }
    },
    mounted(){
        if(this.$store.state.partner_info.public_data.length==0) this.load_partner_data();
    },
    methods: {
        ...mapActions({
            partnerList: 'partner_info/LOAD_DATA'
        }),
        async load_partner_data(){
            let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));

            // setup request data
            let request_data = {
                access_token: getTokenType.access_token
            }

            this.pre_loader = true;
            await this.partnerList(request_data);
            this.pre_loader = false;
        },
        // async load_content(){
        //     var url = '/api/partner-list?limit=' + this.limit;
        //     let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));

        //     this.pre_loader = true

        //     this.$axios.get(url,{
        //         headers: {
        //             'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
        //             'Content-Type': 'application/json'
        //         }
        //     }).then((response) => {
        //         console.log('Get partner list data',response.data)
        //         this.items = response.data.data
        //         this.pre_loader = false
        //     });
        // }
    }
}
</script>
<style scoped>
    #content_block{
        padding: 0;
    }
    .tab_list{
        font-size: 20px;
        font-family: "Hind Siliguri Light";
        font-weight: bold;
        border-bottom: 3px solid #ddd;
        text-align: center;
    }
    .tab_list > label{
        padding: 10px 0;
        color: purple;
        border-bottom: 3px solid purple;
        margin-bottom: -3px;
    }
    .tab_list > label i {
        margin-right: 5px;
    }
    .content_list .content_block{
        display: block;
        width:100%;
        position:relative;
    }
    .content_list :deep(a){
        display: block;
        height: 80px;
        color: purple
    }
    .content_list :deep(img){
        width: 100%; height: 100%;
        object-fit: contain;
    }
    .more_btn_block{
        text-align: right;
        border-bottom: 10px solid #efefef;
        position: relative;
        margin: 25px 0 0 0;
    }
    .more_btn_block > a{
        position: absolute;
        right: 0;
        top: -15px;
        background-color: #fff;
        display: inline-block;
        padding: 5px 0px 5px 15px;
        font-size: 20px;
        font-family: "Hind Siliguri Light";
        font-weight: bold;
        border-radius: 50px;
        color: purple;
        transition: all 0.4s;
    }
    .more_btn_block > a:hover{
        color: #006699;
    }
    @media (max-width: 575.98px){
        #content_block img {
           margin-bottom:10px;
        }
        .content_list a{ height:auto;}
    }
</style>
