<template>
    <div class="category_list_block">
        <div class="black_mask_overlay" @click="$parent.close_category_list_block"></div>        
        <div class="content_block">
            <div class="head_info">
                <nuxt-link :to="{path: '/'}">
                    <Logo />
                </nuxt-link>
            </div>
            <NavList />
        </div>
    </div>
</template>
<script>
import Logo from '~/components/Logo'
import NavList from '@/pages/frontend/components/NavList'
export default {
    name: 'CategoryListBlock',
    components: {
        Logo,
        NavList
    }
}
</script>
<style lang="scss" scoped>
    .category_list_block{
        display: block;
    }
    .black_mask_overlay{
        position: fixed;
        left: 0; bottom: 0;
        width: 100%; height: 100%;
        background-image: linear-gradient(to top, #000000cc, #66666680);
    }
    .content_block{
        position: fixed;
        color: #333;
        background-color: #fff;
        width: 300px;
        height: 100%;
        overflow-y: auto;
        left: 0;
        top: 0;
        box-shadow: 0 0 15px #333;
    }
    .head_info{
        display: flex;
        background-color: #eee;
        height: 90px; padding: 0 15px;
        border-bottom: 1px solid #ddd;        
        & > a{
            align-self: center;
            width: 150px;
        }
        :deep(.logo){
            align-self: center;
            margin-left: auto;
            height: 70px;
            :deep(img){
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            :deep(i){
                font-size: 24px;
                color: #aaa;        
            }
        }
    }
</style>