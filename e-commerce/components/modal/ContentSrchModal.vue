<template>
    <div id="content_srch_modal_block">
        <div class="black_overlay">
            <div class="white_overlay">
                <span class="close_btn" @click="popup_close">x</span>
                <div class="header_title mb-2 mb-md-4">
                    <i class="fa fa-info-circle"></i>
                    <span>সকল শিক্ষা উপকরণ হতে বাছাই করুন</span>
                </div>
                <div class="content">
                    <div v-if="edu_level_filter=='yes'" class="row mb-3 mb-md-4">
                        <div class="col-md-2">
                            <span class="caption">শিক্ষার স্তর</span>
                        </div>
                        <div class="col-md-10">
                            <select class="edu_level_list" v-model="sel_edu_level_id" @change="filter_click('edu_level_id',sel_edu_level_id)">
                                <option value="">নির্বাচন করুন</option>
                                <option v-for="(item,index) in $store.state.edu_level_info.data" :key="index" :value="item.id">{{ item.title_bn }}</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="edu_level_filter=='yes' && class_list.length>0" class="row mb-3 mb-md-4">
                        <div class="col-md-2">
                            <span class="caption">শ্রেণী</span>
                        </div>
                        <div class="col-md-10">
                            <select class="edu_class_list" v-model="sel_edu_class_id" @change="filter_click('edu_class_id',sel_edu_class_id)">
                                <option value="">নির্বাচন করুন</option>
                                <template v-if="class_list.length>0">
                                    <option v-for="(item,index) in class_list" :key="'cl' + index" :value="item.id">{{ item.title_bn }}</option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <span class="caption">ধরন</span>
                        </div>
                        <div class="col-md-10">
                            <div class="type_list clearfix">
                                <span :class="['item',{active:$route.query.academic_type==1}]" @click="filter_click('academic_type',1)">বিষয়ভিত্তিক</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.academic_type==2}]" @click="filter_click('academic_type',2)">নন-একাডেমিক</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="edu_level_filter=='yes'" class="row mt-3 mt-md-4">
                        <div class="col-md-2">
                            <span class="caption">ব্যবহারকারী</span>
                        </div>
                        <div class="col-md-10">
                            <div class="type_list clearfix">
                                <span :class="['item',{active:$route.query.for_student=='1'}]" @click="filter_click('for_student',1)">শিক্ষার্থী</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.for_teacher=='1'}]" @click="filter_click('for_teacher',1)">শিক্ষক</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.for_guardian=='1'}]" @click="filter_click('for_guardian',1)">অভিভাবক</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="$store.state.edu_subject_info.public_data.length>0" class="row mt-3 mt-md-4">
                        <div class="col-md-2">
                            <span class="caption">বিষয়</span>
                        </div>
                        <div class="col-md-10">
                            <select class="subject_list" v-model="sel_subject_id" @change="filter_click('subject_id',sel_subject_id)">
                                <option value="">বিষয় নির্বাচন করুন</option>
                                <option v-for="(item,index) in $store.state.edu_subject_info.public_data" :key="index" :value="item.id">{{ item.title_bn }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3 mt-md-4">
                        <div class="col-md-2">
                            <span class="caption">বিষয়বস্তু</span>
                        </div>
                        <div class="col-md-10">
                            <div class="type_list subject_type_list clearfix">
                                <span :class="['item',{active:$route.query.category=='video'}]" @click="filter_click('category','video')">ভিডিও ক্লাস</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='learning'}]" @click="filter_click('category','learning')">ই-কন্টেন্ট</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='course'}]" @click="filter_click('category','course')">কোর্স</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='blog'}]" @click="filter_click('category','blog')">ব্লগ/ফোরাম</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='app'}]" @click="filter_click('category','app')">অ্যাপস/টুলস/প্ল্যাটফর্ম</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='app'}]" @click="filter_click('category','assessment')">মূল্যায়ন</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.category=='app'}]" @click="filter_click('category','class-routine')">ক্লাস রুটিন</span>
                                <!-- <span class="item inactive ml-2 ml-md-3">মূল্যায়ন</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
export default {
    name: 'ContentSrchModalBlock',
    props: ['edu_level_filter'],
    data(){
        return {
            class_list: [],
            sel_edu_level_id: '',
            sel_edu_class_id: '',
            sel_subject_id: ''
        }
    },
    watch: {
        $route(to, from){
            if(to.query.edu_level_id) this.sel_edu_level_id = to.query.edu_level_id
            if(to.query.edu_class_id) this.sel_edu_class_id = to.query.edu_class_id
            if(to.query.subject_id) this.sel_subject_id = to.query.subject_id
            
            if(this.sel_edu_class_id>0) this.load_edu_subject_info();
        }
    },
    mounted(){
        if(this.$route.query.edu_level_id) this.sel_edu_level_id = this.$route.query.edu_level_id
        if(this.$route.query.edu_class_id) this.sel_edu_class_id = this.$route.query.edu_class_id
        if(this.$route.query.subject_id) this.sel_subject_id = this.$route.query.subject_id

        if(this.sel_edu_level_id>0) this.load_classes();
        if(this.sel_edu_class_id>0) this.load_edu_subject_info();
    },
    methods: {
        ...mapActions({
            eduSubjectList: 'edu_subject_info/GET_PUBLIC_DATA_BY_CLASS_ID'
        }),
        load_classes: function(){
            var url = '/api/education-class/' + this.sel_edu_level_id;
            let oauth_token = JSON.parse(localStorage.getItem('oauth_token'));
            
            this.$axios.get(url,{
                headers: {
                    'Authorization': oauth_token.token_type + ' ' + oauth_token.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                console.log('Get Class data',response.data)
                this.class_list = response.data                
            });
        },
        async load_edu_subject_info(){
            let oauth_token = JSON.parse(localStorage.getItem('oauth_token'));
            
            // console.log(oauth_token)
            // setup request data
            let request_data = {
                edu_class_id: this.sel_edu_class_id,
                access_token: oauth_token.access_token
            }

            await this.eduSubjectList(request_data);
        },
        filter_click: function(type,val,text=''){                        
            if(val==''){
                let query = Object.assign({}, this.$route.query);
                delete query[type];
                this.$router.replace({ query });
            }else{
                let obj = {}; obj[type] = val;
                // let metaObj = {}; metaObj[type] = text
                
                this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
                // this.$router.push({ meta: Object.assign({}, this.$route.meta, metaObj) });
            }

            if(type=='edu_level_id') this.load_classes();
            else if(this.$parent.content_srch_modal) this.popup_close();
        },
        popup_close: function(){
            this.$parent.content_srch_modal=false
            document.body.classList.remove('popup_open')
        }
    }
}
</script>
<style lang="scss" scoped>    
    .white_overlay{
        width: 70%;
        left: 15%;
        top: 10%;
        height: 450px;
        max-height: 100%;
    }
    .header_title{
        display: block;
        padding: 15px;
        background-color: #00b5a2;
        font-family: "Hind Siliguri Light";
        font-size: 28px;
        font-weight: bolder;
        color: #ffffff;
    }    
    .header_title :deep(i){
        margin-right: 8px;
    }
    .content{
        display: block;
        width: 100%;
        background-color: #fff;
        height: auto;
        font-family: "Hind Siliguri Light";
        padding: 25px 20px
    }
    .content .caption{
        display: block;
        font-size: 18px; font-weight: bolder;
        line-height: 32px;
        text-align: right;
    }
    .content :deep(select){
        display: inline-block;
        padding: 5px 15px;
        border: 1px solid #dddddd;
        border-radius: 25px;
        outline: none;
    }
    .content .type_list{
        display: block;
    }
    .content .type_list :deep(span){
        float: left;
        padding: 5px 15px;
        margin-bottom: 10px;
        border: 1px solid #dddddd;
        border-radius: 25px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.4s;
        &:hover{
            background-color:darkkhaki;
            color: #ffffff;
        }
        &.active{
            background-color: coral;
            color: #ffffff;
        }
        &.inactive{
            background-color: #cccccc;
            color: #666;
            cursor: none;
        }
    }
    .subject_type_list :deep(span){
        margin-bottom: 10px;
    }
    @media (max-width: 575.98px) {
        .white_overlay{
            top: 5%;
            width: 90%;
            left: 5%;
        }
        .header_title{
            font-size: 17px;
        }
        .header_title :deep(i){
            margin-right: 5px;
        }
        .content{
            padding: 15px;
        }
        .content .caption{
            text-align: left; margin-bottom: 5px;
        }
        .subject_type_list :deep(span):nth-child(2n+1){
            clear: left;
            margin-left: 0 !important;
        }        
    }
</style>