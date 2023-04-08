<template>
    <div class="fb_login_block" title="Login with Facebook">
        <i class="fab fa-facebook fa-2x" @click="req_login"></i>
    </div>
</template>

<script>
export default {
    name: 'FBLoginBlock',
    mounted() {
        // let elm = this
        // window.fbAsyncInit = function() {
        //     FB.init({
        //         appId      : process.env.FB_APP_ID,
        //         xfbml      : true,
        //         version    : 'v12.0'
        //     });
        //     FB.AppEvents.logPageView();

        //     FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
        //         // elm.statusChangeCallback(response);        // Returns the login status.
        //     });
        // };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    },
    methods: {
        statusChangeCallback(response) {
            console.log('statusChangeCallback');
            console.log(response);
            if (response.status === 'connected') {
                this.getUserInfo();
            }
        },
        getUserInfo() {
            let elm = this
            console.log('Welcome!  Fetching your information.... ');
            FB.api('/me?fields=about,name,picture{url},email', function(response) {
                console.log('Successful login for: ', response);
                // document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';                
                elm.$parent.social_login = {
                    data: response,
                    type: 'Facebook'
                }
                elm.$parent.social_login = {
                    social_id_info: {
                        id: response.id,
                        type: 'Facebook'
                    },
                    login_id: response.email?response.email:'',
                    full_name: response.name,
                    photo: response.picture && response.picture.data?response.picture.data.url:''
                }
            });
        },
        req_login: async function(){
            let elm = this
            FB.login(function(response) {
                // handle the response
                console.log(response)
                elm.statusChangeCallback(response);
            }, {scope: 'public_profile,email'});
            // const { authResponse } = await new Promise(FB.login);
            // console.log(authResponse)
        }
    }
}
</script>
<style scoped>
    .fb_login_block{
        cursor: pointer
    }
</style>