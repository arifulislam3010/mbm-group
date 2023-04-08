<template>
    <div class="rainbow_pre_loader">
        <div>Loading</div>
    </div>
</template>
<script>
export default {
    
}
</script>
<style lang="scss" scoped>
    @-webkit-keyframes rotate {
        100% {
            -webkit-transform: rotate(1turn);
                    transform: rotate(1turn);
        }
    }

    @keyframes rotate {
        100% {
            -webkit-transform: rotate(1turn);
                    transform: rotate(1turn);
        }
    }
    .rainbow_pre_loader {
        position: absolute;
        z-index: 2;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        padding: 15px;
        top: 40%;
        left: 42%;        
        &::before {
            content: '';
            position: absolute;
            z-index: -2;
            left: -50%;
            top: -50%;
            width: 200%;
            height: 200%;
            background-color: #399953;
            background-repeat: no-repeat;
            background-size: 50% 50%, 50% 50%;
            background-position: 0 0, 100% 0, 100% 100%, 0 100%;
            background-image: linear-gradient(#399953, #399953), linear-gradient(#fbb300, #fbb300), linear-gradient(#d53e33, #d53e33), linear-gradient(#377af5, #377af5);
            -webkit-animation: rotate 1s linear infinite;
                    animation: rotate 1s linear infinite;
        }
        &::after {
            content: '';
            position: absolute;
            z-index: -1;
            left: 5px;
            top: 5px;
            width: calc(100% - 5px);
            height: calc(100% - 10px);
            background: white;
            border-radius: 50%;
        }
        :deep(div){
            display: block;
            font-size: 12px;
            font-weight: bold;
            line-height: 60px;
            color: #666;
            text-align: center;
            border: 5px solid #eee;
            border-radius: 50%;
            border-left-width: 1px;
        }
    }
</style>