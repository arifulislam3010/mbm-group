<template>
  <div class="main-block">    
    <authFormBlock v-if="$store.state.auth_form_open_status" />
    <component :is="headerSection"></component>
    <navigationBar v-if="!$device.isMobile" ref="site_navigation" />
    <bodySection />    
    <component :is="footerSection"></component>
    <MobileBottomNavBar v-if="$device.isMobile" />
    <RunnigOrderStatus v-if="$store.state.auth_info.loggedIn && $store.state.running_order_info.length" />
    <OrderDeliveryReviewBlock v-if="$store.state.auth_info.loggedIn && delivery_review_block_show && $store.state.order_delivery_review_pending_info.length" />
  </div>
</template>

<script>
  import MobileBottomNavBar from '@/components/mobile_bottom_nav_bar'
  import RunnigOrderStatus from '@/components/content_display/RunnigOrderStatusBlock'
  import OrderDeliveryReviewBlock from '@/components/content_display/OrderDeliveryReviewBlock'
  import authFormBlock from '@/pages/frontend/authForm'
  import navigationBar from '@/pages/frontend/navigation'
  import bodySection from '@/pages/frontend/body'  
  import { mapActions, mapMutations } from 'vuex'
  export default {
    name: 'MainBlock',
    scrollToTop: true,    
    components: {      
      authFormBlock,      
      navigationBar,
      bodySection,      
      MobileBottomNavBar,
      RunnigOrderStatus,
      OrderDeliveryReviewBlock
    },
    data(){
      return {        
        base_url: process.env.BASE_URL,
        user_access_token: this.$store.state.oauth_token.token,
        cur_fcm_token: '',
        delivery_review_block_show: true,
        limit: 20
      }
    },
    head: {      
      titleTemplate: '%s - Home Page',
      meta: [
        { hid: 'description', name: 'description', content: 'Home page description' }
      ],
    },
    watch: {
      $route(to,from){
        if(this.$store.state.auth_info.loggedIn && this.$store.state.auth_info.user_data.user_type==this.$store.state.customer_user_type_id){
          // this.setupOrderTimelineStream();
          this.getRunningOrderList();
          this.getOrderDeliveryReviewPendingList();
        }        
      },
      timeline_stream_status: function(status){
        if(status) this.setupOrderTimelineStream();
      }
    },
    computed: {
      page_title: function(){
        return 'Home page'
      },
      current_route: function(){
        return this.$route.name
      },
      timeline_stream_status: function(){        
        this.$store.state.order_timeline_sse_status
      },
      headerSection: function() {
        return () => import(`@/components/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/header`);
      },
      footerSection: function() {
        return () => import(`@/components/${this.$store.state.themeDirName}/${process.env.DEFAULT_THEME}/footer`);
      }
    },
    async mounted(){
        this.getCurrentLocation();
        
        this.cur_fcm_token = localStorage.getItem('fcm_token')
        // if(!cur_fcm_token) 
        this.firebasePushNotificationSetup();
        
        if(this.$store.state.static_page_info.get_data.length==0) this.load_data();
        else this.data = this.$store.state.static_page_info.get_data;
                
        if(this.$store.state.auth_info.loggedIn && this.$store.state.auth_info.user_data.user_type==this.$store.state.customer_user_type_id){
          // this.setupOrderTimelineStream();
          this.getRunningOrderList();
          this.getOrderDeliveryReviewPendingList();
        }
        
        // console.log(navigator.platform + '/' + navigator.product + '/' + navigator.vendor)
    },
    methods: {
      ...mapActions({        
        get_site_navigation: 'static_page_info/GET_DATA'            
      }),
      ...mapMutations({
        set_cur_lat_lng: 'CUR_LAT_LNG',
        auth_form_open_status: 'AUTH_FORM_OPEN_STATUS',
        running_order_status: 'RUNNING_ORDER_STATUS',
        order_delivery_review_pending_status: 'ORDER_DELIVERY_REVIEW_PENDING_STATUS'
      }),
      getCurrentLocation: function() {        
        navigator.geolocation.getCurrentPosition(function(position) {
          // Get the coordinates of the current position.
          console.log("Current Position", position);          

          // console.log("Latitude : " + position.coords.latitude);
          // console.log("Longitude : " + position.coords.longitude);
        });
      },
      firebasePushNotificationSetup: function(){
        if(this.$fire.messaging && this.$fireModule.messaging){
          // console.log('Get Messaging')          

          var messaging = this.$fire.messaging;
          // console.log(messaging)          

          messaging
          .requestPermission()
          .then( async () => await messaging.getToken())
          .then((token) => {
              // console.log('Token', token) // Receiver Token to use in the notification

              localStorage.setItem('fcm_token', token)

              // messaging.deleteToken(token)

              this.fcmTokenStore(token)
          })
          .catch(function(err) {
              console.log("Unable to get permission to notify.", err);
          });

          var elm = this
          messaging.onMessage(function(payload) {
            // console.log("Message received. ", payload);
            if(payload.data['gcm.notification.type'] && payload.data['gcm.notification.type']=='timeline') elm.getRunningOrderList();

            elm.$notify.success({
                title: 'Success',
                message: payload.notification.body,
                position: 'bottomRight',
                transitionIn: 'bounceInRight',
                transitionOut: 'fadeOutLeft',
                timeout: 1500
            })
          });
        }
      },
      async fcmTokenStore(getToken){
        let formData = {
          cur_token: this.cur_fcm_token,
          app_platform: navigator.platform,
          token: getToken,
          user_id: this.$store.state.auth_info.user_data.id
        }
        let url = '/api/fcm-token-info/store';
        this.$axios.post(url, formData, {
            headers: {
                'Authorization': 'Bearer ' + this.user_access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': this.user_access_token
            }
        }).then( (response) => {
          console.log(response)          
        }).catch(e => {
          this.$toast.error('Fcm token store failed!!!', {icon: "error_outline"})
        });
      },
      async load_data(){
        // setup submitted data
        let config_data = {                
            access_token: this.user_access_token,
            limit: this.limit                
        }

        // content pre loader active
        if(!this.$device.isMobile) this.$refs.site_navigation.pre_loader = true

        // call function for load data
        await this.get_site_navigation(config_data)

        // get products list
        if(!this.$device.isMobile) this.$refs.site_navigation.data = this.$store.state.static_page_info.get_data

        // call for submit
        if(!this.$device.isMobile) this.$refs.site_navigation.pre_loader = false
      },
      getOrderDeliveryReviewPendingList(){
        let url = '/api/order-delivery-person-infos/review/pending';
        this.$axios.get(url, {
            headers: {
                'Authorization': 'Bearer ' + this.$store.state.auth_info.user_data.token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': this.$store.state.auth_info.user_data.token
            }
        }).then( (response) => {
          console.log(response)
          if(response.data && response.data.data) this.order_delivery_review_pending_status(response.data.data)
        }).catch(e => {
          this.$toast.error('Order delivery review pending  failed!!!', {icon: "error_outline"})
        });
      },
      getRunningOrderList() {
        if(this.$store.state.auth_info.loggedIn){
          let url = '/api/my-orders/live/timeline-status';
          this.$axios.get(url, {
              headers: {
                  'Authorization': 'Bearer ' + this.$store.state.auth_info.user_data.token,
                  'Content-Type': 'application/json',
                  'X-XSRF-TOKEN': this.$store.state.auth_info.user_data.token
              }
          }).then( (response) => {
            console.log(response)
            if(response.data && response.data.data) this.running_order_status(response.data.data)
          }).catch(e => {
            this.$toast.error('Order delivery review pending  failed!!!', {icon: "error_outline"})
          });
        }
      }
      // setupOrderTimelineStream() {        
      //   let es = new EventSource(`${process.env.API_ENDPOINT}/live/timeline-status?user_id=` + this.$store.state.auth_info.user_data.id, {
      //       headers: {
      //           'Authorization': 'Bearer ' + this.user_access_token,
      //           'Content-Type': 'application/json',
      //           'X-XSRF-TOKEN': this.user_access_token
      //       }
      //   }, { withCredentials: true });

      //   es.addEventListener('message', event => {
      //     let data = JSON.parse(event.data);
      //     // this.stockData = data.stockData;
      //     // console.log('data', event.data)
      //     // console.log('len', data.length)
      //     // if(data.length>0){
      //     this.running_order_status(data)
      //     // }else 
      //     es.close();
      //   }, false);

      //   es.addEventListener('error', event => {
      //     if (event.readyState == EventSource.CLOSED) {
      //       console.log('Event was closed');
      //       console.log(EventSource);
      //     }
      //   }, false);
      // }
    }
  }
</script>

<style scoped>
  .main-block{
    background-color: #eee;
  }
</style>