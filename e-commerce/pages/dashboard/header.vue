<template>
    <div class="header_container">
        <template v-if="$store.state.add_new_status">
            <div class="back_block" @click="form_close">
                <div class="icon"><i class="fa fa-angle-left"></i></div>
                <div class="text">BACK</div>
            </div>
        </template>
        <template v-else>
            <div class="srch_block">
                <template v-if="$parent.title!==$store.state.dashboard.prefix">
                    <div class="icon"><i class="fa fa-search"></i></div>
                    <div class="srch_input_block">
                        <input type="text" placeholder="search for an entry..." v-model="keyword" @keyup.enter="req_search()" />
                    </div>
                </template>
                <template v-else>
                    <div class="icon"><i class="fa fa-chart-area"></i></div>
                </template>
            </div>
        </template>
        <div class="page_highlight">
            <span><i class="fa fa-signal"></i> {{ title }}</span>
        </div>
        <div class="user_login_info">
            <div class="photo">
                <!-- <i class="fa fa-user"></i> -->
                <Gravatar :email="$store.state.auth_info.user_data.email" :size="40" />
            </div>
            <div class="info">
                <div class="name" v-if="$store.state.auth_info.user_data.user_info">{{ $store.state.auth_info.user_data.user_info.full_name }}</div>
                <div class="action_btn">
                    <span @click="$parent.user_logout"><i class="fa fa-power-off"></i> Logout</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapMutations } from 'vuex'
export default {
    name: 'HeaderContainer',
    props: ['title'],
    data(){
        return {
            keyword: ''
        }
    },
    watch: {
        $route(to, from ){
            this.keyword = ''
            this.req_search()
        }
    },
    methods: {
        ...mapMutations({
            search_status: 'SERACH_STATUS',
            add_new_entity: 'ADD_NEW_STATUS',
            form_submit: 'FORM_SUBMIT_STATUS'
        }),
        req_search: function(){
            this.search_status(this.keyword)
        },
        form_close: function(){
            this.$router.push(this.$parent.cur_path)
            this.add_new_entity(false)
            this.form_submit(false)
        }
    }
}
</script>
<style scoped>
    .header_container{        
        display: flex;
        letter-spacing: 0px;
        min-width: 44rem;
    }
    /* .header_container > div:first-child{
        margin-right: 10px;
    } */
    .header_container > div:nth-child(1){
        display: flex;
        flex: 2 1 0%;
    }
    .header_container > div:nth-child(1) input{
        position: relative;
        width: 100%;
        border: none;
        height: calc(100% - 2px);
        outline: none; color: #666;
    }
    .header_container .icon{
        margin-right: 15px;
        font-size: 20px;
        color: #bbb;
    }
    .header_container .srch_input_block{
        width: 100%
    }
    .back_block{
        cursor: pointer
    }
    .back_block > .text{
        padding-right: 25px;
        border-right: 1px solid #ddd;
    }
    .page_highlight{
        margin: 0 12px;
    }
    .page_highlight > span{        
        background-color: #dbf6ff;
        border: 1px solid #84d7d8;
        padding: 2px 15px;
        border-radius: 5px;
        font-size: 13px;
        color: #477bdf;
    }
    .page_highlight > span i{
        padding-right: 5px;
        font-size: 10px;
    }
    .user_login_info{
        display: flex;        
        padding-top: 10px;
    }
    .user_login_info .photo{
        margin: 0 10px;
        width: 40px;
        height: 40px;
        line-height: 38px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 50%;
        overflow: hidden
    }
    .info > div{
        line-height: 20px;
        font-size: 13px;
    }
    .info > div.action_btn{
        font-size: 12px; color: #006699;
        cursor: pointer;
        transition: all 0.4s;
    }
    .info > div.action_btn:hover{
        color: #CD0000
    }
</style>