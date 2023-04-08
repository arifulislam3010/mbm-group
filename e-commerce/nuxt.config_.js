require('dotenv').config()
import { createSEOMeta } from "./utils/seo";
// import path from 'path'
// import fs from 'fs'

export default {
  // ssr: true,
  // target: 'static',
  server: {
    port: 3232, // default: 3000
    host: '0.0.0.0', // default: localhost
    // https: {
    //   key: fs.readFileSync(path.resolve(__dirname, 'server.key')),
    //   cert: fs.readFileSync(path.resolve(__dirname, 'server.crt'))
    // }
  },
  /*
  ** Headers of the page
  */
  head: {
    title: process.env.npm_package_name || '',
    htmlAttrs: {
      lang: 'en',
      amp: true
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ...createSEOMeta({
        title: process.env.APP_NAME,
        description: process.env.npm_package_description || '',
        image: "/static/_ipx/images/logo.png",
        url: process.env.BASE_URL
      })
    ],
    // meta: [
    //   { charset: 'utf-8' },
    //   { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    //   { hid: 'description', name: 'description', content: process.env.npm_package_description || '' }
    // ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'stylesheet', type: 'text/css', href: 'https://use.fontawesome.com/releases/v5.15.2/css/all.css' },
      { rel: 'stylesheet', type: 'text/css', href: 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' },
      { rel: 'stylesheet', type: 'text/css', href: 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' }
    ],
    script: [
      { src: 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js', type: 'text/javascript' },
      { src: 'https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', type: 'text/javascript' }
    ]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: {
    name: 'folding-cube',//'chasing-dots',
    color: '#006699',
    background: '#f16837',
    // height: '3px'
  },
  /*
  ** Global CSS
  */
  css: [
    '~/assets/css/style.css',
    '~/assets/scss/themes-'+ process.env.DEFAULT_THEME +'/style.scss',
    '~/assets/scss/themes-'+ process.env.DEFAULT_THEME +'/colors.scss'
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    { src: '~/plugins/global.js' },
    { src: '~/plugins/custom-filter.js' },
    { src: '~/plugins/vue-disqus'},
    // { src: '~/plugins/vue-mce'},
    { src: '~/plugins/vue-google-charts' },
    { src: '~/plugins/vue-spinkit.js', ssr: false },
    { src: '~/plugins/vue-luxon.js', ssr: false },
    { src: '~/plugins/vue-textarea-autosize.js', ssr: false },
    { src: '~/plugins/vue-datetime.js', ssr: false },
    { src: '~/plugins/vue-croppie.js', ssr: false },
    { src: '~/plugins/vue-full-calendar.js', ssr: false },
    { src: '~/plugins/vue-swal' },
    { src: '~/plugins/vue-editor', ssr: false },
    { src: '~/plugins/vue-tinymce', ssr: false },
    { src: '~/plugins/vue-carousel', ssr: false },
    { src: '~/plugins/vue-tags-input', ssr: false },
    { src: '~/plugins/vue-marqee-text-component', ssr: false },
    { src: '~/plugins/date-range-picker', ssr: false },
    { src: '~/plugins/vue-star-rating', ssr: false },    
    { src: '~/plugins/vue-awesome-swiper', mode: 'client' },
    { src: '~/plugins/cube-vue-image-lazy.js' },
    { src: '~/plugins/vue-clazy-load.js' },
    // { src: '~/plugins/crisp-chat.js', mode: 'client'},
    { src: '~/plugins/vue-html-to-paper.js', mode: 'client'},
    // { src: '~/plugins/lazysizes.client.js' },
    // { src: '~plugins/ga.js', mode: 'client' },
    // { src: '~/plugins/vue-fb-customer-chat.js', ssr: false },
    // { src: '~/plugins/videojs-youtube', ssr: false }
  ],
  /*
  ** PWA menifest
  */
  pwa: {
    icon:{
      sizes: [64, 120, 144, 152, 192, 384,512],
    },
    meta: {
      title: 'Boxprint eCommerce Application',
      author: 'Md. Rokibul Hossain | Orangebd',
    },
    manifest: {
      name: process.env.npm_package_name,
      short_name: process.env.npm_package_name,
      lang: 'en',
      description: process.env.npm_package_description,
      background_color: '#ffffff',
      theme_color: '#f16837',
      start_url: process.env.BASE_URL + '?standalone=true'
    },
    workbox: {}
  },
  
  serverMiddleware: {
    '/_logger': '@/server/middleware/logger',
    '/_ipx': '@/server/middleware/ipx'
  },  
  
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
    '@nuxtjs/pwa',
    '@nuxt/image',
    '@nuxtjs/device',
    'nuxt-animejs',
    'nuxt-gsap-module',
    // 'nuxt-vite'
  ],
  gsap: {
    /* module options */
  },
  image: {
    imgix: {
      baseURL: process.env.API_ENDPOINT
    }
  },
  // Add global page transition
  pageTransition: {
    name: 'page',
    mode: 'out-in',
    css: false,

    beforeEnter(el) {
      this.$anime.set(el, {
        opacity: 0
      })
    },

    enter(el, done) {
      this.$anime({
        targets: el,
        opacity: [0, 1],
        duration: 500,
        easing: 'easeInOutSine',
        complete: done
      })
    },

    leave(el, done) {
      this.$anime({
        targets: el,
        opacity: [1, 0],
        duration: 500,
        easing: 'easeInOutSine',
        complete: done
      })
    }
  },
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    '@nuxt/http',
    '@nuxtjs/pwa',
    // '@nuxtjs/auth',
    '@nuxtjs/device',
    '@nuxtjs/dotenv',
    '@nuxtjs/toast',
    'nuxt-izitoast',
    '@nuxtjs/sitemap',
    '@nuxtjs/robots',
    // '@nuxtjs/svg',
    '@nuxtjs/firebase',
    '@nuxtjs/style-resources',    
    ['@nuxtjs/proxy', { proxyHeaders: false }],
    ['vue-scrollto/nuxt', { duration: 300 }],
    // Simple usage
    // '@nuxtjs/component-cache',

    // With options
    ['nuxt-leaflet', { /* module options */ }],
    ['cookie-universal-nuxt', { alias: 'cookiz' }],

    // With options
    ['@nuxtjs/component-cache', {
      max: 10000,
      maxAge: 1000 * 60 * 60
    }]
  ],

  robots: {
    'user-agent': '*',
    'disallow': '/dashboard/'
  },

  styleResources: {
    scss: ['./assets/scss/themes-'+ process.env.DEFAULT_THEME +'/*.scss']
  },

  /**
   * Firebase Configuration
   */
  firebase: {
    config: {
      apiKey: process.env.FC_API_KEY,
      authDomain: process.env.FC_AUTH_DOMAIN,
      projectId: process.env.FC_PROJECT_ID,
      storageBucket: process.env.FC_STORAGE_BUCKET,
      messagingSenderId: process.env.FC_MESSAGING_SENDER_ID,
      appId: process.env.FC_APP_ID,
      measurementId: process.env.FC_MEASUREMENT_ID
    },
    services: {
      // auth: true,
      // firestore: true,
      // functions: true,
      // storage: true,
      // database: true,
      messaging: {
        createServiceWorker: true,
        actions: [
          {
            action: 'randomName',
            url: 'randomUrl'
          }
        ],
        fcmPublicVapidKey: process.env.FCM_PUBLIC_VAPID_KEY // OPTIONAL : Sets vapid key for FCM after initialization
      },
      // performance: true,
      // analytics: true,
      // remoteConfig: true
    }
  },
  
  /**
   * SITEMAP Configuration
   */
  sitemap: {
    hostname: process.env.BASE_URL,
    gzip: true,
    exclude: [
      '/frontend',
      '/dashboard/**'
    ]
  },
  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    baseURL: process.env.API_ENDPOINT,
    proxy: false,
    credentials: false
  },

  http: {
    baseURL: process.env.API_ENDPOINT,
    proxy: true // Can be also an object with default options
  },  

  proxy: {
    '/oauth/': { target: process.env.API_ENDPOINT, pathRewrite: { '^/oauth/': '/oauth/' }, changeOrigin: true },
    '/api/': { target: process.env.API_ENDPOINT, pathRewrite: { '^/api/': '/api/' }, changeOrigin: true }
  },
  toast: {
    position: 'bottom-right',
    duration: 2500
  },
  izitoast: {
    position: 'bottomRight',
    transitionIn: 'bounceInLeft',
    transitionOut: 'fadeOutRight',
    progressBar: false,
    timeout: 2500
  },
  /*
  ** Build configuration
  */
  build: {
    // vendor: [
    //   'vue-swal'
    // ],
    /*
    ** You can extend webpack config here
    */
    // extend (config, ctx) {
    // },
    extend (config, { isDev, isClient, loaders: { vue } }) {
      if (isClient) {
        vue.transformAssetUrls.img = ['data-src', 'src']
        vue.transformAssetUrls.source = ['data-srcset', 'srcset']
      }
    }
  }
}
