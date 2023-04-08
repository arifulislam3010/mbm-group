// State
export const state = () => ({
    // user status
    user_status: '',
    // user id
    user_id: '',
    // login status
    loggedIn: false,
    // login error message
    login_err_msg: '',
    // signup error message
    signup_err_msg: '',
    // login user info
    user_data: [],
    // singup return info
    signup_data: [],
    // forgot password return info
    forgot_password_data: [],
    // msg return
    msg: ''
  })
  
  export const mutations = {
    // User Status
    USER_STATUS (state, data){
      state.user_status = data
    },
    // Get User ID
    GET_USER_ID (state, data){
      state.user_id = data
    },
    // LOGIN STATUS
    LOGIN_STATUS (state, data){
      state.loggedIn = data
    },
    LOGIN_ERR_MSG (state, data){
      state.login_err_msg = data
    },
    SIGNUP_ERR_MSG (state, data){
      state.signup_err_msg = data
    },
    // Get user data
    ADD_USER_DATA (state, data) {
      state.user_data = data
    },
    UPDATE_DATA (state,data){
      state.user_data.partner_info = data.partner_info;    
      state.user_data.user_info = data.user_info;    
  
      localStorage.setItem('user_info', JSON.stringify(state.user_data))
    },
    // Get user data
    REQ_SIGNUP_DATA (state, data) {
      state.signup_data = data
    },
    // Get forgot password request data
    REQ_FORGOT_PASSWORD_DATA (state, data) {
      state.forgot_password_data = data
    },
    // Reset user data
    RESET_USER_DATA (state) {
      state.loggedIn = false
      state.user_data = []
    },
    // Reset user data
    RESET_SIGNUP_DATA (state) {
      state.signup_data = []
    },
    // Add error message
    ERR_MSG (state, data) {
        state.msg = data
    }
  }
  
  // Actions
  export const actions = {
    async GET_USER_INFO ({ commit }) {
      // eslint-disable-next-line no-console
      // console.log('USER INFO request')      
      if (localStorage.getItem('user_info')) {
        commit('LOGIN_STATUS', true)
        // console.log(JSON.parse(localStorage.getItem('user_info')))
        commit('ADD_USER_DATA', JSON.parse(localStorage.getItem('user_info')))
        return false
      }
    },
  
    // eslint-disable-next-line require-await
    async DATA_SUBMIT ({ commit }, request) {
        // eslint-disable-next-line no-console
        // console.log('Data submit request')
        try {
            const { data } = await this.$axios.post('/api/user-info/' + request.id, request.data, {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })
            // eslint-disable-next-line no-console
            // console.log('After submit get data', data)
            
            if(data.status) {
              let get_user_info = data.data
  
              if (localStorage.getItem('user_info')!==null) {
                  let pre_user_info = JSON.parse(localStorage.getItem('user_info'));
                  let cur_token = pre_user_info.token
  
                  get_user_info.token = cur_token
                  
                  localStorage.setItem('user_info',JSON.stringify(get_user_info));
                  //console.log(get_user_info)
              }
  
              commit('ADD_USER_DATA', get_user_info)
            } else {
              commit('ERR_MSG', data.msg)
            }   
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async PASSWORD_CHANGE ({ commit }, request) {
      // eslint-disable-next-line no-console
      // console.log('Data submit request')
          try {
              const { data } = await this.$axios.post('/api/user/change-password',request.data, {
                  headers: {
                      'Authorization': 'Bearer ' + request.access_token,
                      'Content-Type': 'application/json',
                      'X-XSRF-TOKEN': request.access_token
                  }
              })
              // alert('ss');
              // eslint-disable-next-line no-console
              // console.log('After submit get data', data)
              
              if(data.status) {
                  // commit('UPDATE_DATA', data.data)
              } else {
                  commit('ERR_MSG',data)
              }   
          } catch (err) {
              // eslint-disable-next-line no-console
              console.log('Error', err)
          }
    },
    async PROFILE_UPDATE ({ commit }, request) {
      // eslint-disable-next-line no-console
      // console.log('Data submit request')
          try {
              const { data } = await this.$axios.post('/api/user/profile-update',request.data, {
                  headers: {
                      'Authorization': 'Bearer ' + request.access_token,
                      'Content-Type': 'application/json',
                      'X-XSRF-TOKEN': request.access_token
                  }
              })
              // alert('ss');
              // eslint-disable-next-line no-console
              // console.log('After submit get data', data)
              
              if(data.status) {
                  commit('UPDATE_DATA', data.data)
              } else {
                  commit('ERR_MSG', data.msg)
              }   
          } catch (err) {
              // eslint-disable-next-line no-console
              console.log('Error', err)
          }
    },    

    // eslint-disable-next-line require-await
    async ADMIN_LOGIN_REQ ({ commit }, obj) {
      // eslint-disable-next-line no-console
      // console.log('Admin login request')
      try {
        // if (localStorage.getItem('user_info')) {
        //   commit('LOGIN_STATUS', true)
        //   // console.log(JSON.parse(localStorage.getItem('user_info')))
        //   commit('ADD_USER_DATA', JSON.parse(localStorage.getItem('user_info')))
        //   return false
        // }
  
        // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));

        const { formData, getTokenData } = obj
  
        const { data } = await this.$axios.post('/api/admin/login', formData, {
          headers: {
            'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getTokenData.access_token
          }
        })
        // eslint-disable-next-line no-console
        // console.log('Data', data)
        
        if(data.status==1) {
          commit('LOGIN_STATUS', true)
          commit('ADD_USER_DATA', data.user_info)
          commit('LOGIN_ERR_MSG', '')
        } else if(data.status==2) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Unverified account. Please verify your account.</div>')
        } else if(data.status==3) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Your account has been blocked.</div>')
        } else if(data.status==0) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Invalid credential.</div>')
        }   
      } catch (err) {
        // eslint-disable-next-line no-console
        console.log('Error', err)
        commit('RESET_USER_DATA')
      }
    },

    async SOCIAL_USER_LOGIN_REQ ({ commit }, obj) {
      try {
        const { formData, getTokenData } = obj
        // console.log(formData,getTokenData)
  
        const { data } = await this.$axios.post('/api/auth/social-login', formData, {
          headers: {
            'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getTokenData.access_token
          }
        })
        // eslint-disable-next-line no-console
        // console.log('Data', data)
        
        if(data.status==1) {
          commit('LOGIN_STATUS', true)
          commit('ADD_USER_DATA', data.user_info)
          commit('LOGIN_ERR_MSG', '')
        } else if(data.status==2) {
          commit('USER_STATUS', data.status)
          commit('GET_USER_ID', data.user_info.id)
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Unverified account. Please verify your account.</div>')
        } else if(data.status==3) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Your account has been blocked.</div>')
        } else if(data.status==0) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Invalid credential.</div>')
        }   
      } catch (err) {
        // eslint-disable-next-line no-console
        console.log('Error', err)
        commit('RESET_USER_DATA')
      }
    },
  
    // eslint-disable-next-line require-await
    async USER_LOGIN_REQ ({ commit }, obj) {
      // eslint-disable-next-line no-console
      // console.log('User login request')
      try {
        // if (localStorage.getItem('user_info')) {
        //   commit('LOGIN_STATUS', true)
        //   // console.log(JSON.parse(localStorage.getItem('user_info')))
        //   commit('ADD_USER_DATA', JSON.parse(localStorage.getItem('user_info')))
        //   return false
        // }
  
        // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));

        const { formData, getTokenData } = obj
        // console.log(formData,getTokenData)
  
        const { data } = await this.$axios.post('/api/auth/login', formData, {
          headers: {
            'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getTokenData.access_token
          }
        })
        // eslint-disable-next-line no-console
        // console.log('Data', data)
        
        if(data.status==1) {
          commit('LOGIN_STATUS', true)
          commit('ADD_USER_DATA', data.user_info)
          commit('LOGIN_ERR_MSG', '')
        } else if(data.status==2) {
          commit('USER_STATUS', data.status)
          commit('GET_USER_ID', data.user_info.id)
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Unverified account. Please verify your account.</div>')
        } else if(data.status==3) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Your account has been blocked.</div>')
        } else if(data.status==0) {
          commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> Invalid credential.</div>')
        }   
      } catch (err) {
        // eslint-disable-next-line no-console
        console.log('Error', err)
        commit('RESET_USER_DATA')
      }
    },
  
    async LOGOUT_REQ ({ commit }, getTokenData) {
      // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));
  
      const { data } = await this.$axios.get('/api/logout', {
        headers: {
          'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
          'Content-Type': 'application/json',
          'X-XSRF-TOKEN': getTokenData.access_token
        }
      })
      
      if(data.status) commit('RESET_USER_DATA')
    },
  
    // eslint-disable-next-line require-await
    async REQ_SIGN_UP ({ commit }, obj) {
      // eslint-disable-next-line no-console
      // console.log('Signup request')
      try {
        // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));

        const { formData, getTokenData } = obj

        const { data } = await this.$axios.post('/api/auth/register', formData, {
          headers: {
            'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getTokenData.access_token
          }
        })
        // eslint-disable-next-line no-console
        // console.log('Data', data)        
        if(data.registered) {
          commit('SIGNUP_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> ' + (data.req_id_type=='email'?'E-mail':'Mobile number') + ' already exist.</div>')
        }else if(data.status==1) {          
          commit('REQ_SIGNUP_DATA', data)
          commit('SIGNUP_ERR_MSG', '')
        }
      } catch (err) {
        // eslint-disable-next-line no-console
        console.log('Error', err)
        commit('RESET_SIGNUP_DATA')
      }
    },
  
    // eslint-disable-next-line require-await
    async REQ_FORGOT_PASSWORD ({ commit }, obj) {
      // eslint-disable-next-line no-console
      // console.log('Forgot password request')
      try {
        // let getTokenData = JSON.parse(localStorage.getItem('oauth_token'));

        const { formData, getTokenData } = obj

        const { data } = await this.$axios.post('/api/auth/forgot-password', formData, {
          headers: {
            'Authorization': getTokenData.token_type + ' ' + getTokenData.access_token,
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getTokenData.access_token
          }
        })
        // eslint-disable-next-line no-console
        // console.log('Data', data)
        if(data.status){
          commit('LOGIN_ERR_MSG','')
          commit('REQ_FORGOT_PASSWORD_DATA', data)
        }else commit('LOGIN_ERR_MSG', '<div class="err"><i class="fa fa-check-circle"></i> '+ data.msg +'</div>')
      } catch (err) {
        // eslint-disable-next-line no-console
        console.log('Error', err)        
      }
    }
  }
    