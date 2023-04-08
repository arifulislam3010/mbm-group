<template>
    <div class="customer_info">        
        <div v-if="data.email" class="img">
            <Gravatar :email="data.email" :size="50" />
        </div>
        <div v-if="data.user_info" class="info">
            <div class="full_name">{{ data.user_info.full_name }}</div>
            <div v-if="data.email" class="email">
                <a :href="`mailto:${data.email}`">{{ data.email }}</a>
            </div>
            <div v-if="data.mobile" class="mobile">
                <a :href="`tel:${data.mobile}`">{{ data.mobile }}</a>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'CustomerInfoBlock',
    props: ['data']
}
</script>
<style scoped>
    .customer_info{
        display: flex;
        height: 100%;
    }
    .customer_info > .img{
        align-self: center;
        display: inline-block;        
        width: 50px; height: 50px;
        border-radius: 50%; overflow: hidden;
    }
    .customer_info > .info{
        align-self: center;
        padding: 0 15px;
    }
    .customer_info > .info .full_name{
        font-size: 13px; font-weight: bold; color: #666
    }
    .customer_info > .info .email,
    .customer_info > .info .mobile{
        font-size: 12px; font-weight: bold;
    }
    .customer_info > .info .email > a{
        color: purple
    }
    .customer_info > .info .email > a:hover{
        color: rebeccapurple;
    }
    .customer_info > .info .mobile > a{
        color:rgb(199, 67, 53)
    }
    .customer_info > .info .mobile > a:hover{
        color:sienna;
    }
</style>