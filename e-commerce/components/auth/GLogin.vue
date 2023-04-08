<template>
    <div class="google_login_block" title="Login with Google">
        <i class="fab fa-google fa-2x" @click="req_login"></i>
    </div>
</template>

<script>
export default {
    name: 'GoogleLoginBlock',
    mounted() {        
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://apis.google.com/js/api.js";
            js.onload = "this.onload=function(){};this.HandleGoogleApiLibrary()";
            js.defer = true
            js.onreadystatechange = "if (this.readyState === 'complete') this.onload()";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'google-jssdk'));
    },
    methods: {
        HandleGoogleApiLibrary: function(login_req=false) {
            let elm = this
            let google_api_key = this.$store.state.site_basic_config_data.third_party_auth_info?this.$store.state.site_basic_config_data.third_party_auth_info.google_api_key:''
            let google_client_id = this.$store.state.site_basic_config_data.third_party_auth_info?this.$store.state.site_basic_config_data.third_party_auth_info.google_client_id:''
            gapi.load('client:auth2',  {
                callback: function() {
                    // Initialize client & auth libraries
                    gapi.client.init({
                        apiKey: google_api_key,
                        clientId: google_client_id,
                        cookiepolicy: 'single_host_origin',
                        plugin_name: 'login',
                        scope: 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email'
                    }).then(
                        function(success) {
                            // Libraries are initialized successfully
                            // You can now make API calls
                            console.log(success,login_req)
                            if(login_req) elm.req_login()
                        }, 
                        function(error) {
                            // Error occurred
                            console.log(error) 
                            // to find the reason
                        }
                    );
                },
                onerror: function() {
                    // Failed to load libraries
                    console.log('Failed')
                }
            });
        },
        req_login: function() {
            let elm = this
            console.log('Req login', gapi.auth2)
            if(gapi.auth2 == undefined) this.HandleGoogleApiLibrary(true)
            else{
                gapi.auth2.getAuthInstance().signIn().then(
                    function(success) {
                        // Login API call is successful                    
                        console.log('Login success',success)
                        elm.getUserInfo()
                    },
                    function(error) {
                        // Error occurred
                        console.log('Error',error)
                        //to find the reason
                    }
                );
            }
        },
        getUserInfo: function(){
            let elm = this
            gapi.client.load('oauth2', 'v2', function () {
                var request = gapi.client.oauth2.userinfo.get({
                    'userId': 'me'
                });
                request.execute(function (resp) {
                    // Display the user details
                    console.log(resp)
                    if(resp.email){
                        if(resp.verified_email){
                            elm.$parent.social_login = {
                                social_id_info: {
                                    id: resp.id,
                                    type: 'Google'
                                },
                                login_id: resp.email,
                                full_name: resp.name,
                                photo: resp.picture     
                            }
                        }else{
                            elm.$toast.error('Please valid login id', {icon: "Warning"});
                            elm.$parent.$refs.login_id.focus();
                            return false;
                        }
                    }else{
                        elm.$toast.error('Please valid google login', {icon: "Warning"});
                        elm.$refs.login_id.focus();
                        return false;
                    }                    
                });
            });
        },
        signOut: function() {
            var auth2 = gapi.auth2.getAuthInstance();
            auth2.signOut().then(function () {
                // document.getElementsByClassName("userContent")[0].innerHTML = '';
                // document.getElementsByClassName("userContent")[0].style.display = "none";
                // document.getElementById("gSignIn").style.display = "block";
            });
            auth2.disconnect();
        }
    }
}
</script>
<style scoped>
    .google_login_block{
        cursor: pointer
    }
</style>