<template>
    <div id="slogan_block">
        <h1>ঘরে বসেই শিক্ষা</h1>
    </div>
</template>
<script>
export default {
    name: 'SloganBlock'
}
</script>
<style scoped>
    #slogan_block{
        display: block;
        margin-top: 60px;
        font-family:"Hind Siliguri Light";
        font-weight: bolder;
    }
    h1{
        font-size: 50px;
        color: #fff;
        font-weight: bolder;
        text-shadow: 0 0 3px #333;
    }
    @media (max-width: 575.98px) {
        #slogan_block{
            padding-top: 20px; margin:0;
        }
        h1{
            font-size: 30px;
            text-align:center;
        }
    }
</style>