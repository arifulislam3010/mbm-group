<template>
    <div class="content_header_block">
        <div class="row">
            <div class="col-sm-6 header-title">
                <template v-if="$store.state.add_new_status">
                    <h4>Create an entry</h4>
                    <p>API ID: {{ title }}</p>
                </template>
                <template v-else>
                    <h4>{{ $strCapitalize(title) }}</h4>
                    <p>{{ total_rows }} entry found</p>
                </template>
            </div>
            <div class="col-sm-6 justify-content-end">
                <template v-if="$store.state.add_new_status">
                    <button type="button" class="cancel_btn" @click="form_close">
                        <i class="far fa-times-circle"></i> Cancel
                    </button>

                    <button type="button" class="save_btn ml-2" @click="form_submit(true)">
                        <i class="far fa-check-circle"></i> Save
                    </button>
                </template>
                <template v-else-if="$parent.$parent.role_access.create">
                    <button type="button" class="add_btn" @click="form_open">
                        <i class="fa fa-plus-circle"></i> Add new {{ title }}
                    </button>                    
                </template>
            </div>
        </div>
    </div>
</template>
<script>
import { mapMutations } from 'vuex'
import $ from 'jquery'
export default {
    name: 'ContentHeaderBlock',
    props: ['title','total_rows'],
    data(){
        return {
            cur_path: this.$route.path,
            action_btn_offset: ''
        }
    },
    mounted(){
        this.$nextTick(function() {
            this.action_btn_offset = $( ".justify-content-end" ).offset();
            window.addEventListener('scroll', this.onScroll);
            this.onScroll(); // needed for initial loading on page
        });
    },
    methods: {
        ...mapMutations({
            add_new_entity: 'ADD_NEW_STATUS',
            form_submit: 'FORM_SUBMIT_STATUS'
        }),
        onScroll: function(){                
            // console.log($('html,body').scrollTop(),'>', offset.top)
            if ( $('html').scrollTop() > this.action_btn_offset.top){
                $('.justify-content-end').addClass('sticky');
            } else {
                $('.justify-content-end').removeClass('sticky');
            }
        },
        form_open: function(){
            this.$router.push(this.cur_path + '?action=new')
            this.add_new_entity(true)
            this.$parent.$refs.container_block.edit_content_id=null
        },
        form_close: function(){
            this.$router.push(this.cur_path)
            this.add_new_entity(false)
            this.form_submit(false)
        }
    },
    beforeDestroy(){
        window.removeEventListener('scroll', this.onScroll);
    }
}
</script>
<style scoped>
    h4{        
        color: #000;
    }
    .justify-content-end{
        text-align: right;
    }
    .justify-content-end > button{                
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: center;
        outline: 0px;
        color: #fff;
        border: none;
        padding: 5px 15px;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.4s;
    }
    .justify-content-end > button i{
        margin-right: 5px;
    }
    .justify-content-end > button.add_btn{
        background-color: #0097f7;
    }
    .justify-content-end > button.add_btn:hover{
        background-color: #006699;
    }
    .justify-content-end > button.cancel_btn{
        background-color: #CD0000;
    }
    .justify-content-end > button.cancel_btn:hover{
        background-color: darkorange;
    }
    .justify-content-end > button.save_btn{
        background-color: green;
    }
    .justify-content-end > button.save_btn:hover{
        background-color: lightseagreen;
    }
    .justify-content-end.sticky{
        position: fixed;
        z-index: 100;
        right: 0px;
        top: 0;
        background-color: #fff;
        height: 59px;
        padding: 13px 25px 0 0;
    }
</style>