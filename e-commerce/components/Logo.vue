<template>
  <div class="logo">
    <!-- <img src="~/images/main-site-logo.png" title="logo" /> -->
    <template v-if="type=='white'">
      <nuxt-img
        v-if="$store.state.logo_info_data.dark_logo"
        format="webp"
        title="Logo"
        alt="Logo"
        :src="$store.state.logo_info_data.dark_logo"        
      />
      <nuxt-img
        v-else        
        format="webp"
        alt="Logo"
        src="/images/logo.white.png"      
      />
    </template>
    <template v-else>
      <nuxt-img
        v-if="$store.state.logo_info_data.logo"
        format="webp"
        alt="Logo"
        :src="$store.state.logo_info_data.logo"
      />
      <nuxt-img
        v-else
        format="webp"
        alt="Logo"
        src="/images/logo.png"      
      />
    </template>    
  </div>
</template>
<script>
export default {
  name: 'LogoBlock',
  props: ['type']
}
</script>
<style scoped>
  .logo{
    display: flex;
    width: 100%;
    height: 65px;
    /* padding: 8px 0; */
  }
  .logo > img{
    align-self: center;
    width: 100%; height: 100%;
    object-fit: contain;
  }
  .logo > .logo_title{
    align-self: center;
  }
</style>
