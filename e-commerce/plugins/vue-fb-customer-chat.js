import Vue from 'vue'
import VueFbCustomerChat from 'vue-fb-customer-chat'

Vue.use(VueFbCustomerChat, {
  page_id: 100670965811683, //  change 'null' to your Facebook Page ID,
  theme_color: '#293168', // theme color in HEX
  locale: 'en_US', // default 'en_US'
})