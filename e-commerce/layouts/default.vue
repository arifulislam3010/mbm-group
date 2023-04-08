<template>
  <div>    
    <div v-if="pre_loader || page_loader">
      <!-- <div class="init_loader">
        <SpinkitDoubleBounceLoader />
        <SpinkitLoader />
      </div> -->
      <div class="init_loader">
          <LogoPercentageBarLoaderV2 :slogan="`Welcome to ${app_name}`" />
      </div>
    </div>
    <div v-if="!pre_loader">
      <nuxt keep-alive />
    </div>
  </div>
</template>
<script>
import { mapActions, mapMutations } from 'vuex'
import { createSEOMeta } from "@/utils/seo";
export default {
  name: 'DefaultLayout',
  head(){    
    return {
      title: this.siteTitle,
      meta: createSEOMeta(this.metaContent),
      link: [
        { rel: 'icon', type: 'image/png', href: this.$store.state.logo_info_data.favicon? this.$store.state.logo_info_data.favicon:'/favicon.ico' },
        { rel: "canonical", href: process.env.BASE_URL + this.$route.fullPath}
      ],
    }
  },
  data(){
    return {
      app_name: process.env.APP_NAME,
      pre_loader: true,
      page_loader: true 
    }
  },
  watch: {
    siteTitle: function(val){}
  },
  computed: {
    siteTitle: function(){
      if(this.$route.name=='index-'+ this.$store.state.product_dtl_path +'-slug') return this.$store.state.product.product_dtl_info.product_title
      else if(this.$store.state.site_basic_config_data.website_title) return this.$store.state.site_basic_config_data.website_title
      else return process.env.APP_NAME + ' | Home Page'
    },
    metaContent: function(){
      let obj = {}
      if(this.$route.name=='index-'+ this.$store.state.product_dtl_path +'-slug'){
        const { product_title, product_photo_infos, tag_info } = this.$store.state.product.product_dtl_info
        let tags = ''
        // console.log(product_title)
        for(var i=0; tag_info && tag_info[i]; i++) {
          if(tag_info[i].tags_data_info){
            if(i>0) tags = tags + ','
            tags = tags + tag_info[i].tags_data_info.tag_title
          }
        }        
        obj = {
            title: product_title,
            description: product_title,
            keywords: tags,
            image: product_photo_infos && product_photo_infos[0] && product_photo_infos[0].product_photo_data?product_photo_infos[0].product_photo_data.content:'',
            url: process.env.BASE_URL + this.$route.fullPath 
        }
      }else{
        obj = {
            title: this.siteTitle,
            description: this.$store.state.site_basic_config_data?this.$store.state.site_basic_config_data.website_desc:'',
            keywords: this.$store.state.site_basic_config_data?this.$store.state.site_basic_config_data.meta_keywords:'',
            image: process.env.BASE_URL + '/_ipx/logo.png',
            url: process.env.BASE_URL + this.$route.fullPath 
        }
      }

      return obj
    }
  },
  mounted(){
    this.initial_load();

    // window.CRISP_READY_TRIGGER = function() {      
    //   $crisp.push(["config", "color:theme", ["deep_orange"]])
    //   $crisp.push(["config", "hide:on:mobile", [true]])
    // }
  },  
  methods: {
    ...mapActions({
      // curOauthToken: 'oauth_token/GET_OAUTH_TOKEN',
      // getOauthToken: 'oauth_token/GET_TOKEN_INFO',
      getUserInfo: 'auth_info/GET_USER_INFO',
      // getBasicConfig: 'GET_BASIC_CONFIG_INFO'
    }),
    ...mapMutations({
      getCartItems: 'cart_info/GET_CART_ITEMS'
    }),    
    async initial_load(){
      // Oauth token check
      // if($nuxt.isOnline && localStorage.getItem('oauth_token') == null){
      //     // await this.getOauthToken()
      //     // console.log('C T')
      //     if(this.$store.state.oauth_token.data[0]!==undefined){
      //         let oauth_token = this.$store.state.oauth_token.data[0]
      //         document.cookie = 'oauth_token='+ JSON.stringify(oauth_token) +';';
      //         // localStorage.setItem('oauth_token', JSON.stringify(oauth_token))
      //     }
      // }else{
      //     let oauth_token = JSON.parse(localStorage.getItem('oauth_token'));

      //     // Overrides `Authorization` header with new value
      //     // this.$axios.setHeader('Authorization', oauth_token.token_type + ' ' + oauth_token.access_token)

      //     // Adds header: `Content-Type: application/x-www-form-urlencoded` to only post requests
      //     // this.$axios.setHeader('Content-Type', 'application/json', ['post'])          
      // }
      console.log('Cached', this.$store.state.oauth_token.cached)
      let getOauthToken = this.$cookiz.get(this.$store.state.outh_token_name)
      
      if(!this.$store.state.oauth_token.cached){
        await this.$store.dispatch('oauth_token/GET_OAUTH_TOKEN', getOauthToken)        
        // document.cookie = 'oauth_token='+ JSON.stringify(oauth_token) +';';
        // localStorage.setItem('oauth_token', JSON.stringify(oauth_token))
      }

      // Check current cart items
      if($nuxt.isOnline){
        this.getCartItems();

        // call for get job seeker info
        if(this.$store.state.auth_info.user_data.length==0) await this.getUserInfo()
        // if(this.$store.state.oauth_token.token=='') await this.curOauthToken()

        // Get basic config
        // await this.getBasicConfig();
      }
            
      this.pre_loader = false;
    }
  }
}
</script>
<style lang="scss" scoped>
  .init_loader{
    @extend .init-loader-basic;
    background-color: $init_loader_bg_color_opc;
    color: #fff; text-shadow: 0 0 5px #000;
    z-index: 1000
  }  
</style>
