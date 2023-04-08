<template>
    <div id="initial_modal_block">
        <div class="black_overlay">
            <div class="white_overlay">
                <div class="content">
                    <h4>আপনি কে <i class="fa fa-question"></i></h4>
                    <div class="option_block">
                        <div class="user_selection_btn">
                            <span @click="goto_page(1)"><img src="~/assets/images/student.png" /> শিক্ষার্থী</span>
                        </div>
                        <div class="user_selection_btn">
                            <span @click="goto_page(2)"><img src="~/assets/images/teacher.png" /> শিক্ষক</span>
                        </div>
                        <div class="user_selection_btn">
                            <span @click="goto_page(3)"><img class="img-fluid" src="~/assets/images/mother.png"> অভিভাবক</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'InitialModalBlock',
    data(){
        return {
            'Question_view': false
        }
    },
    methods: {
        goto_page: function(val){
            this.$parent.init_modal_show = false
            if(val==1){
                localStorage.setItem('init_user_type', val)
                this.$router.push('/')
            }else if(val==2){
                localStorage.setItem('init_user_type', val)
                this.$router.push('/teacher-page')
            }else if(val==3){
                localStorage.setItem('init_user_type', val)
                this.$router.push('/guardian-page')
            }
        }
    }
}
</script>
<style lang="scss" scoped>
    #partner_auth_modal_block{
        position: relative;
        height: 100%;
    }
    .white_overlay{
        width: 70%;
        left: 15%;
        top: 10%;
        height: 450px;
        max-height: 100%;
    }
    .content{
        display: block;
        width: 100%;
        height: 100%;
        font-family: "Hind Siliguri Light";
    }
    .content > h4{
        padding: 20px;
        border-bottom: 5px solid #ddd;
        text-align: center;
        font-size: 30px;        
        font-weight: bolder;
        color: #666;
    }
    .option_block {
        padding: 5px 15px
    }
    .user_selection_btn{
        display: block;                
        text-align: center;
        margin: 15px;        
        :deep(span){
            padding: 15px;
            border: 1px solid #dddddd;
            box-shadow: 0 0 15px #cccccc;
            display: inline-block;
            min-width: 250px;
            border-radius: 5px;
            font-size: 30px;
            font-weight: bolder;
            color: #666;
            line-height: 60px;
            cursor: pointer;
            transition: all 0.4s;
        }
        &:hover :deep(span){
            font-size: 34px; color: purple
        }
        :deep(img){
            width: 60px; height: 60px;
            object-fit: cover; margin-right: 10px;
        }
    }
</style>