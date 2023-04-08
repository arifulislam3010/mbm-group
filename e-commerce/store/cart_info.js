// State
export const state = () => ({    
    cart_items: {},
    total_cart_items: 0,
    total_cart_amount: 0
})

export const mutations = {    
    GET_CART_ITEMS (state) {
        if(localStorage.getItem('cart_items')){
            state.cart_items = JSON.parse(localStorage.getItem('cart_items'))
            
            let total = 0
            for(var index in state.cart_items){
                for(var pi in state.cart_items[index]){
                    total += Object.keys(state.cart_items[index][pi]).length
                }
            }
            state.total_cart_items = total //Object.keys(state.cart_items).length
        }else{
            state.cart_items = {}
            state.total_cart_items = 0
        }
    },
    UPDATE_CART_ITEMS_QUANTITY (state, data) {        
        state.cart_items[data.id][data.ptid][data.ptvid].quantity = data.quantity        

        // Store to localstorage
        localStorage.setItem('cart_items', JSON.stringify(state.cart_items))
    },
    ADD_CART_ITEMS (state, data) {
        console.log(data)
        let sel_price_type_index =  data.sel_price_type_index?data.sel_price_type_index:0
        let sel_price_type_id = data.sel_price_type_id
        let sel_price_list_infos = JSON.parse(data.sel_price_list_infos)[sel_price_type_index]
        let sel_product_type_info_values = JSON.parse(data.sel_product_type_info_values)
        // let sel_custom_product_design = JSON.parse(data.sel_custom_product_design);
        let sel_product_type_infos = ''
        for(var pid in sel_product_type_info_values) {
            sel_product_type_infos = sel_product_type_infos + (sel_product_type_infos!=''?'-':'') + pid + ':' + sel_product_type_info_values[pid]['pid']
        }
        if(state.cart_items[data.id] && state.cart_items[data.id][sel_price_type_id] && state.cart_items[data.id][sel_product_type_infos]){
            state.cart_items[data.id][sel_price_type_id][sel_product_type_infos].quantity = sel_price_list_infos.quantity            
        }else {
            let obj = {
                // company_info: data.company_info,
                product_title: data.product_title,
                product_slug: data.slug,
                product_type_infos: data.sel_product_type_infos,
                product_type_info_values: sel_product_type_info_values,
                unit_price: data.sel_unit_price,
                cart_qty_mode: data.sel_cart_qty_mode,
                min_qty: data.sel_min_qty,
                max_qty: data.sel_max_qty,                
                price: data.sel_unit_price_by_qty,
                price_type_id: sel_price_type_id,
                price_type: data.price_type_infos[sel_price_type_id].type_title,
                product_image: data.product_photo_infos[0]?data.product_photo_infos[0].product_photo_data.content:'',
                // upload_product_image: data.sel_upload_file_content,
                // custom_product_design: sel_custom_product_design,
                quantity: data.quantity?data.quantity:parseInt(sel_price_list_infos.quantity)
            }

            if(!state.cart_items[data.id]) state.cart_items[data.id] = {}
            if(!state.cart_items[data.id][sel_price_type_id]) state.cart_items[data.id][sel_price_type_id] = {}
            state.cart_items[data.id][sel_price_type_id][sel_product_type_infos] = obj

            // if(sel_custom_product_design.price) state.total_cart_amount += parseFloat(sel_custom_product_design.price);
        }

        let total = 0
        for(var index in state.cart_items){
            for(var pi in state.cart_items[index]){
                total += Object.keys(state.cart_items[index][pi]).length
            }
        }
        state.total_cart_items = total //Object.keys(state.cart_items).length

        // Store to localstorage
        localStorage.setItem('cart_items', JSON.stringify(state.cart_items))
    },
    REMOVE_CART_ITEM (state, data) {
        delete state.cart_items[data.id][data.ptid][data.ptvid]

        if(Object.keys(state.cart_items[data.id][data.ptid]).length==0) delete state.cart_items[data.id][[data.ptid]]
        if(Object.keys(state.cart_items[data.id]).length==0) delete state.cart_items[data.id]

        let total = 0
        for(var index in state.cart_items){
            for(var pi in state.cart_items[index]){
                total += Object.keys(state.cart_items[index][pi]).length
            }
        }
        state.total_cart_items = total

        // Store to localstorage
        localStorage.setItem('cart_items', JSON.stringify(state.cart_items))
    },
    TOTAL_CART_AMOUNT (state) {
        let amount = 0
        for(var id in state.cart_items){
            for(var tid in state.cart_items[id]){
                for(var pti in state.cart_items[id][tid]){
                    let item_data = state.cart_items[id][tid][pti]
                    amount += parseFloat(item_data.price) * parseInt(item_data.quantity)

                    // if(item_data.custom_product_design && item_data.custom_product_design.price) amount += parseFloat(item_data.custom_product_design.price)
                }
            }
        }

        state.total_cart_amount = amount
    }
}

// Actions
export const actions = {}  