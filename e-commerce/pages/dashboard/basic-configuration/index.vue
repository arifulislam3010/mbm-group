<template>
    <div class="basic_configuration_block">
        <template v-if="content_loader">
            <div class="form_block">
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </template>
        <template v-else>
            <label>Website Title</label>
            <div class="mb-4">
                <input type="text" v-model="formData.website_title" placeholder="Enter the website title" class="form-control" ref="website_title" />
            </div>

            <label>Website Description</label>
            <div class="mb-4">
                <textarea-autosize
                    :placeholder="'Enter the website description'"                    
                    v-model="formData.website_desc"
                    :min-height="30"
                    :max-height="450"                
                />
            </div>

            <label>Meta Keywords</label>
            <div class="mb-4">
                <textarea-autosize
                    :placeholder="'Enter the meta keywords'"                    
                    v-model="formData.meta_keywords"
                    :min-height="30"
                    :max-height="450"                
                />
            </div>

            <!-- <label>Meta Tags</label>
            <div class="mb-4">
                <textarea-autosize
                    :placeholder="'Enter the meta tags'"                    
                    v-model="formData.meta_tags"
                    :min-height="30"
                    :max-height="450"                
                />
            </div> -->

            <label>Not found Page Text</label>
            <div class="mb-4">
                <textarea-autosize
                    :placeholder="'Enter the not found page text'"                    
                    v-model="formData.not_found_page_txt"
                    :min-height="30"
                    :max-height="450"                
                />
            </div>
            
            <label>Printer Line Text</label>
            <div class="mb-4">     
                <div class="text_editor_block">       
                    <vue-editor v-model="formData.printer_line_txt" placeholder="Enter the printer line info" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Android Play Store Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.android_play_store" placeholder="Enter the play store link url" class="form-control" />
                    </div>

                    <label>IOS App Store Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.ios_app_store" placeholder="Enter the app store link url" class="form-control" />
                    </div>

                    <label>Facebook Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.fb_page" placeholder="Enter the facebook page link url" class="form-control" />
                    </div>

                    <label>Twitter Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.twitter_page" placeholder="Enter the twitter page link url" class="form-control" />
                    </div>

                    <label>Linkedin Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.linkedin_page" placeholder="Enter the linkedin page link url" class="form-control" />
                    </div>

                    <label>Instagram Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.instagram_page" placeholder="Enter the instagram page link url" class="form-control" />
                    </div>

                    <label>Youtube Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.youtube_page" placeholder="Enter the youtube page link url" class="form-control" />
                    </div>

                    <label>Google Map Page Link</label>
                    <div class="mb-4">
                        <input type="text" v-model="formData.google_map_page" placeholder="Enter the google map page link url" class="form-control" />
                    </div>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend>Third Party Auth Access Info</legend>
                        <div class="info">
                            <label>Google Api Key</label>
                            <div class="mb-4">
                                <input type="text" v-model="formData.third_party_auth_info.google_api_key" placeholder="Enter the google api key" class="form-control" />
                            </div>

                            <label>Google Client ID</label>
                            <div class="mb-4">
                                <input type="text" v-model="formData.third_party_auth_info.google_client_id" placeholder="Enter the google client ID" class="form-control" />
                            </div>

                            <label>Facebook Page ID</label>
                            <div class="mb-4">
                                <input type="text" v-model="formData.third_party_auth_info.fb_page_id" placeholder="Enter the facebook page ID" class="form-control" />
                            </div>

                            <label>Facebook App ID</label>
                            <div class="mb-4">
                                <input type="text" v-model="formData.third_party_auth_info.fb_app_id" placeholder="Enter the facebook app ID" class="form-control" />
                            </div>
                        </div>
                    </fieldset>                    
                </div>
            </div>
            
            <hr />
            <div align="right" class="pr-3">
                <div v-if="pre_loader"><i class="fa fa-cog fa-spin"></i> Submitting...wait</div>
                <div v-else class="submit_btn" @click="submit_form"><i class="fa fa-save mr-1"></i> Save</div>
            </div>
        </template>
    </div>
</template>
<script>
export default {
    name: 'ChangePasswordBlock',
    data(){
        return {
            content_loader: false,
            pre_loader: false,
            formData: {
                website_title: '',
                website_desc: '',
                meta_keywords: '',
                meta_tags: '',
                not_found_page_txt: '',
                printer_line_txt: '',
                android_play_store: '',
                ios_app_store: '',
                fb_page: '',
                twitter_page: '',
                linkedin_page: '',
                instagram_page: '',
                youtube_page: '',
                google_map_page: '',
                third_party_auth_info: {
                    google_api_key: '',
                    google_client_id: '',
                    fb_page_id: '',
                    fb_app_id: ''
                }
            },            
            user_access_token: this.$store.state.auth_info.user_data.token
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.user_access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.user_access_token
                }
            };
            return obj;
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        load_data: function(){
            this.content_loader = true
            this.$axios.get('/api/site-basic-config', this.header_config).then( async (response) => {
                console.log('Get Data', response.data)
                let getData = response.data.data;
                if(getData) {
                    this.formData = {
                        website_title: getData.website_title,
                        website_desc: getData.website_desc,
                        meta_keywords: getData.meta_keywords,
                        meta_tags: getData.meta_tags,
                        not_found_page_txt: getData.not_found_page_txt,
                        printer_line_txt: getData.printer_line_txt,
                        android_play_store: getData.android_play_store,
                        ios_app_store: getData.ios_app_store,
                        fb_page: getData.fb_page,
                        twitter_page: getData.twitter_page,
                        linkedin_page: getData.linkedin_page,
                        instagram_page: getData.instagram_page,
                        youtube_page: getData.youtube_page,
                        google_map_page: getData.google_map_page,
                        third_party_auth_info: {
                            google_api_key: getData.third_party_auth_info && getData.third_party_auth_info.google_api_key?getData.third_party_auth_info.google_api_key:'',
                            google_client_id: getData.third_party_auth_info && getData.third_party_auth_info.google_client_id?getData.third_party_auth_info.google_client_id:'',
                            fb_page_id: getData.third_party_auth_info && getData.third_party_auth_info.fb_page_id?getData.third_party_auth_info.fb_page_id:'',
                            fb_app_id: getData.third_party_auth_info && getData.third_party_auth_info.fb_app_id?getData.third_party_auth_info.fb_app_id:'',
                        }
                    }

                    this.content_loader = false;
                }else{
                    this.$toast.error(response.data.msg, {icon: "error_outline"})
                    this.content_loader = false;
                }
            }).catch(e => {
                // console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false;
            });
        },
        submit_form: function(){
            if(this.formData.website_title.trim()==''){
                this.$toast.error('Please enter website title', {icon: "Warning"});
                this.$refs.website_title.focus();
                return false;
            }

            if(confirm('Are you sure to submit it?')){

                this.pre_loader = true;
                
                this.$axios.post('/api/site-basic-config', this.formData, this.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    if(response.data.status) {                        
                        this.pre_loader = false;

                        await this.$swal("Good job!", "Site baasic configuration has been saved successfully.", "success");                    
                    }else{
                        this.$toast.error(response.data.msg, {icon: "error_outline"})
                        this.pre_loader = false;                
                    }
                }).catch(e => {
                    // console.log(e)
                    this.$toast.error('Failed!!!', {icon: "error_outline"})
                    this.pre_loader = false;
                });
            }
        },
        reset_form: function(){
            this.formData = {
                website_title: '',
                website_desc: '',
                meta_keywords: '',
                meta_tags: '',
                not_found_page_txt: '',
                printer_line_txt: '',
                android_play_store: '',
                ios_app_store: '',
                fb_page: '',
                twitter_page: '',
                linkedin_page: '',
                instagram_page: '',
                youtube_page: '',
                google_map_page: ''
            }            
        }
    }
}
</script>
<style lang="scss" scoped>
    .basic_configuration_block{
        padding: 25px 0
    }
    label{
        font-size: 12px;
    }
    textarea{
        border: 1px solid #ddd;        
        border-radius: 3px;
        padding: 5px 10px;
        width: 100%;
    }
    .text_editor_block{
        background-color: #fff;
    }

    fieldset{
        border: 1px solid #ccc;
        padding: 0 20px;
        legend{
            font-size: 14px;
            font-weight: 600;
            color: #666;
            padding: 0 10px;
            width: auto;
        }
    }
    .submit_btn{        
        display: inline-block;
        padding: 3px 15px;
        font-size: 13px;
        font-weight: bold;
        background-color: #ff6600;
        color: #fff;
        cursor: pointer;
        text-align: center;
        border-radius: 25px;
        transition: all 0.4s;
    }
    .submit_btn:hover{
        background-color: #cd5200;
    }
</style>