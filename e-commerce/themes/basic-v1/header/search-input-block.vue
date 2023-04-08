<template>
    <div class="seach_input_block">        
        <i v-if="listening" class="fa fa-assistive-listening-systems"></i>
        <i v-else class="fa fa-search"></i>
        <input type="input" v-model="srch_keyword" :placeholder="listening?listening_placeholder:srch_placeholder" @blur="handle_blur" @focus="focus_item_list" @keyup="search_list" @keyup.enter="search_items" />        
        <SpeechRecognition :config="custom_config" />
        
        <template v-if="(autocompleteItems.length>0 || search_history.length>0) && autocompleteItemsOpen">
            <div class="auto_complete_items_block" @mouseover="autocompleteHoverActive=true" @mouseout="autocompleteHoverActive=false">
                <template v-if="autocompleteItems.length>0">
                    <template v-for="(item,index) in autocompleteItems">
                        <nuxt-link :key="index" :to="{path: item.url }" v-html="item.display_title"></nuxt-link>
                    </template>
                </template>

                <template v-else-if="search_history.length>0 && srch_keyword==''">
                    <label>Search history</label>
                    <template v-for="(item,index) in search_history">
                        <nuxt-link :key="index" :to="{path: item.url }" v-html="item.title"></nuxt-link>
                    </template>
                </template>

                <template v-else>
                    <EmptyContentBlock />
                </template>
            </div>
        </template>
    </div>
</template>
<script>
import SpeechRecognition from '@/components/SpeechRecognition'
import EmptyContentBlock from '@/components/content_display/EmptyContentBlock'
export default {
    name: 'SearchInputBlock',
    components: {
        SpeechRecognition,
        EmptyContentBlock
    },
    data() {
        return {
            listening: false,
            listening_placeholder: 'Listening, speak for search...',
            srch_placeholder: 'product name, keyword etc',
            srch_keyword: (this.$route.query.keyword!=''?this.$route.query.keyword:''),
            custom_config: {
                lang: 'en-US',
                // lang: 'bn-BD',
                confidence_level: '', // '0.6'
                action: this.search_items
            },
            autocompleteItems: [],
            autocompleteItemsOpen: false,
            autocompleteHoverActive: false,
            srch_input_timer: null,
            search_history: [],
            search_history_limit: 10,
            access_token: this.$store.state.oauth_token.token
        }
    },
    watch: {
        $route(to, from){
            if(to.query.keyword){
                this.srch_keyword = to.query.keyword
                this.get_search_history()
                this.store_search_history()
                this.get_search_data()
            }else this.srch_keyword = ''

            this.autocompleteItemsOpen = false
        },
        listening: function(status){
            if(status) this.srch_keyword = ''
        }
    },
    computed: {
        header_config (){
            let obj = {
                headers: {
                    'Authorization': 'Bearer ' + this.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': this.access_token
                }
            };
            return obj;
        },
    },
    mounted() {
        this.get_search_history()
        this.autocompleteItemsOpen = false
    },
    methods: {
        handle_blur: function(){
            if(!this.autocompleteHoverActive) this.autocompleteItemsOpen = false
        },
        get_search_history: function(){
            if(localStorage.getItem('_search_history')) this.search_history = JSON.parse(localStorage.getItem('_search_history'))
        },
        focus_item_list: function(){
            this.get_search_history()
            this.autocompleteItemsOpen = true            
        },
        get_search_data: async function(){
            if(this.srch_keyword!=''){
                const url = `/api/products/search/list?term=${this.srch_keyword}&limit=10`
                let getReponseData = await this.$http.$get(url, this.header_config);
                // console.log('Response data', getReponseData);
                this.autocompleteItems = getReponseData.map(a => {
                    return {
                        title: a.product_title,
                        display_title: a.product_title.replace(new RegExp(this.srch_keyword, "i"), match => { return '<b>'+ match +'</b>' }) + (a.generic_name?'<div><small>' + a.generic_name.replace(new RegExp(this.srch_keyword, "i"), match => { return '<b>'+ match +'</b>' }) + '</small></div>':''),
                        url: '/search?keyword=' + a.product_title,                        
                    };
                });
            }else{
                this.autocompleteItems = []
            }
        },
        search_list: function(){
            if (this.srch_input_timer) {
                clearTimeout(this.srch_input_timer);
                this.srch_input_timer = null;
            }
            this.srch_input_timer = setTimeout(async () => {
                this.get_search_data();

                clearTimeout(this.srch_input_timer);
                this.srch_input_timer = null;
            }, 200);            
        },
        remove_same_history: function(){
            if(this.search_history.length>0){
                this.search_history.forEach((v,i) => {
                    if(this.srch_keyword==v.title) this.search_history.splice(i,1)
                });
            }
        },
        store_search_history: function(){
            this.autocompleteItems.forEach( async (v) => {                
                if(this.srch_keyword==v.title){
                    let obj = {
                        title: v.title,
                        url: v.url
                    }
                    // console.log(obj)
                    await this.remove_same_history()
                    this.search_history.unshift(obj)
                    if(this.search_history.length > this.search_history_limit) this.search_history.pop()
                    localStorage.setItem('_search_history', JSON.stringify(this.search_history))
                }
            });
        },
        search_items: function(){
            if(this.srch_keyword){                
                this.$router.push('/'+ this.$store.state.search_page_path +'?keyword=' + this.srch_keyword)
            }else this.$router.push('/')
        }
    }
}
</script>
<style lang="scss" scoped>
    .seach_input_block{
        position: relative; width: 100%;
        display: flex; padding: 0 15px; margin: 0 25px;
        background-color: $light_white;
        border: 1px solid $light_ash;
        /* margin-top: 7px; */
        height: 40px; border-radius: 25px;
    }
    .seach_input_block > input{
        align-self: center; padding: 0 10px; height: 100%;
        color: $dark_ash; width: 100%; border: none; background-color: transparent;
    }
    .seach_input_block > i{
        align-self: center;
        display:inline-block; font-size: 16px; color: $ash;
    }
    .seach_input_block .speech_recongition_block{
        display: inline-block; align-self: center; font-size: 16px; color: $ash;   
    }
    .seach_input_block .speech_recongition_block .action_btn{
        width: 30px; height: 30px; line-height: 30px;
        text-align: center; font-size: 20px;
        /* background-color: $white;
        border: 1px solid $default_border_color; */
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.4s;
    }
    .seach_input_block .speech_recongition_block .action_btn.active,
    .seach_input_block .speech_recongition_block .action_btn:hover{
        font-size: 14px;
        background-color: $darkred;
        color: $light_white;
        border-color: $darkred;
    }
    .auto_complete_items_block{
        position: absolute;
        width: calc(100% - 65px);
        top: 38px;
        left: 0;
        margin: 0 30px;
        height: auto;
        background-color: $white;
        border: 1px solid $default_border_color;
    }
    .auto_complete_items_block > label{
        display: block;
        font-size: 10px;
        padding: 7px 10px 0 10px;
        color: $light_text_color;
    }
    .auto_complete_items_block > a{
        display: block;
        padding: 7px 10px;
        color: $default_text_color;
        font-size: 12px;
        border-bottom: 1px solid $default_border_color
    }
    .auto_complete_items_block > a:hover{
        color: $skyblue
    }
    .auto_complete_items_block > a:last-child{
        border-bottom: none
    }
</style>