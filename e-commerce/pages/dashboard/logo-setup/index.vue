<template>
    <div class="logo_setup_block">
        <template v-if="content_loader">
            <div class="form_block">
                <FormBlockLoader :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
                <FormBlockLoader class="mt-2" :cols="1" :height="65" :r1="true" :r2="true" :r1_w="50" :r2_w="100" :r1_h="20" :r2_h="25" />
            </div>
        </template>
        <template v-else>
            <div class="row">
                <div class="col-md-4">
                    <label>Site Main Logo</label>
                    <div class="mb-4">
                        <!-- <LogoInputBlock /> -->
                        <ImageInputBlock :column="'logo'" />
                    </div>
                </div>

                <div class="col-md-4">
                    <label>Logo for dark background</label>
                    <div class="mb-4">
                        <ImageInputBlock :column="'dark_logo'" />
                        <!-- <DarkLogoInputBlock /> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Watermark Logo</label>
                    <div class="mb-4">
                        <!-- <WatermarkLogoInputBlock /> -->
                        <ImageInputBlock :column="'watermark_logo'" />
                    </div>
                </div>

                <div class="col-md-4">
                    <label>Favicon Logo</label>
                    <div class="mb-4">
                        <!-- <FaviconInputBlock /> -->
                        <ImageInputBlock :column="'favicon'" />
                    </div>
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
import LogoInputBlock from './components/logo_input_block'
import DarkLogoInputBlock from './components/dark_bg_logo_input_block'
import WatermarkLogoInputBlock from './components/watermark_logo_input_block'
import FaviconInputBlock from './components/favicon_input_block'
import ImageInputBlock from './image-input-block'
export default {
    name: 'LogoSetupBlock',
    components:{
        LogoInputBlock,
        DarkLogoInputBlock,
        WatermarkLogoInputBlock,
        FaviconInputBlock,
        ImageInputBlock
    },
    data(){
        return {
            content_loader: false,
            pre_loader: false,
            formData: {
                logo: null,
                exist_logo: null,
                dark_logo: null,
                exist_dark_logo: null,
                watermark_logo: null,
                exist_watermark_logo: null,
                favicon: null,
                exist_favicon: null
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
            this.$axios.get('/api/logo-info', this.header_config).then( async (response) => {
                console.log('Get Data', response.data.data)
                let getData = response.data.data;
                
                this.formData = {
                    logo: getData.logo,
                    exist_logo: getData.exist_logo,
                    dark_logo: getData.dark_logo,
                    exist_dark_logo: getData.exist_dark_logo,
                    watermark_logo: getData.watermark_logo,
                    exist_watermark_logo: getData.exist_watermark_logo,
                    favicon: getData.favicon,
                    exist_favicon: getData.exist_favicon
                }

                this.content_loader = false;
            }).catch(e => {
                // console.log(e)
                this.$toast.error('Failed!!!', {icon: "error_outline"})
                this.content_loader = false;
            });
        },
        submit_form: function(){
            if(confirm('Are you sure to submit it?')){

                this.pre_loader = true;
                
                this.$axios.post('/api/logo-info', this.formData, this.header_config).then( async (response) => {
                    console.log('Get Data', response.data)

                    if(response.data.status) {                        
                        this.pre_loader = false;

                        await this.$swal("Good job!", "Logo setup has been saved successfully.", "success");                    
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
                logo: null,
                exist_logo: null,
                dark_logo: null,
                exist_dark_logo: null,
                watermark_logo: null,
                exist_watermark_logo: null,
                favicon: null,
                exist_favicon: null
            }            
        }
    }
}
</script>
<style scoped>
    .logo_setup_block{
        padding: 25px 0
    }
    label{
        font-size: 12px;
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