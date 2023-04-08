// State
export const state = () => ({    
    // seeker basic resume info
    data: [],
    sel_data: {},
    pagination_data: [],
    msg: ''
})

export const mutations = {
    // SEL DATA
    SEL_DATA (state, data) {
        state.sel_data = data
    },
    RECONFIG_DATA (state, data) {
        if(data.field=='order_items_info') state.sel_data.order_items_info[data.index].qty = parseInt(data.qty)
        else if(data.field=='discount') state.sel_data.discount = parseFloat(data.value)
        else if(data.field=='delivery_fee') state.sel_data.delivery_fee = parseFloat(data.value)
        else if(data.remove) state.sel_data.order_items_info.splice(data.index,1)

        // calculate total amount
        state.sel_data.total_amount = 0
        for(var i=0; state.sel_data.order_items_info[i]; i++){
            let v = state.sel_data.order_items_info[i]
            state.sel_data.total_amount += (v.price * v.qty)
        }

        // calculate total payment amount
        state.sel_data.total_payable = state.sel_data.total_amount + (state.sel_data.total_amount * state.sel_data.vat_amount)/100 + state.sel_data.delivery_fee - state.sel_data.discount
    },
    // Add data
    ADD_DATA (state, data) {
        state.data = data.data
        state.pagination_data = data.meta
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
        // console.log('Order info data request')      
        try {
            const { data } = await this.$axios.get('/api/generics' + (request['limit']>0?'/' + request['limit']:'') + (request['page']?'?page=' + request['page']:''), {
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
            const { data } = await this.$axios.post('/api/generics' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data, {
                headers: {
                    'Authorization': 'Bearer ' + request.access_token,
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': request.access_token
                }
            })

            // const { data } = await this.$axios.post('/api/generics' + (request['action']=='update'?'/update/' + request['edit_id']:'/store'), request.data)

            // eslint-disable-next-line no-console
            // console.log('After submit get data', data)
            
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