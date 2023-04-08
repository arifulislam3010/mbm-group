// State
export const state = () => ({    
    // seeker basic resume info
    data: [],
    get_data: {},
    pagination_data: [],
    msg: ''
})

export const mutations = {
    // Add data
    ADD_DATA (state, data) {
        state.data = data.data
        state.pagination_data = data.meta
    },
    // GET DATA
    GET_DATA (state, data) {
        state.get_data = data
    },
    // Add error message
    ERR_MSG (state, data) {
        state.msg = data
    }
}

// Actions
export const actions = {
    async GET_DATA ({ commit }, request) {
        // eslint-disable-next-line no-console
        console.log('Product type info names data request')      
        try {
            const { data } = await this.$axios.get('/api/product-type-info-name/load' + (request['limit']>0?'/' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })
            // eslint-disable-next-line no-console
            console.log('Get data', data)
            
            if(data.data.length) {
                commit('GET_DATA', data.data)
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
        console.log('Data submit request')
        try {
            const { data } = await this.$axios.post('/api/product-type-info-name' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data, {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })

            // const { data } = await this.$axios.post('/api/product-type-info-name' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data)

            // eslint-disable-next-line no-console
            console.log('After submit get data', data)
            
            if(data.status) {
                commit('ADD_DATA', data.data)
            } else {
                commit('ERR_MSG', data.msg)
            }   
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err)
        }
    }
}