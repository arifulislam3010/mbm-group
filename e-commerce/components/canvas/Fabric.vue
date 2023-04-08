<template>
    <div id="drawingArea" class="drawing-area">					
        <div class="canvas-container">
            <canvas id="canvas"></canvas>
        </div>
    </div>
</template>
<script>    
    export default {
        name: 'FabricDrawingAreaBlock',
        props: ['width','height'],
        data() {
            return {
                canvas: ''
            }
        },
        mounted(){
            var canvas = new fabric.Canvas('canvas', {
                width: this.width,
                height: this.height
            });
            this.canvas = canvas;
            canvas.selection = false; // disable group selection
            var iText4 = new fabric.IText('Boxprint', {                
                // left: 200,
                // top: 50,
                fontFamily: 'Lobster',                
                caching: false,
                textAlign: 'center'
            });
            canvas.add(iText4);
            canvas.renderAll();
        }
    }
</script>
<style lang="scss" scoped>
    $width: 160;
    $height: 250;
    $border_width: 5;

    .drawing-area{
        position: absolute;
        top: 80px;
        left: 150px;
        z-index: 10;
        width: #{$width}px;
        height: #{$height}px;

        .canvas-container{
            position: relative;
            width: #{$width + $border_width}px;
            height: #{$height + $border_width}px;
            border: #{$border_width}px dashed #ccc;
            border-radius: 3px;
            user-select: none;

            #canvas{
                position: absolute;
                width: #{$width}px;
                height: #{$height}px;
                left: 0px; 
                top: 0px; 
                user-select: none; 
                cursor: default;
            }
        }
    }    
</style>