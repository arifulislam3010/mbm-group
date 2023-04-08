<template>
  <div class="default-theme">
    <!-- {{ $store.state.site_basic_config_data }} -->
    <component :is="defaultTheme"></component>
  </div>
</template>

<script>
  export default {
    name: 'DefaultTheme',
    scrollToTop: true,
    async asyncData({ $cookiz, store, route, params, redirect }){
      console.log('Fetch SSR')
      let getOauthToken = $cookiz.get(store.state.outh_token_name)
      if(getOauthToken && getOauthToken.token_type) await store.dispatch('oauth_token/GET_OAUTH_TOKEN', getOauthToken)

      console.log(store.state.oauth_token.cached)
      if(!store.state.oauth_token.cached){
        console.log('Fetch OAUTh TOKEN')
        try{
          await store.dispatch('oauth_token/GET_TOKEN_INFO')
          // console.log('token data', store.state.oauth_token.data)
          let oauth_token = store.state.oauth_token.data
          $cookiz.set(store.state.outh_token_name, JSON.stringify(oauth_token))
          store.commit('oauth_token/CACHE_STATUS', true)
        } catch (e) {
          return error({ message: e.message, statuscode: e.response.status })
        }
      }

      console.log('Fetch BASIC INFO')
      try{
        await store.dispatch('GET_BASIC_CONFIG_INFO', store.state.oauth_token.data)

        // console.log('Basic Config Data', store.state.site_basic_config_data)
        // if(Object.keys(store.state.site_basic_config_data).length==0) {
        //   $cookiz.remove(store.state.outh_token_name)
        //   // redirect(`${process.env.BASE_URL}`)
        // }
      } catch (err) {
          // eslint-disable-next-line no-console
          // console.log('Got Error', err.message)
          if(err.message.indexOf('code 401')>=0){
              $cookiz.remove(store.state.outh_token_name)
              redirect(`${process.env.BASE_URL}`)
          }

          console.log('Error', err)
          return false
      }

      // console.log(params)
      if(route.name=='index-'+ store.state.product_dtl_path +'-slug' && params.slug){
          await Promise.all([
              // console.log('Fetch LOGO INFO')
              store.dispatch('GET_LOGO_INFO', store.state.oauth_token.data),

              // console.log('Fetch Categories Info')
              store.dispatch('category_info/GET_DATA', {access_token: store.state.oauth_token.token,type: 'display_on_nav'}),

              // console.log('Fetch STATIC PAGE INFO')
              store.dispatch('static_page_info/GET_DATA', {access_token: store.state.oauth_token.token,limit: 20}),

              // console.log('Fetch DETAILS PAGE')
              await store.dispatch('product/GET_PRODUCT_INFO', {access_token: store.state.oauth_token.token, slug: params.slug})
          ]);
      }else{
          await Promise.all([
              // console.log('Fetch LOGO INFO')
              store.dispatch('GET_LOGO_INFO', store.state.oauth_token.data),

              // console.log('Fetch Categories Info')
              store.dispatch('category_info/GET_DATA', {access_token: store.state.oauth_token.token,type: 'display_on_nav'}),

              // console.log('Fetch STATIC PAGE INFO')
              store.dispatch('static_page_info/GET_DATA', {access_token: store.state.oauth_token.token,limit: 20}),

              // console.log('Fetch PROMOTIONAL BANNER INFO')
              store.dispatch('promotional_banner_info/GET_DATA', {access_token: store.state.oauth_token.token,limit: 8}),

              // console.log('Fetch Categories Display on Body Info')
              store.dispatch('category_info/GET_DATA', {access_token: store.state.oauth_token.token,type: 'display_on_body'})
          ]);
          // console.log('Display on body', store.state.category_info.get_body_data)

          // console.log('Fetch PRODUCT TYPE INFO')
          // await store.dispatch('product_type/GET_DATA', {access_token: store.state.oauth_token.token})

          // console.log('Fetch HOT PRODUCT INFO')
          // await store.dispatch('product/GET_HOT_PRODUCTS', {access_token: store.state.oauth_token.token,limit: 15})

          // console.log('Fetch RECOMENDED PRODUCT INFO')
          // await store.dispatch('product/GET_RECOMENDED_PRODUCTS', {access_token: store.state.oauth_token.token,limit: 15})

          // console.log('Fetch ALL PRODUCT INFO')
          // await store.dispatch('product/GET_ALL_PRODUCTS', {access_token: store.state.oauth_token.token,limit: 18})
      }

    },
    // middleware({ store, redirect }) {
    //   if(Object.keys(store.state.site_basic_config_data).length==0) {
    //     console.log('Middle')
    //     return redirect('/')
    //   }
    // },
    computed: {
      defaultTheme: function() {
        return () => import(`@/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}`);
      }
    }
  }
</script>
