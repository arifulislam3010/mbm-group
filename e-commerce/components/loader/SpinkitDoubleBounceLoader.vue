<template>
    <div class="spinkit-double-bounce-loader">
      <div class="spinner">
          <div class="double-bounce1"></div>
          <div class="double-bounce2"></div>
      </div>
    </div>
</template>
<script>
export default {
    name: 'SpinkitDoubleBounceLoader'
}
</script>
<style lang="scss" scoped>
.spinner {
  width: 80px;
  height: 80px;

  position: relative;
  margin: 50px auto;
}

.double-bounce1, .double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: $sys_brand_color;
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