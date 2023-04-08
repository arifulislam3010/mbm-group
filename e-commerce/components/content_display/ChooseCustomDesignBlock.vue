<template>
    <div class="choose_custom_design_block">
        <div class="black_overlay" @click="custom_product_design_on(false)"></div>
        <div class="content_layer_block">
            <div class="head_block"><i class="fa fa-info-circle"></i> Customize product design</div>
            <div class="main_block">
                <div class="row">
                    <div class="col-4">
                        <div class="upload_section" @click="upload_design">                    
                            <div v-if="!sel_design_info.upload_file">
                                <div><i class="fa fa-cloud-upload-alt fa-2x"></i></div>
                                <p>Choose from your local storage</p>
                            </div>
                            <div v-else>
                                <img :src="sel_design_info.upload_file" />
                            </div>
                            <input class="upload_file_input" type="file" ref="input_file" @change="load_image" accept="image/*" capture />
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="custom_design_list">
                            <div class="loader" v-if="pre_loader">
                                <i class="fa fa-cog fa-spin"></i> Loading...wait
                            </div>
                            <template v-else-if="custom_designs.length>0">
                                <!-- <pre>{{ custom_designs }}</pre> -->
                                <div v-for="(design_info,key) in custom_designs" :key="key">
                                    <div class="basic_info">                                    
                                        <h4><PriceViewBlock :data="design_info.price" /></h4>
                                        <h6>{{ design_info.design_title }}, {{ design_info.cat_info.category_name }}</h6>                                        
                                    </div>
                                    <div class="thumb_list_block">
                                        <carousel
                                            :paginationActiveColor="'#007499'"
                                            :paginationColor="'#CCC'"
                                            :paginationPadding="3"
                                            :perPage="4"
                                            :minSwipeDistance="10"
                                            :loop="true">
                                            <template v-for="(item,index) in design_info.photo_infos">
                                                <slide v-if="item.content" :key="index">
                                                    <div class="thumb_item" @click="choose_custom_design(item,design_info.price)">
                                                        <i v-if="sel_design_info.design_id==item.id" class="fa fa-check-circle"></i>
                                                        <nuxt-img
                                                        :src="item.content"
                                                        :title="design_info.design_title + '-thumb-' + index"
                                                        :alt="design_info.design_title + '-thumb-' + index"
                                                        sizes="sm:40px md:80px lg:120px"
                                                        />
                                                    </div>
                                                </slide>
                                            </template>
                                        </carousel>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <EmptyContentBlock />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="foot_block">
                <div class="submit_msg">{{ msg }}</div>
                <div v-if="sel_design_info.upload_file || sel_design_info.design_id" class="select_btn" @click="select_design">
                    <i class="fa fa-check"></i>
                    <span>Select</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import PriceViewBlock from '@/components/content_display/price/ViewBlock'
import EmptyContentBlock from '@/components/content_display/EmptyContentBlock'
import { mapMutations } from 'vuex'
export default {
    name: 'ChooseCustomDesignBlock',    
    components: {
        PriceViewBlock,
        EmptyContentBlock
    },
    data(){
        return {
            pre_loader: false,
            custom_designs: [],
            sel_design_info: {
                upload_file: '',
                design_id: '',
                price: '',
                design_url: ''
            },
            msg: '',
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
        this.load_custom_design()
    },
    methods: {
        ...mapMutations({
            custom_product_design_on: 'CUSTOM_PRODUCT_DESIGN_ON',
            sel_custom_product_design: 'SEL_CUSTOM_PRODUCT_DESIGN'
        }),
        upload_design: function(){
            this.$refs.input_file.click();
        },
        load_image(e){
            var files = e.target.files || e.dataTransfer.files; 
            if (!files.length) return;
            console.log('File info', files[0]);
            this.CreateImage(files[0]);
        },
        CreateImage(file){
            var elm = this
            var file_size = file.size / 1024
            if(file_size>100){
              this.msg = 'The selected file size 100 KB exceeded.\nPlease upload less or equal 100 KB'
              return false
            }
            var reader = new FileReader();            
            console.log('file size', file_size)
            reader.onload = (e) => {
                elm.sel_design_info = {
                    upload_file: e.target.result,
                    design_id: '',
                    design_url: '',
                    price: ''
                }                
            };
            reader.readAsDataURL(file);
        },
        choose_custom_design(item,item_price){
            this.sel_design_info = {
                upload_file: '',
                design_id: item.id,
                design_url: item.content,
                price: item_price
            }
        },
        select_design(){
            this.sel_custom_product_design(this.sel_design_info)
            this.custom_product_design_on(false)
        },
        load_custom_design: async function(){
            let url = '/api/custom-product-designs/load' + (this.$store.state.sel_custom_product_cat_id?'?cat_id = ' + this.$store.state.sel_custom_product_cat_id:'');

            this.custom_designs = [];
            this.pre_loader = true
            this.$axios.get(url, this.header_config).then( (response) => {
                console.log(response)
                this.custom_designs = response.data.data
                this.pre_loader = false
            }).catch(e => {
                this.$toast.error('Custom design load failed!!!', {icon: "error_outline"})
            });
        }
    }
}
</script>
<style lang="scss" scoped>
    $min_box_height: 450;
    .content_layer_block{
        position: fixed;
        width: 80%; left: 10%;
        top: 5%;
        // padding: 25px; 
        min-height: #{$min_box_height}px;
        max-height: 80%;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px #444;        
        z-index: 10000;

        .head_block{
            display: flex;
            gap: 5px;
            border-bottom: 1px solid #ddd;
            padding: 0 15px;
            font-size: 14px;
            height: 48px;
            align-items: center;
            font-weight: 600;
            color: #666;
        }

        .main_block{
            display: block;
            padding: 15px;
            .upload_section{
                display: flex;            
                min-height: #{$min_box_height - 150}px;
                align-items: center;
                justify-content: center;
                background: #f7f7f7;
                border: 1px solid #eee;
                border-radius: 5px;

                & > div {
                    & > div{
                        text-align: center;
                        font-size: 46px;
                        color: #ddd;
                    }

                    & > p{
                        font-size: 12px; color: #666
                    }
                }
            }

            .upload_file_input{
                display: none;
            }

            .custom_design_list{
                display: block;
                height: calc(100% - 150px);
                overflow-y: auto;

                & > div{
                    display: block;
                    border: 1px solid #ddd;
                    border-radius: 5px;

                    &.loader{
                        padding: 15px
                    }

                    .basic_info{
                        padding: 15px;
                        border-bottom: 1px solid #ddd;
                        h4{
                            font-size: 14px; font-weight: 600;
                        }
                        h6{
                            font-size: 12px;
                            margin: 0
                        }
                    }
                }

                .thumb_list_block{
                    display: block;
                    padding: 15px;
                    :deep(.VueCarousel-inner){
                        gap: 15px
                    }
                }
                .thumb_item{
                    display: block;
                    position: relative;
                    width: 140px;
                    height: 140px;

                    & > i{
                        position: absolute;
                        height: 100%;
                        width: 100%;
                        font-size: 32px;
                        color: #ceffce;
                        z-index: 5;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #00000060;
                        box-shadow: 0 0 5px #000;
                    }
                }
                .thumb_item.active{
                    border: 2px solid #444;
                    border-radius: 3px;
                }
                .thumb_item > img{
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    object-position: -10px
                }
                .photo_empty_block{
                    width: 100%;
                    height: 100%;
                    padding-top: 25%;
                    background: #eee;
                    text-align: center;
                    font-size: 112px;
                    color: #ccc;
                }
            }
        }
        .foot_block{
            display: flex;
            align-items: center;
            height: 48px;
            border-top: 1px solid #ddd;
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 0 15px;
            
            .select_btn{
                display: flex;
                align-items: center;
                margin-left: auto;
                background-color: #293168;
                color: #fff;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.4s;
                opacity: 0.8;
                height: 30px;
                padding: 0 15px;
                gap: 10px;
                &:hover{
                    opacity: 1.0
                }
            }
        }
    }
</style>