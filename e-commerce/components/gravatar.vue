<template>
    <div class="gravatar_image_block">        
        <img :src="getImage()" :alt="alt" />
    </div>
</template>
<script>
import CryptoJS from "crypto-js";
export default {
    name: 'GravatarImageBlock',
    props: ['email','size','alt'],
    components: {
        CryptoJS
    },
    data(){
        return {
            base_url: 'https://www.gravatar.com',            
            config: {
                image_size: this.size?this.size:80,
                default_image: 'retro', // ['mm','identicon','monsterid','wavatar','retro','blank']                
                image_ration: 'g' // ['g','pg','r','x']
            }
        }
    },
    methods: {
        getHash: function(str){
            return CryptoJS.MD5(str);
        },
        getImage: function(){
            let hash = this.getHash(this.email)
            return this.base_url + '/avatar/'+ hash +'?s='+ this.config.image_size +'&d='+ this.config.default_image +'&r='+ this.config.image_ration
        }
    }
}
</script>
<style scoped>

</style>