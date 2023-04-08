<template>
  <div class="main-block">
    <component :is="headerSection"></component>    
    <bodySection />    
    <component :is="footerSection"></component>    
  </div>
</template>

<script>  
  import bodySection from '@/pages/frontend/body'  
  export default {
    name: 'MainBlock',
    scrollToTop: true,
    components: {
      bodySection
    },
    data(){
      return {
      }
    },
    computed: {
      current_route: function(){
        return this.$route.name
      },
      headerSection: function() {
        return () => import(`@/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/header`);
      },
      footerSection: function() {
        return () => import(`@/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/footer`);
      }
    },
    mounted(){        
    }
  }
</script>

<style scoped>
  .main-block{
    background-color: #eee;
  }
</style>