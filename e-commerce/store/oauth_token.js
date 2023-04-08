export const state = () => ({
    // oauth token
    data: {},
    token: '',
    cached: false
  })
  
  export const mutations = {
    // Get Token
    GET_TOKEN (state, data) {
      state.data.access_token = data.access_token
      state.data.token_type = data.token_type
      state.data.token_exp = data.expires_in
      state.token = data.access_token
    },
    // Add Token
    ADD_TOKEN (state, data) {
      state.data = data
      state.token = data.access_token
    },
    // cache status
    CACHE_STATUS (state, status) {
      state.cached = status
    },
    // Resetn Token
    RESET_TOKEN (state) {
      state.data = {}
      state.token = ''
    }
  }
  
  export const actions = {
    async GET_OAUTH_TOKEN ({ commit }, getOauthToken) {
      // let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));
      // console.log('cookie', request.headers)
      // if(request.headers.cookie!==undefined){
        // const getCookies = request.headers.cookie.split(';');
        // // console.log(request.headers.cookie)
        // let srch_str = 'oauth_token=';
        // let getOauthToken = '';
        // for(let i = 0; i < getCookies.length; i++) {         
        //   // console.log(getCookies[i].search(srch_str))
        //   if(getCookies[i].search(srch_str)>=0){
        //     // console.log(getCookies[i])
        //     let arr = getCookies[i].split(srch_str)
        //     getOauthToken = JSON.parse(arr[1])
        //   }
        // }

        // console.log(getOauthToken)

        // eslint-disable-next-line no-console            
        // commit('GET_TOKEN', getTokenType.access_token)
        if(getOauthToken){
          commit('CACHE_STATUS', true)
          commit('GET_TOKEN', getOauthToken)          
        }else{
          commit('CACHE_STATUS', false)
          commit('RESET_TOKEN')          
        }
      // } 
    },

    // eslint-disable-next-line require-await
    async GET_TOKEN_INFO ({ commit }) {
      // console.log('Request for oauth token')
      try {
        const { data } = await this.$axios.post('/oauth/token', {
          'grant_type': 'client_credentials',
          'client_id': process.env.API_CLIENT_ID,
          'client_secret': process.env.API_CLIENT_SECRET
        }, {
          headers: {
            'Content-Type': 'application/json'
          }
        })
        // eslint-disable-next-line no-console
        console.log('Data', data)
        commit('ADD_TOKEN', data)
      } catch (err) {
        // eslint-disable-next-line no-console
        // console.log('Error', err)
        commit('RESET_TOKEN')
      }
    }
  }
    