<template>
    <div id="advance_srch_modal_block">
        <div class="black_overlay">
            <div class="white_overlay">
                <span class="close_btn" @click="popup_close">x</span>
                <div class="header_title mb-2">
                    <i class="fa fa-info-circle"></i>
                    <span>বাছাই করুন</span>
                </div>
                <div class="content">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">সহযোগী প্রতিষ্ঠান</span>
                        </div>
                        <div class="col-md-9">
                            <select class="partner_list" v-model="sel_partner_id" @change="filter_click('partner_id',sel_partner_id)">
                                <option value="">নির্বাচন করুন</option>
                                <option v-for="(partner,index) in $store.state.partner_info.public_data" :key="index" :value="partner.id">{{ partner.name }}</option>
                            </select>
                        </div>
                    </div>                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">ধরন</span>
                        </div>
                        <div class="col-md-9">
                            <div class="type_list clearfix">
                                <span :class="['item',{active:$route.query.academic_type==1}]" @click="filter_click('academic_type',1)">বিষয়ভিত্তিক</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.academic_type==2}]" @click="filter_click('academic_type',2)">নন-একাডেমিক</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">ব্যবহারকারী</span>
                        </div>
                        <div class="col-md-9">
                            <div class="type_list clearfix">
                                <span :class="['item',{active:$route.query.for_student=='1'}]" @click="filter_click('for_student',1)">শিক্ষার্থী</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.for_teacher=='1'}]" @click="filter_click('for_teacher',1)">শিক্ষক</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.for_guardian=='1'}]" @click="filter_click('for_guardian',1)">অভিভাবক</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">কন্টেন্ট ভিউ টাইপ</span>
                        </div>
                        <div class="col-md-9">
                            <div class="type_list clearfix">
                                <span :class="['item',{active:$route.query.featured=='1'}]" @click="filter_click('featured',1)">ফিচার্ড</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.status=='1'}]" @click="filter_click('status',1)">প্রকাশিত</span>
                                <span :class="['item','ml-2','ml-md-3',{active:$route.query.status=='0'}]" @click="filter_click('status',0)">অপ্রকাশিত</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">প্রকাশের সময়</span>
                        </div>
                        <div class="col-md-9 date_range_block">
                            <span>
                                <date-range-picker v-model="sel_date_range" />
                            </span>
                            <span class="sel_btn" @click="filter_click('date_range',JSON.stringify(sel_date_range))"><i class="fa fa-check"></i></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">শিক্ষার স্তর</span>
                        </div>
                        <div class="col-md-9">
                            <select class="edu_level_list" v-model="sel_edu_level_id" @change="filter_click('edu_level_id',sel_edu_level_id)">
                                <option value="">নির্বাচন করুন</option>
                                <option v-for="(item,index) in $store.state.edu_level_info.data" :key="index" :value="item.id">{{ item.title_bn }}</option>
                            </select>
                        </div>
                    </div>
                    <div v-if="sel_edu_level_id>0" class="row mb-3">
                        <div class="col-md-3">
                            <span class="caption">শ্রেণী</span>
                        </div>
                        <div class="col-md-9">
                            <template v-if="class_list.length>0">
                                <select class="edu_class_list" v-model="sel_edu_class_id" @change="filter_click('edu_class_id',sel_edu_class_id)">
                                    <option value="">নির্বাচন করুন</option>                                
                                    <option v-for="(item,index) in class_list" :key="'cl' + index" :value="item.id">{{ item.title_bn }}</option>                                
                                </select>
                            </template>
                            <div v-else-if="class_pre_loader" class="content_loader"><i class="fa fa-cog fa-spin"></i> কন্টেন্ট লোডিং</div>
                            <div v-else class="content_loader">কোন তথ্য পাওয়া যায় নাই</div>
                        </div>
                    </div>
                    <div v-if="sel_edu_class_id>0" class="row mt-3">
                        <div class="col-md-3">
                            <span class="caption">বিষয়</span>
                        </div>
                        <div class="col-md-9">
                            <template v-if="subject_list.length>0">
                                <select class="subject_list" v-model="sel_subject_id" @change="filter_click('subject_id',sel_subject_id)">
                                    <option value="">বিষয় নির্বাচন করুন</option>
                                    <option v-for="(item,index) in subject_list" :key="index" :value="item.id">{{ item.title_bn }}</option>
                                </select>
                            </template>
                            <div v-else-if="subject_pre_loader" class="content_loader"><i class="fa fa-cog fa-spin"></i> কন্টেন্ট লোডিং</div>
                            <div v-else class="content_loader">কোন তথ্য পাওয়া যায় নাই</div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapState, mapActions } from 'vuex'
export default {
    name: 'AdvanceSrchModalBlock',
    data(){
        return {
            class_pre_loader: false,
            class_list: [],
            subject_pre_loader: false,
            subject_list: [],
            sel_partner_id: '',
            sel_edu_level_id: '',
            sel_edu_class_id: '',
            sel_subject_id: '',
            sel_date_range: [moment().format('DD/MM/YYYY'),moment().format('DD/MM/YYYY')],
            // sel_date_range: [moment().startOf('hour').format('DD/MM/YYYY hh:mm A'),moment().startOf('hour').format('DD/MM/YYYY hh:mm A')],
            // date_options: {
            //     timePicker: true,
            //     startDate: moment().startOf('hour'),
            //     endDate: moment().startOf('hour').add(32, 'hour'),
            //     locale: {
            //         format: 'DD/MM/YYYY hh:mm A'
            //     }
            // }
        }
    },
    watch: {
        $route(to, from){
            if(to.query.partner_id) this.sel_partner_id = to.query.partner_id
            if(to.query.date_range) this.sel_date_range = JSON.parse(to.query.date_range)
            if(to.query.edu_level_id) this.sel_edu_level_id = to.query.edu_level_id
            if(to.query.edu_class_id) this.sel_edu_class_id = to.query.edu_class_id
            if(to.query.subject_id) this.sel_subject_id = to.query.subject_id
            
            if(this.sel_edu_level_id>0) this.load_classes();
            if(this.sel_edu_class_id>0) this.load_edu_subject_info();
        }
    },
    mounted(){
        if(this.$route.query.partner_id) this.sel_partner_id = this.$route.query.partner_id
        if(this.$route.query.date_range) this.sel_date_range = JSON.parse(this.$route.query.date_range)
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
            
            this.class_pre_loader = true;
            this.$axios.get(url,{
                headers: {
                    'Authorization': oauth_token.token_type + ' ' + oauth_token.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                console.log('Get Class data',response.data);
                this.class_list = response.data;
                this.class_pre_loader = false;
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

            this.subject_pre_loader = true;
            await this.eduSubjectList(request_data);            
            
            this.subject_list = this.$store.state.edu_subject_info.public_data;
            this.subject_pre_loader = false;
        },
        async filter_click(type,val,text=''){
            if(type=='edu_level_id'){
                if(this.$route.query.edu_class_id) delete this.$route.query.edu_class_id;
                if(this.$route.query.subject_id) delete this.$route.query.subject_id;
            }
            
            let obj = {}; obj[type] = val;
            // let metaObj = {}; metaObj[type] = text
            
            this.$router.push({ query: Object.assign({}, this.$route.query, obj) });
            // this.$router.push({ meta: Object.assign({}, this.$route.meta, metaObj) });

            if(this.$parent.advance_srch_modal) this.popup_close();
        },        
        popup_close: function(){
            this.$parent.advance_srch_modal=false
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
        :deep(i){
            margin-right: 8px;
        }
    }
    .content{
        display: block;
        width: 100%;
        background-color: #fff;
        height: auto;
        font-family: "Hind Siliguri Light";
        padding: 25px 20px;
        .caption{
            display: block;
            font-size: 18px; font-weight: bolder;
            line-height: 32px;
            text-align: right;
        }
        :deep(select){
            display: inline-block;
            padding: 5px 15px;
            border: 1px solid #dddddd;
            border-radius: 25px;
            outline: none;
        }
        .date_range_block{
            :deep(input){
                width: 50%;
                padding: 5px 15px;
                color: #666;
                border-radius: 25px;
                border: 1px solid #ddd;
                text-align: center;
            }
            :deep(.sel_btn){
                display: inline-block;
                padding: 5px 10px;
                background-color: #eee;
                border-radius: 25px;
                margin-left: 15px;
                line-height: 20px;
                border: 1px solid #ddd;
                cursor: pointer;
                &:hover{
                    background-color:darkkhaki;
                    color: #ffffff;
                }
            }
        }
    }
    .content .type_list{
        display: block;
        :deep(span){
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
    }    
    .subject_type_list :deep(span){
        margin-bottom: 10px;
    }
    .content_loader {
        line-height: 30px;
    }
    @media (max-width: 480px) {
        .white_overlay{
            top: 5%;
            width: 90%;
            left: 5%;
        }
        .header_title{
            font-size: 17px;
            :deep(i){
                margin-right: 5px;
            }
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