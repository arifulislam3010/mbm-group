// State
export const state = () => ({
    themeDirName: 'themes',
    dashboard: {
        'name': 'Dashboard',
        'prefix': 'dashboard'
    },
    blackOverlayHeader: false,
    blackOverlayFooter: false,
    outh_token_name: 'oauth_token',
    // content type list
    dashboard_featured_list: {
        'Collection Types': {
            1: {
                name: 'Products',
                path: '/dashboard/products',
                route: 'dashboard-products',
                icon: '<i class="fa fa-laptop-medical"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            2: {
                name: 'Categories',
                path: '/dashboard/categories',
                route: 'dashboard-categories',
                icon: '<i class="fa fa-bars"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            // 3: {
            //     name: 'Companies',
            //     path: '/dashboard/companies',
            //     route: 'dashboard-companies',
            //     icon: '<i class="fa fa-hospital"></i>',
            //     position: 'dashboard-left-nav',
            //     onClick: 'page'
            // },
            4: {
                name: 'Media Gallery',
                path: '/dashboard/media-galleries',
                route: 'dashboard-media-galleries',
                icon: '<i class="fa fa-images"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            5: {
                name: 'Media Categories',
                path: '/dashboard/media-categories',
                route: 'dashboard-media-categories',
                icon: '<i class="fa fa-bars"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            6: {
                name: 'Custom Product Designs',
                path: '/dashboard/custom-product-designs',
                route: 'dashboard-custom-product-designs',
                icon: '<i class="fa fa-bars"></i>',
                position: 'dashboard-left-nav',
                onclick: 'page'
            }
        },
        'Components': {
            21: {
                name: 'Product Color Infos',
                path: '/dashboard/product-color-infos',
                route: 'dashboard-product-color-infos',
                icon: '<i class="fa fa-palette"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            // 22: {
            //     name: 'Product Size Infos',
            //     path: '/dashboard/product-size-infos',
            //     route: 'dashboard-product-size-infos',
            //     icon: '<i class="fa fa-bars"></i>',
            //     position: 'dashboard-left-nav',
            //     onClick: 'page'
            // },
            23: {
                name: 'Product Info Types',
                path: '/dashboard/product-info-types',
                route: 'dashboard-product-info-types',
                icon: '<i class="fa fa-book-medical"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            24: {
                name: 'Product Types',
                path: '/dashboard/product-types',
                route: 'dashboard-product-types',
                icon: '<i class="fa fa-pills"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            25: {
                name: 'Product Type Items',
                path: '/dashboard/product-type-items',
                route: 'dashboard-product-type-items',
                icon: '<i class="fa fa-bars"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            26: {
                name: 'Product Price Types',
                path: '/dashboard/product-price-types',
                route: 'dashboard-product-price-types',
                icon: '<i class="fa fa-funnel-dollar"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            27: {
                name: 'Tags',
                path: '/dashboard/tags',
                route: 'dashboard-tags',
                icon: '<i class="fa fa-tags"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            }            
        },
        'GEO Information': {
            101: {
                name: 'Regions',
                path: '/dashboard/regions',
                route: 'dashboard-regions',
                icon: '<i class="fa fa-map-marker-alt"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            102: {
                name: 'Cities',
                path: '/dashboard/cities',
                route: 'dashboard-cities',
                icon: '<i class="fa fa-map-marker-alt"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            103: {
                name: 'Areas',
                path: '/dashboard/areas',
                route: 'dashboard-areas',
                icon: '<i class="fa fa-map-marker-alt"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            }
        },
        'Data Information': {
            31: {
                name: 'Order Infos',
                path: '/dashboard/order-infos',
                route: 'dashboard-order-infos',
                icon: '<i class="fa fa-notes-medical"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            32: {
                name: 'Customer Infos',
                path: '/dashboard/customer-infos',
                route: 'dashboard-customer-infos',
                icon: '<i class="fa fa-hospital-user"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            33: {
                name: 'Delivery Persons',
                path: '/dashboard/delivery-persons',
                route: 'dashboard-delivery-persons',
                icon: '<i class="fa fa-biking"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            }
        },
        'Frontend Setting': {
            41: {
                name: 'Static Pages',
                path: '/dashboard/static-pages',
                route: 'dashboard-static-pages',
                icon: '<i class="fa fa-scroll"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            42: {
                name: 'Promotional Banners',
                path: '/dashboard/promotional-banners',
                route: 'dashboard-promotional-banners',
                icon: '<i class="fa fa-ad"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            43: {
                name: 'Logo Setup',
                path: '/dashboard/logo-setup',
                route: 'dashboard-logo-setup',
                icon: '<i class="fa fa-images"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            // 44: {
            //     name: 'Timeline Setup',
            //     path: '/dashboard/timeline-setup',
            //     route: 'dashboard-timeline-setup',
            //     icon: '<i class="fa fa-stream"></i>',
            //     position: 'dashboard-left-nav',
            //     onClick: 'page'
            // },
            45: {
                name: 'Basic Configuration',
                path: '/dashboard/basic-configuration',
                route: 'dashboard-basic-configuration',
                icon: '<i class="fa fa-cogs"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            }
        },
        'Generals': {
            14: {
                name: 'Admin User Roles',
                path: '/dashboard/admin-user-roles',
                route: 'dashboard-admin-user-roles',
                icon: '<i class="fa fa-list"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            },
            15: {
                name: 'Admin Users',
                path: '/dashboard/admin-users',
                route: 'dashboard-admin-users',
                icon: '<i class="fa fa-users"></i>',
                position: 'dashboard-left-nav',
                onClick: 'page'
            }
        }
    },

    // Admin User Role Feature ID
    admin_user_role_feature_id: 14,

    // user dashboard list
    user_dashboard_nav_list: {
        1: {
            name: 'Manage Account',
            path: '/user/manage-account',
            page: 'manage-account',
            allow: true,
            icon: '<i class="far fa-user"></i>'
        },
        2: {
            name: 'Manage Address',
            path: '/user/manage-address',
            page: 'manage-address',
            allow: false,
            allow_user_type: 3, // user_type_id [1 => admin,2 => delivery person,3 => customer]
            icon: '<i class="far fa-address-book"></i>'
        },
        3: {
            name: 'Manage Orders',
            path: '/user/manage-orders',
            page: 'manage-orders',
            allow: false,
            allow_user_type: 2, // user_type_id [1 => admin,2 => delivery person,3 => customer]
            icon: '<i class="fa fa-shopping-basket"></i>'
        },
        4: {
            name: 'My Orders',
            path: '/user/my-orders',
            page: 'my-orders',
            allow: false,
            allow_user_type: 3, // user_type_id [1 => admin,2 => delivery person,3 => customer]
            icon: '<i class="fa fa-shopping-basket"></i>'
        },
        5: {
            name: 'Manage Exact Location',
            path: '/user/manage-exact-location',
            page: 'manage-exact-location',
            allow: false,
            allow_user_type: 2, // user_type_id [1 => admin,2 => delivery person,3 => customer]
            icon: '<i class="far fa-address-book"></i>'
        },
        // 6: {
        //     name: 'My Prescriptions',
        //     path: '/user/my-prescriptions',
        //     page: 'my-prescriptions',
        //     allow: false,
        //     allow_user_type: 3, // user_type_id [1 => admin,2 => delivery person,3 => customer]
        //     icon: '<i class="fa fa-prescription"></i>'
        // },
        7: {
            name: 'My reviews',
            path: '/user/my-reviews',
            page: 'my-reviews',
            allow: false,
            allow_user_type: 2, // user_type_id [1 => admin,2 => delivery person,3 => customer]
            icon: '<i class="far fa-star"></i>'
        },
        8: {
            name: 'Change Password',
            path: '/user/change-password',
            page: 'change-password',
            allow: true,
            icon: '<i class="fa fa-exchange-alt"></i>'
        }
    },

    // site basic config data
    site_basic_config_data: {},

    // site logo info data
    logo_info_data: {},

    // gender list
    gender_list: {
        1: 'Male',
        2: 'Female',
        3: 'Common',
        4: 'Not Mentioned'
    },

    // product size type list
    product_size_types: {
        1: 'EU',
        2: 'UK',
        3: 'US',
        4: 'Int',
        5: 'Waist',
        6: 'Suit Only',
        7: 'Full Look',
        8: 'Other'
    },

    // product mode type list
    product_mode_types: {
        1: 'New',
        2: 'Featured',
        3: 'Hot',
        4: 'Special'
    },

    // display on list
    display_on_list: {
        1: 'Top Header Section',
        2: 'Header Navigation Section',
        3: 'Footer Section'
    },

    // order timeline list
    order_timeline_list: {
        1: 'We are getting your order',
        2: 'Order Placed',
        3: 'Production Ongoing',
        4: 'Processed',
        5: 'Ready to Ship',
        6: 'Going for Delivery',        
        7: 'Order has been delivered'
    },

    // order status list
    order_status_list: {
        1: 'Order Placed',
        2: 'Production Ongoing',
        3: 'Processed',
        4: 'Ready to Ship',
        5: 'Going for Delivery',
        6: 'Delivered'
    },

    // order status images
    order_status_images: {},

    // running order info
    running_order_info: [],

    // order delivery review info
    order_delivery_review_pending_info: [],

    // shipping address labels
    shipping_addr_labels: {
        1: {
            title: 'Home',
            icon: '<i class="fa fa-home"></i>'
        },
        2: {
            title: 'Office',
            icon: '<i class="fa fa-briefcase"></i>'
        }
    },

    // footer navigation list
    footer_nav_list: {
        1: {
            name: 'About Us',
            path: '/about-us',
            route: 'about-us',
            icon: '<i class="fa fa-info-circle"></i>'
        },
        2: {
            name: 'Terms &amp; Conditions',
            path: '/terms-and-conditions',
            route: 'terms-and-conditions',
            icon: '<i class="fa fa-info-circle"></i>'
        },
        3: {
            name: 'Privacy Policy',
            path: '/privacy-policy',
            route: 'privacy-policy',
            icon: '<i class="fa fa-info-circle"></i>'
        }
    },

    // label setup
    order_init_status_label: 'Order Submitted',

    // User type setup
    admin_user_type_id: 1,
    delivery_user_type_id: 2,
    customer_user_type_id: 3,

    // Setup paths
    user_dashboard_path: 'user',
    product_dtl_path: 'products',
    checkout_page_path: 'checkout',
    static_page_path: 'page',
    search_page_path: 'search',

    // Global variables
    currency_info: {
        title: 'Tk',
        // symbol: '৳',
        symbol: 'Tk',
        symbol_pos: 'left'
    },

    // Banner display types
    banner_display_types: {
        1: 'On body',
        2: 'Popup'
    },
    banner_schedule_types: {
        0: 'Always Show',
        1: 'On Schedule'
    },

    // Checkout page variables
    vat: 0, // percentage
    delivery_fee_default: 100,

    // Media Gallery Images Path
    media_gallery_img_path: '',

    // Get role access
    user_role_access: {},

    // Get feature access list
    feature_access_list: '',

    // form action status
    add_new_status: false,
    form_submit_status: false,
    auth_form_open_status: false,

    // current location
    cur_lat: 23.727929,
    cur_lng: 90.410545,

    // order timeline stream status
    order_timeline_sse_status: false,

    // search data info
    search_keyword: '',

    // star rating out of
    rating_out_off: 5,

    // product custom design popup view
    custom_product_design_on: false,
    sel_custom_product_cat_id: '',
    sel_custom_product_design: {},
    content_builder_media_gallery: false,
    google_map_allow: false
  })

  export const mutations = {
    BLACK_OVERLAY_HEADER (state, value){
        // console.log('Overlay', value)
        state.blackOverlayHeader = value
    },
    BLACK_OVERLAY_FOOTER (state, value){
        console.log('Overlay', value)
        state.blackOverlayFooter = value
    },
    // Basic config data
    BASIC_CONFIG_DATA (state, data) {
        state.site_basic_config_data = data.site_basic_config_data
        state.gender_list = data.gender_list
        state.display_on_list = data.display_on_list
        state.order_timeline_list = data.order_timeline_list
        state.order_status_list = data.order_status_list
        state.order_status_images = data.order_status_images
        state.order_init_status_label = data.order_init_status_label
        state.admin_user_type_id = data.admin_user_type_id
        state.delivery_user_type_id = data.delivery_user_type_id
        state.customer_user_type_id = data.customer_user_type_id
        state.currency_info = data.currency_info
        state.banner_display_types = data.banner_display_types
        state.banner_schedule_types = data.banner_schedule_types
        state.media_gallery_img_path = data.media_gallery_img_path
        state.vat = data.vat
        state.delivery_fee_default = data.delivery_fee_default
    },
    LOGO_INFO_DATA (state, data) {
        state.logo_info_data = data
    },
    // Set
    CUR_LAT_LNG (state, data) {
        // console.log('Get Latlng', data)
        state.cur_lat = data.latitude
        state.cur_lng = data.longitude
    },
    // Google map status
    GOOGLE_MAP_STATUS (state, status) {
        state.google_map_allow = status
    },
    // Running Order Status
    RUNNING_ORDER_STATUS (state, data) {
        state.running_order_info = data
    },
    // Order Delivery Review Status
    ORDER_DELIVERY_REVIEW_PENDING_STATUS (state, data) {
        state.order_delivery_review_pending_info = data
    },
    // Get user data
    USER_ROLE_ACCESS (state, data) {
      state.user_role_access = data
    },
    // Get feature access list by role list
    FEATURE_ACCESS_LIST (state) {
      let arr = {};
      /*for (var access_id in state.user_role_access) {
          arr[access_id] = state.dashboard_featured_list[access_id];
      }*/
      // console.log('Get user role access', state.user_role_access)
      state.user_role_access.forEach(v => {
          arr[v.feature_id] = v
      });

      state.feature_access_list = arr
    },
    // Get form action status
    ADD_NEW_STATUS (state, status) {
        state.add_new_status = status
    },
    FORM_SUBMIT_STATUS (state, status) {
        state.form_submit_status = status
    },
    SERACH_STATUS(state, data) {
        state.search_keyword = data
    },
    AUTH_FORM_OPEN_STATUS(state, status) {
        state.auth_form_open_status = status
    },
    ORDER_TIMELINE_SSE_REQ_STATUS(state, status) {
        state.order_timeline_sse_status = status
    },
    CUSTOM_PRODUCT_DESIGN_ON(state, status){
        state.custom_product_design_on = status
    },
    SEL_CUSTOM_PRODUCT_DESIGN(state, data){
        state.sel_custom_product_design = data
    },
    SEL_CUSTOM_PRODUCT_CAT_ID(state, id){
        state.sel_custom_product_cat_id = id
    },
    CONTENT_BUILDER_MEDIA_GALLERY(state, status){
        state.content_builder_media_gallery = status
    }
  }

  // Actions
  export const actions = {
    // eslint-disable-next-line require-await
    // async nuxtServerInit ({ dispatch }) {
    //   // eslint-disable-next-line no-console
    //   // console.log('Testing')
    //   const res = dispatch('oauth_token/GET_TOKEN_INFO')
    //   return res
    // }
    async GET_BASIC_CONFIG_INFO ({ commit }, getTokenType) {
        // eslint-disable-next-line no-console
        // console.log('Basic configuration info data request', getTokenType)
        let headerObj = {
            headers: {
                'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getTokenType.access_token
            }
        }
        // console.log('header object', headerObj)
        try {
            // let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));
            const { data } = await this.$axios.get('/api/basic-config', headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get basic config data', data.data.site_basic_config_data)

            commit('BASIC_CONFIG_DATA', data.data)
        } catch (err) {
            // eslint-disable-next-line no-console
            // console.log('Error', err)
        }
    },
    async GET_LOGO_INFO ({ commit }, getTokenType) {
        // eslint-disable-next-line no-console
        // console.log('Basic configuration info data request', getTokenType)
        let headerObj = {
            headers: {
                'Authorization': getTokenType.token_type + ' ' + getTokenType.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getTokenType.access_token
            }
        }
        // console.log('header object', headerObj)
        try {
            // let getTokenType = JSON.parse(localStorage.getItem('oauth_token'));
            const { data } = await this.$axios.get('/api/logo-info', headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)

            commit('LOGO_INFO_DATA', data.data)
        } catch (err) {
            // eslint-disable-next-line no-console
            // console.log('Error', err)
        }
    },
  }
