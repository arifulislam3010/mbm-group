<template>
  <div class="main-block">    
    <template v-if="loggedIn || loginStatus">
      <template v-if="$store.state.auth_info.loggedIn && $store.state.auth_info.user_data">
        <AdminBlock />
      </template>
      <template v-else>
        <SpinkitDoubleBounceLoader />
      </template>
    </template>
    <template v-else>
      <LoginForm :base_path='base_path' />
    </template>
  </div>
</template>

<script>
  import AdminBlock from './dashboard/skeleton'
  import LoginForm from './dashboard/login-form'
  export default {
    name: 'MainBlock',
    scrollToTop: true,
    components: {
      AdminBlock,
      LoginForm
    },
    data(){
      return {
        loggedIn: true,
        base_path: this.$store.state.dashboard.prefix, //'dashboard',
        advance_srch_modal: false
      }
    },
    async asyncData({ $cookiz, store, route, params, redirect }){
      console.log('Fetch SSR')
      let getOauthToken = $cookiz.get(store.state.outh_token_name)
      await store.dispatch('oauth_token/GET_OAUTH_TOKEN', getOauthToken)        
            
      // console.log(store.state.oauth_token.cached)
      if(!store.state.oauth_token.cached){
        console.log('Fetch OAUTh TOKEN')
        try{
          await store.dispatch('oauth_token/GET_TOKEN_INFO')
          
          let oauth_token = store.state.oauth_token.data
          $cookiz.set(store.state.outh_token_name, JSON.stringify(oauth_token))
          store.commit('oauth_token/CACHE_STATUS', true)
        } catch (e) {
          return error({ message: e.message, statuscode: e.response.status })
        }
      }

      console.log('Fetch BASIC INFO')
      await store.dispatch('GET_BASIC_CONFIG_INFO', store.state.oauth_token.data)      

      // console.log('Basic Config Data', store.state.site_basic_config_data)
      if(Object.keys(store.state.site_basic_config_data).length==0) {          
        $cookiz.remove(store.state.outh_token_name)
        redirect(`${process.env.BASE_URL}`)          
      }
    },
    computed: {
        loginStatus: function(){
            if(this.$store.state.auth_info.loggedIn) return true
            else return false
        }
    },
    mounted(){
        if(localStorage.getItem('user_info') == null) this.loggedIn = false
        else this.loggedIn = true

        if(this.loggedIn && this.loginStatus && this.$route.name=='index') this.$router.push('/' + this.$store.state.dashboard.prefix)
    }
  }
</script>

<style scoped>
</style>