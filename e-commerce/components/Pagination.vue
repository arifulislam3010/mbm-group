<template>
    <div class="clearfix">        
        <div :class="['pagination_block',{'float-left':align=='left'},{'float-right':align=='right'}]">
            <span class="label" v-html="total_record_display(data)"></span>
            <span :class="['pg_btn',{inactive:data.current_page<=1}]" @click="go_first" v-html="go_first_btn()"></span>
            <span :class="['pg_btn',{inactive:data.current_page<=1}]" @click="go_previous" v-html="go_previous_btn()"></span>
            
            <template v-if="pg_display_limit>=data.last_page">
                <span :class="['pg',{active:data.current_page==pg}]" v-for="(pg,index) in data.last_page" :key="index" @click="load_content(pg)" v-html="pg_num_display(pg)"></span>
            </template>
            <template v-else>
                
                <template v-if="first_page_display">                    
                    <span :class="['pg',{active:data.current_page==1}]" @click="load_content(1)" v-html="pg_num_display(1)"></span>
                    <span class="divider">...</span>
                </template>
                
                <template v-if="pg_list">
                    <span :class="['pg',{active:data.current_page==pg}]" v-for="(status,pg) in pg_list" :key="pg" @click="load_content(pg)" v-html="pg_num_display(pg)"></span>
                </template>

                <template v-if="last_page_display">
                    <span class="divider">...</span>
                    <span :class="['pg',{active:data.current_page==data.last_page}]" @click="load_content(data.last_page)" v-html="pg_num_display(data.last_page)"></span>
                </template>

            </template>

            <span :class="['pg_btn',{inactive:data.current_page>=data.last_page}]" @click="go_next" v-html="go_next_btn()"></span>
            <span :class="['pg_btn',{inactive:data.current_page>=data.last_page}]" @click="go_last" v-html="go_last_btn()"></span>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Pagination',
    props: ['config'],
    data(){
        return {
            allow_lang: {
                'en': true,
                'bn': true
            },
            sel_lang: 'en',
            pg_list: {},
            pg_display_limit: 7,
            center_pg_pos: 3,
            first_page_display: '',
            last_page_display: ''
        }
    },
    computed: {
        data: function(){
            return this.config.data
        },
        lang: function(){
            return this.config.lang
        },
        align: function(){
            return this.config.align
        },
        action: function(){
            return this.config.action
        }
    },
    mounted(){
        if(this.data.current_page - this.center_pg_pos > this.center_pg_pos) this.first_page_display = true
        if(this.data.current_page + this.center_pg_pos < this.data.last_page - (this.center_pg_pos - 1)) this.last_page_display = true
        if(this.first_page_display && this.last_page_display){
            for(var i=parseInt(this.data.current_page) - this.center_pg_pos; i<=parseInt(this.data.current_page) + this.center_pg_pos; i++){
                this.pg_list[i] = true
            }
        }else if(this.first_page_display){
            for(var i=parseInt(this.data.last_page) - this.pg_display_limit + 1; i<=parseInt(this.data.last_page); i++){
                this.pg_list[i] = true
            }
        }else if(this.last_page_display){
            for(var i=1; i<=parseInt(this.pg_display_limit); i++){
                this.pg_list[i] = true
            }
        }

        if(this.lang!='' && this.allow_lang[this.lang]){
            this.sel_lang = this.lang            
        }
        if(this.$route.query.page==1) this.$router.push({ path: this.$router.path })
        else if(this.$route.query.page>this.data.last_page) this.load_content(this.data.last_page)
    },
    methods: {
        total_record_display: function(obj){
            let str = '';
            if(this.sel_lang=='bn') str = 'পাতা ' + this.$enToBnNumber(obj.current_page) + ' এর ' + this.$enToBnNumber(obj.last_page);
            else str = 'Page ' + obj.current_page + ' of ' + obj.last_page;
            return str;
        },
        go_first_btn: function(){
            let str = '';
            // str = '&lt;&lt;';
            str = '<i class="fa fa-fast-backward"></i>';
            return str;
        },
        go_first: function(){            
            if(this.data.current_page>1) this.load_content(1)
        },
        go_previous_btn: function(){
            let str = '';
            // str = '&lt;';
            str = '<i class="fa fa-step-backward"></i>';
            return str;
        },
        go_previous: function(){
            if(this.data.current_page>1) this.load_content(this.data.current_page - 1)
        },
        go_next_btn: function(){
            let str = '';
            // str = '&gt;';
            str = '<i class="fa fa-step-forward"></i>';
            return str;
        },
        go_next: function(){            
            if(this.data.current_page<this.data.last_page) this.load_content(this.data.current_page + 1)
        },
        go_last_btn: function(){
            let str = '';
            // str = '&gt;&gt;';
            str = '<i class="fa fa-fast-forward"></i>';
            return str;
        },
        go_last: function(){
            if(this.data.current_page<this.data.last_page) this.load_content(this.data.last_page)
        },
        pg_num_display: function(pg){
            let str = '';
            if(this.sel_lang=='bn') str = this.$enToBnNumber(pg)
            else str = pg
            return str;
        },        
        load_content: function(pg){
            if(this.data.current_page!==pg){
                if(this.action!=='') this.action(pg);    
                else if(pg==1){
                    let query = Object.assign({}, this.$route.query);
                    delete query['page'];
                    this.$router.replace({ query });
                }else{
                    let obj = {}; obj['page'] = pg;
                    this.$router.push({ path: this.$router.path, query: Object.assign({}, this.$route.query, obj) });
                }
            }
        }
    }
}
</script>
<style scoped>
    .pagination_block{
        display: block;
        text-align: center
    }
    .pagination_block :deep(span){
        display: inline-block;
        height: 30px;
        line-height: 30px;
        padding: 0 10px;
        margin: 3px;
    }
    .pagination_block :deep(span.label){
        font-size: 13px;
        font-weight: bold;
        color: #666;
    }
    .pagination_block :deep(span.pg){
        background-color: #eee;
        border: 1px solid #ddd;
        border-radius: 50%;
        cursor: pointer;
    }
    .pagination_block :deep(span.pg_btn){
        background-color: #eee;
        border: 1px solid #ddd;
        /* padding: 10px 2px; */
        border-radius: 50%;
        cursor: pointer;
    }
    .pagination_block :deep(span.pg_btn.inactive){
        color: #ccc; cursor: auto
    }
    .pagination_block :deep(span.pg.active){
        background-color: #fff;
        color: #CD0000; font-weight: 700;
        border-left-color: #f26822;
        border-top-color: #44abe0;
        border-right-color: #fbb300;
        border-bottom-color: #999999;
        cursor: auto;
    }
    .pagination_block :deep(span.divider) {
        padding: 0; color: #999;
    }
    @media (max-width: 480px) {
        .pagination_block :deep(span.label){
            display: block
        }
    }
</style>