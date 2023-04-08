<template>
    <div class="static_page_block">
        <div class="container pt-3">
            <div v-if="pre_loader">
                <FormBlockLoader :cols="1" :height="20" :r1="true" :r2="false" :r1_w="50" :r2_w="0" :r1_h="15" :r2_h="0" />
                <div v-for="(n,i) in 5" :key="i">
                    <FormBlockLoader :cols="1" :height="10" :r1="true" :r2="false" :r1_w="100" :r2_w="0" :r1_h="5" :r2_h="0" />
                </div>
            </div>
            <template v-else-if="data">
                <h1>{{ data.page_title }}</h1>
                <div class="details_block mt-2" v-html="data.details"></div>
            </template>
        </div>
    </div>
</template>
<script>
import { mapMutations } from 'vuex'
export default {
    name: 'StaticPageBlock',
    data(){
        return {
            pre_loader: false,
            data: ''
        }
    },
    watch: {
        $route(to,fromt){
            this.load_data()
        }
    },
    computed: {
        cur_page: function(){
            return this.$route.params.page
        }
    },
    mounted(){
        this.load_data();
    },
    methods: {
        ...mapMutations({
            google_map_allow_status: 'GOOGLE_MAP_STATUS'
        }),
        async load_data(){
            this.pre_loader = true
            this.$axios.get('/api/static-page-infos/page/' + this.cur_page, this.$parent.$parent.header_config).then( (response) => {
                console.log('Get Data', response.data.data)
                this.data = response.data.data
                if(this.$store.state.site_basic_config_data.google_map_page) this.google_map_allow_status(this.data.google_map_display)
                this.pre_loader = false
            }).catch(e => {
                console.log(e)
                this.pre_loader = false;
            });
        }
    }
}
</script>
<style scoped>
    h1{
        font-size: 24px;
        padding-bottom: 5px;
    }
    .details_block{
        display: block;
        text-align: justify;
        font-size: 13px;
    }
</style>