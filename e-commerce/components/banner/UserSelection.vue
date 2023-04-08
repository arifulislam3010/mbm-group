<template>
    <div class="user_selection_block mt-3">
        <div class="user_type_block clearfix">
            <div class="float-left">
                <span class="label">আমি একজন</span>
            </div>
            <div class="float-left ml-0 ml-sm-3">
                <div :class="['user_btn','clearfix',{active:select_user==1}]" @click="select_user_info(1,'student')">
                    <span class="icon"><img class="img-fluid" src="~/assets/images/student.png"></span>
                    <span class="caption">শিক্ষার্থী</span>
                </div>
            </div>
            <div class="float-left ml-0 ml-sm-3">
                <div :class="['user_btn','clearfix',{active:select_user==2}]" @click="select_user_info(2,'teacher')">
                    <span class="icon"><img class="img-fluid" src="~/assets/images/teacher.png"></span>
                    <span class="caption">শিক্ষক</span>
                </div>
            </div>
            <div class="float-left ml-0 ml-sm-3">
                <div :class="['user_btn','clearfix',{active:select_user==3}]" @click="select_user_info(3,'guardian')">
                    <span class="icon"><img class="img-fluid" src="~/assets/images/mother.png"></span>
                    <span class="caption">অভিভাবক</span>
                </div>
            </div>
        </div>

        <div :class="['user_education_level','clearfix','mt-2',{active:select_user==1}]">
            <div class="float-left bot_text">
                <span class="label">আমি</span>
            </div>
            <div class="float-left ml-3 mr-3">
                <select v-model="sel_edu_class" @change="goto_content_page">
                    <option value="">বাছাই করুন</option>
                    <option v-for="(item,index) in $store.state.edu_class_info.all_public_data" :key="index" :value="item.slug_name">{{ item.title_bn }}র</option>
                </select>
            </div>
            <div class="float-left">
                <span class="label">শিক্ষা উপকরণ খুঁজছি ।</span>
            </div>
            <!-- <div v-for="(level,index) in $store.state.edu_level_info.data" :key="index" class="float-left mb-2">
                <div :class="['user_btn','clearfix',{active:select_edu_level==level.id}]" @click="goto_content_page(level.id, level.slug_name)">
                    <span class="caption">{{ level.title_bn }}</span>
                </div>
            </div> -->
        </div>
    </div>
</template>
<script>
export default {
    name: 'UserSelectionBlock',
    data(){
        return {
            select_user: '',
            select_content_page: '',
            // select_edu_level: '',
            sel_edu_class: ''
        }
    },
    methods: {
        select_user_info: function(val, page){
            this.select_user = val;
            this.select_content_page = page;
            if(this.select_user!==1) this.$router.push('/contents/' + this.select_content_page);
        },
        // goto_content_page: function(val, page){
        //     this.select_edu_level = val;
        //     this.$router.push('/contents/' + this.select_content_page + '/' + page);
        // }
        goto_content_page: function(){
            this.$router.push('/contents/' + this.select_content_page + '/' + this.sel_edu_class);
        }
    }
}
</script>
<style lang="scss" scoped>
    .user_selection_block {
        font-family:"Hind Siliguri Light";
        font-weight: bolder;
    }
    .user_type_block .label{
        /* display: inline-block;
        font-family:"Hind Siliguri Light"; */
        font-weight: bolder;
        line-height: 50px;
        font-size: 26px;
        color: #ffffff;
        text-shadow: 0 0 3px #333;
    }
    .user_btn{
        display: inline-block;
        position: relative;
        border: 1px solid #dddddd;
        background-color: #ffffff;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.4s;
    }    
    .user_btn :deep(.icon){
        float: left;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        .caption{
            float: left;
            padding: 0 20px 0 12px;
            font-size: 20px;
            line-height: 46px;
        }
    }
    .user_btn:hover{
        background-color:cadetblue;
        :deep(.caption){
            color: #ffffff;
        }
    }
    .user_btn.active{
        background-color: #caae24;
        :deep(.caption){
            color: #ffffff;
        }
    }
    .user_education_level{
        position: absolute;
        top: -150px;
        opacity: 0;
        transition: all 0.4s;
        &.active{
            position: relative;
            opacity: 1;
            top: 0
        }
        .label{
            display: inline-block;
            /* font-family:"Hind Siliguri Light";
            font-weight: bolder; */
            line-height: 50px;
            font-size: 26px;
            color: #ffffff;
            text-shadow: 0 0 3px #333;
        }
        :deep(select){
            display: inline-block;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 20px;
            outline: none;
        }
    }
    /* .user_education_level > div{
        margin-left: 15px;
    }
    .user_education_level > div:first-child{
        margin-left: 0;
    }
    .user_education_level > div:nth-child(4n+1){
        clear: left;
        margin-left: 0;
    }
    .user_education_level .user_btn :deep(.caption){
        padding: 0 15px; line-height: 36px;
    } */
    @media (max-width: 575.98px) {
        .user_type_block .label{
            display:block; text-align:center;
        }
        .float-left {
            float: none !important;
        }
        .user_btn{ display:block; margin:0 0 10px 0;}
        .user_education_level .label{ display:block; text-align:center}
        .user_education_level select{ margin:0 auto; display:block;}
    }
</style>