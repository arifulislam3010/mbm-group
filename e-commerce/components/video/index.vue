<template>
    <div>
            <div class="container clearfix" id="video_content_block">
                <div class="row">
  <div class="column" v-for="(item,index) in items" :key="index">
                        <div class="card text-center">
                            <a :href="item.content_link" target="_blank">
                        <div class="content_img">
                            <div class="overlap"></div>
                            <div class="icon">
                                <i class="fa fa-play"></i>
                            </div>
                            <img   :src="item.cover_photo" />
                            
                        </div>
                        <div class="card-footer text-muted">
                            <ul class="links">
          <li><i class="fa fa-calendar" aria-hidden="true"></i><span>৩০ মার্চ ২০২০</span></li>
          <li><i class="fa fa-eye"></i><span>১৫</span></li>
      </ul>
                        </div>
                            </a>
                    </div>
  </div>
 



      
</div>



    </div>

    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
    name: 'VideoContentBlock',
    data(){
        return {
            pre_loader: false,
            items: [],
            sel_tab_item: 2,            
            limit: 16
        }
    },
    mounted(){
        this.load_content();
    },
    methods: {
        ...mapActions({
            videoContentList: 'video_content_info/GET_DATA'
        }),
        sel_tab_content: function(val){
            if(this.sel_tab_item!==val){
                this.sel_tab_item = val;
                this.load_content();
            }
        },
        async load_content(){
            var url = '/api/load-video-content/' + this.sel_tab_item + '/' + this.limit;
            let getTokenType = JSON.parse(localStorage.getItem('oauth_token')); 
            
            this.pre_loader = true
            
            this.$axios.get(url,{
                headers: {
                    'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
                    'Content-Type': 'application/json'
                }
            }).then((response) => {
                console.log('Get Live Video data',response.data)                
                this.items = response.data.data
                this.pre_loader = false
            });
        }
    }
}
</script>>
<style lang="scss" scoped>
    .links{
        margin-left:-33px;
        li{
            display: inline;
            span{
                color: blue;
            }
            i{
                cursor: pointer;
                background-color: #F1F3F4;
                border-radius: 50%;
                color: #92258E;
                border: 1x solid red;
                padding:10px;
            }
        }
    }
    .column{
        margin-bottom: 1.5em;
        margin-right: 2.8em;
        margin-left: 1.5em;
        img{
            height: 140px; width:200px;
        }
        .icon{
            position: absolute;
            top: 25%;
            left: 34%;
            z-index: 2;
            width: 50px;
            height: 50px;
            line-height: 50px;
            border-radius: 50%;
            font-size: 22px;
            text-align: center;
            background-color: #fff;
            color: #d47dbd;
            border: 3px solid #b5b5b5;
        }
    }
    #video_content_block{
        margin-top: 12px;
        padding: 0;
    }
    .tab_list{
        font-size: 20px;
        font-family: "Hind Siliguri Light";
        font-weight: bold;
        border-bottom: 3px solid #ddd;
        & > span{
            display: inline-block;
            padding-bottom: 10px;
            cursor: pointer;
            position: all 0.4s;
            &:hover,
            &.active{
                color: purple;
                border-bottom: 3px solid purple;
                margin-bottom: -3px;
            }
            i{
                margin-right: 5px;
            }
        }
    }
    .content_list .content_img{
        display: block;
        position: relative;
        height: 120px;
        background-color: #ccc;
        .overlap{
            position: absolute;
            background-color: #00000080;
            width: 100%; height: 100%;
            z-index: 1;
        }
        .content_img{
            .icon{
                position: absolute;
                top: 25%;
                left: 34%;
                z-index: 2;
                width: 50px;
                height: 50px;
                line-height: 50px;
                border-radius: 50%;
                font-size: 22px;
                text-align: center;
                background-color: #fff;
                color: #d47dbd;
                border: 3px solid #b5b5b5;
                cursor: pointer;
                &:before{
                    top: -11px;
                    left: -11px;
                    width: 30px;
                    height: 30px;
                    border: 1px solid #f7971d;
                    border-radius: 50%;
                    background: #f7971d80;
                    animation: circle 1.3s ease-out infinite;
                }
            }
            :deep(img){
                width: 100%; height: 100%;
                object-fit: cover;
                cursor: pointer;
            }
        }
    }
    .content_list .content_title{
        margin-top: 5px;
        border-left: 2px solid #ddd; padding-left: 8px
    }
    .content_list .content_title img{max-height: 40px;margin-bottom: 5px;}

    @keyframes circle{0%{opacity:1;transform:scale(.2)}to{opacity:0;transform:scale(1.8)}}
</style>