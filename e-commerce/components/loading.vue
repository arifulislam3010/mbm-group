<template lang="html">
  <div class="loading-page" v-if="loading">
    <div class="spinner">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>
    <p>Loading...</p>
  </div>
</template>

<script>
export default {
    name: 'Loading',
    data: () => ({
        loading: false
    }),
    methods: {
        start () {
            this.loading = true
        },
        finish () {
            this.loading = false
        }
    }
}
</script>

<style scoped>
    .loading-page {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        text-align: center;
        padding-top: 60px;
        font-size: 30px;
        font-family: sans-serif;
        z-index: 10000;
    }
    .spinner {
        width: 80px;
        height: 80px;
        position: relative;
        margin: 100px auto;
    }

    .double-bounce1, .double-bounce2 {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #CD0000;
        opacity: 0.6;
        position: absolute;
        top: 0;
        left: 0;

        -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
        animation: sk-bounce 2.0s infinite ease-in-out;
    }

    .double-bounce2 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    @-webkit-keyframes sk-bounce {
        0%, 100% { -webkit-transform: scale(0.0) }
        50% { -webkit-transform: scale(1.0) }
    }

    @keyframes sk-bounce {
        0%, 100% { 
            transform: scale(0.0);
            -webkit-transform: scale(0.0);
        } 50% { 
            transform: scale(1.0);
            -webkit-transform: scale(1.0);
        }
    }
</style>