// State
export const state = () => ({    
    // seeker basic resume info
    data: [],
    get_data: [],
    get_body_data: [],
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
    // GET BODY DATA
    GET_BODY_DATA (state, data) {
        state.get_body_data = data
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
        let headerObj = {
            headers: {
                'Authorization': 'Bearer ' + request.access_token,
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': request.access_token
            }
        }
        // console.log('Data info data request')      
        try {
            // const { data } = await this.$axios.get('/api/media-categories/load' + (request['limit']>0?'?limit=' + request['limit']:'') + (request['page']?'&page=' + request['page']:''), headerObj)
            const { data } = await this.$axios.get('/api/media-categories/load' + (request['type']?'?type=' + request['type']:''), headerObj)
            // eslint-disable-next-line no-console
            // console.log('Get data', data)
            
            if(data.data.length>0) {
                console.log(request['type'])
                if(request['type']=='display_on_nav') commit('GET_DATA', data.data)
                else if(request['type']=='display_on_body') commit('GET_BODY_DATA', data.data)
            } else {
                if(request['type']=='display_on_nav') commit('GET_DATA', [])
                else if(request['type']=='display_on_body') commit('GET_BODY_DATA', [])
            }  
        } catch (err) {
            // eslint-disable-next-line no-console
            console.log('Error', err.message)
        }
    },

    // eslint-disable-next-line require-await
    async DATA_SUBMIT ({ commit }, request) {
        // eslint-disable-next-line no-console
        // console.log('Data submit request')
        try {
            const { data } = await this.$axios.post('/api/media-categories' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data, {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })

            // const { data } = await this.$axios.post('/api/media-categories' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data)

            // eslint-disable-next-line no-console
            // console.log('After submit get data', data)
            
            if(data.status) {
                commit('ERR_MSG', '')
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