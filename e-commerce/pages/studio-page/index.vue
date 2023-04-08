<template>
    <div class="studio_control_panel">
        <img id="tshirt-bg-picture" src="@/assets/images/t-shirt/t-shirt-front.jpg"/>
        <FabricCanvasBlock :width="width" :height="height" />
    </div>
</template>

<script>
    import FabricCanvasBlock from '@/components/canvas/Fabric'
    export default {
        name: 'StudioPageBlock',
        components: {
            FabricCanvasBlock
        },
        data(){
            return {
                width: 160,
                height: 250
            }
        }
    }
</script>

<style lang="scss" scoped>
    .studio_control_panel{        
        position: relative;
        background-color: #fff;
        width: 452px;
        height: 548px;

        & > img{
            width: 100%; height: 100%; object-fit: cover;
        }
    }
</style>