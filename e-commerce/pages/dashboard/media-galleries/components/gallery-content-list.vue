<template>
    <div class="gallery_content_list_block">
        <!-- <template v-if="$parent.selected_arr.length>0">
            <label>Selected Contents</label>
            <div class="selected_content_block mb-4">
                <carousel
                    :paginationActiveColor="'#8800cd'"
                    :paginationColor="'#CCC'"
                    :paginationPadding="3"
                    :perPage="6"
                    :loop="true">
                    <slide v-for="(item,index) in $parent.selected_arr" :key="index">
                        <div class="selected_item">
                            <div class="img">
                                <img :src="item.content" />
                            </div>
                            <div class="info">
                                <div class="title">{{ item.content_title }}</div>
                            </div>
                        </div>
                    </slide>
                </carousel>
            </div>
        </template> -->
        <div class="row">
            <div class="col-md-8">
                <div class="srch_block mb-4">
                    <span class="input_box"><input v-model="$parent.srch_keyword" placeholder="Enter keyword what you want..." @keyup.enter="$parent.load_data" /></span>
                    <span class="srch_btn" @click="$parent.load_data"><i class="fa fa-search"></i></span>
                </div>
            </div>

            <div class="col-md-4 mb-4">                
                <div class="list_block category_list_block" ref="category_block">                            
                    <div><select v-model="$parent.srch_cat_id" class="form-control" @change="$parent.load_data">
                        <option value="">All Category</option>
                        <option v-for="(item,index) in $parent.category_list" :value="item.id" :key="index">{{ item.category_name }}</option>                                
                    </select></div>
                </div>
            </div>
            
            <template v-if="data.length>0">
                <div class="col-md-3 mb-4" v-for="(item,index) in data" :key="index" @click="$parent.select_item(item)">
                    <div :class="['item',{active:$parent.selected_item_ids[item.id]}]">
                        <div v-if="$parent.selected_item_ids[item.id]" class="active_overlay">
                            <i class="fa fa-check-circle fa-3x"></i>
                        </div>
                        <div class="img">
                            <img :src="item.content" />
                        </div>
                        <div class="info">
                            <div class="title">{{ item.content_title }}</div>
                        </div>
                    </div>
                </div>
            </template>
            <div v-else class="empty_content_block">
                <EmptyContentBlock />
            </div>
        </div>
    </div>
</template>
<script>
import EmptyContentBlock from '@/components/content_display/EmptyContentBlock'
export default {
    name: 'GalleryContentListBlock',
    props: ['data'],
    components: {
        EmptyContentBlock
    }
}
</script>
<style lang="scss" scoped>
    .gallery_content_list_block{
        margin: 25px;
    }
    label{
        font-size: 14px; font-weight: bold; color: #666;
    }
    .selected_content_block{
        display: block;
        background-color: #f7f7f7;
        padding: 15px; border: 1px solid #ddd
    }
    .selected_item{
        display: block;
        width: 100%; background-color: #fff;
        border: 1px solid #ddd;
        & > .img{
            display: block;
            /* background-color: #eee; */
            height: 100px;
            & > img{
                width: 100%; height: 100%;
                object-fit: contain;
            }
        }
        & > .info{
            display: flex; height: 50px;
            border-top: 1px solid #ddd;
            padding: 5px 8px;
            & > .title{
                font-size: 12px;
                align-self: center;
                margin: auto;
            }
        }
    }
    .srch_block{
        width: 100%;
        background-color: #f7f7f7;
        border: 1px solid #ddd;
        border-radius: 3px;
        position: relative;
        & > span{
            display: block;
            & > input{
                width: 100%;
                border: none;
                outline: none;
                padding: 8px 10px;
                border-radius: 3px;
            }
        }
        & > .srch_btn{
            display: inline-block;
            position: absolute;
            right: 10px;
            top: 8px;
            font-size: 16px;
            cursor: pointer;
            color: #999;
        }
    }
    .item{
        display: block;
        border: 1px solid #ddd;
        &.active{
            position: relative;
            border-color: #CD0000;
        }
        .active_overlay{
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: rgba(205,0,0,0.50196);
            color: #fff;
            display: flex;        
            z-index: 1;
            & > i{
                color: #fff;
                align-self: center;
                margin: auto;
            }
        }
        & > .img{
            display: block;
            background-color: #eee;
            height: 150px;
            & > img{
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }
        & > .info{
            display: block;
            border-top: 1px solid #ddd;
            padding: 15px
        }
    }
    .empty_content_block{
        display: flex;
        margin: 0 auto
    }
</style>