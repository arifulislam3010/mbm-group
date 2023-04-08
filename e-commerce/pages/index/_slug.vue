<template>
    <div class="dynamic_page_block">
        <BreadcrumbBlock :data="breadcrumb_data" />
        <hr class="m-0" />
        <nuxt-child />
    </div>
</template>
<script>
import BreadcrumbBlock from '~/components/Breadcrumb'
export default {
    name: 'DynamicPageContainer',
    components: {
        BreadcrumbBlock
    },
    data(){
        return {
            access_token: this.$store.state.oauth_token.token,
            breadcrumb_data: {
                pre_loader: true,
                cat_info: '',
                page_info: ''
            }
        }
    },
    watch: {
        $route(to,from){            
            this.current_breadcrumb_data()
        },
        check_user_dashboard: function(){}
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
        current_slug: function() {
            return this.$route.params.slug
        },
        check_user_dashboard: function(){
            // let get_full_path = this.$route.fullPath

            if(this.current_slug==this.$store.state.user_dashboard_path) return true
            // else if(get_full_path.search('/'+ this.$store.state.user_dashboard_path +'/')) return true
            else return false            
        }
    },
    mounted(){
        // alert(this.current_slug + '===' + this.$store.state.checkout_page_path)
        this.current_breadcrumb_data()
    },
    methods: {
        current_breadcrumb_data(){            
            if(this.check_user_dashboard){
                this.breadcrumb_data.pre_loader = false
                this.breadcrumb_data.page_info = 'User dashboard'
            }else if(this.current_slug==this.$store.state.checkout_page_path){            
                this.breadcrumb_data.pre_loader = false
                this.breadcrumb_data.page_info = 'Checkout'
            }else if(this.current_slug==this.$store.state.static_page_path){            
                this.breadcrumb_data.pre_loader = false
                this.breadcrumb_data.page_info = 'Static page'
            }else{                
                this.breadcrumb_data.pre_loader = false
                this.breadcrumb_data.page_info = ''
            }
        }
    }
}
</script>
<style scoped>
    .page_block{
        display: block;
    }
</style>