<template>
    <div class="fb_chat_block">
        <div id="fb-root"></div>
        <div id="fb-customer-chat" class="fb-customerchat"></div>
    </div>
</template>
<script>
export default {
    name: 'FBChatBlock',
    mounted() {
        let fb_app_id = this.$store.state.site_basic_config_data.third_party_auth_info?this.$store.state.site_basic_config_data.third_party_auth_info.fb_app_id:''
        console.log('Fb init', fb_app_id)
        let fb_page_id = this.$store.state.site_basic_config_data.third_party_auth_info?this.$store.state.site_basic_config_data.third_party_auth_info.fb_page_id:''
        console.log('Fb init', fb_page_id)

        let chatbox = document.getElementById('fb-customer-chat');
        chatbox.setAttribute("page_id", fb_page_id);
        chatbox.setAttribute("attribution", "biz_inbox");

        window.fbAsyncInit = function() {
            FB.init({
                appId     : fb_app_id,
                xfbml     : true,
                version   : 'v12.0'
            });
        };

        // (function(d, s, id) {
        //     var js, fjs = d.getElementsByTagName(s)[0];
        //     if (d.getElementById(id)) return;
        //     js = d.createElement(s); js.id = id;
        //     js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        //     fjs.parentNode.insertBefore(js, fjs);
        // }(document, 'script', 'facebook-jssdk'));
    }
}
</script>