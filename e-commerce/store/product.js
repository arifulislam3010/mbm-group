// State
export const state = () => ({    
    // seeker basic resume info
    data: [],
    submit_data: [],
    recommended_data: [],
    selected_data: [],
    hot_data: [],
    all_data: [],
    products_by_category: {},
    all_pagination_data: [],
    pagination_data: [],
    product_dtl_info: '',
    msg: ''
})

export const mutations = {
    // Recommended data
    ADD_RECOMMENDED_DATA (state, data) {
        state.recommended_data = data.data
    },
    // Selected data
    ADD_SELECTED_DATA (state, data) {
        state.selected_data = data.data
    },
    // Hot data
    ADD_HOT_DATA (state, data) {
        state.hot_data = data.data
    },
    // Products by category
    ADD_PRODUCTS_BY_CATEOGRY_DATA (state, data) {
        state.products_by_category[data.cat_id] = data.data
    },
    // all data
    ADD_ALL_DATA (state, data) {
        state.all_data = data.data
        state.all_pagination_data = data.meta
    },
    // get product detail info
    GET_PRODUCT_DTL_INFO (state, data) {
        state.product_dtl_info = data
    },
    // Add data
    ADD_DATA (state, data) {
        state.data = data.data
        state.pagination_data = data.meta
    },
    SUBMIT_DATA (state, data){
        state.submit_data = data
    },
    // Add error message
    ERR_MSG (state, data) {
        state.msg = data
    }
}

// Actions
export const actions = {
    async GET_PRODUCT_INFO ({ commit }, request) {
        // eslint-disable-next-line no-console
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('Product info data request')      
        try {
            const { data } = await this.$axios.get('/api/products/' + request.slug, headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data && data.data) {
                commit('GET_PRODUCT_DTL_INFO', data.data)
            } else {
                commit('GET_PRODUCT_DTL_INFO', '')
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async GET_RECOMENDED_PRODUCTS ({ commit }, request) {
        // eslint-disable-next-line no-console
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('Recommended products data request')      
        try {
            let tag_id_history = '';
            // if(localStorage.getItem('_visited_tag_id_history')) tag_id_history = localStorage.getItem('_visited_tag_id_history')
            // console.log(typeof this.$cookiz.get('_visited_tag_id_history'))
            if(this.$cookiz.get('_visited_tag_id_history')) tag_id_history = this.$cookiz.get('_visited_tag_id_history')
            let reqData = {
                tag_ids: tag_id_history.toString()
            }

            // console.log(reqData)

            const { data } = await this.$axios.post('/api/products/recommended' + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'&page=' + request['page']:''), reqData, headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length) {
                commit('ADD_RECOMMENDED_DATA', data)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err.message)
        }
    },
    async GET_SELECTED_PRODUCTS ({ commit }, request) {
        // eslint-disable-next-line no-console
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('Hot products data request')      
        try {
            const { data } = await this.$axios.get('/api/products/selected' + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length) {
                commit('ADD_SELECTED_DATA', data)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async GET_HOT_PRODUCTS ({ commit }, request) {
        // eslint-disable-next-line no-console
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('Hot products data request')      
        try {
            const { data } = await this.$axios.get('/api/products/hot' + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length) {
                commit('ADD_HOT_DATA', data)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async GET_PRODUCTS_BY_CATEGORY ({ commit }, request) {
        // eslint-disable-next-line no-console
        console.log('Products by category data request')      
        try {
            const { data } = await this.$axios.get('/api/products/by-category/' + request['cat_id'] + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })
            // eslint-disable-next-line no-console
            console.log('Get data', data)
            
            if(data.data.length) {
                let obj = {
                    cat_id:request['cat_id'],
                    data:data.data
                }
                commit('ADD_PRODUCTS_BY_CATEOGRY_DATA', obj)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async GET_ALL_PRODUCTS ({ commit }, request) {
        // eslint-disable-next-line no-console
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('All products data request')      
        try {
            const { data } = await this.$axios.get('/api/products/all' + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'&page=' + request['page']:''), headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length) {
                commit('ADD_ALL_DATA', data)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },
    async GET_DATA ({ commit }, request) {
        // eslint-disable-next-line no-console
        // console.log('Products data request')      
        try {
            const { data } = await this.$axios.get('/api/products' + (request['limit']>0?'/' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length) {
                commit('ADD_DATA', data)
            } else {
                commit('ERR_MSG', data.msg)
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    },

    // eslint-disable-next-line require-await
    async DATA_SUBMIT ({ commit }, request) {
        // eslint-disable-next-line no-console
        // console.log('Data submit request')
        try {
            const { data } = await this.$axios.post('/api/products' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data, {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })

            // const { data } = await this.$axios.post('/api/products' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data)

            // eslint-disable-next-line no-console
            // console.log('After submit get data', data)
            
            // if(data.status) {
                commit('SUBMIT_DATA', data)
            // } else {
            //     commit('ERR_MSG', data.msg)
            // }   
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    }
}