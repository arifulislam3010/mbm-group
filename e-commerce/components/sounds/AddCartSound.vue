<template>
    <div class="add_cart_sound_block">
        <audio id="audio" src="@/assets/sounds/button-1.wav" autostart="false" ></audio>
    </div>
</template>
<script>
export default {
    name: 'AddCardSoundBlock',
    mounted(){
        this.PlaySound();
    },
    methods: {
        PlaySound() {
            var sound = document.getElementById("audio")
            sound.play()
        }
    }
}
</script>