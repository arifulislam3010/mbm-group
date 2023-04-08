<template>
    <div>
        <div class="img-container">
            <img ref="image" :src="src" crossorigin>
        </div>        
    </div>
</template>

<script>
    import Cropper from "cropperjs";
    export default {
        name: "ImageCropper",
        data() {
            return {
                cropper: {},
                destination: {},
                image: {}
            }
        },
        props: {
            src: String
        },
        mounted() {
            this.image = this.$refs.image;
            this.cropper = new Cropper(this.image, {
                zoomable: false,
                scalable: false,
                aspectRatio: 1,
                crop: () => {
                    const canvas = this.cropper.getCroppedCanvas();
                    this.destination = canvas.toDataURL("image/png");
                }
            });
        }
    }
</script>

<style scoped>
    .img-container {
        width: 100%;
        height: 100%;        
    }
</style>