export default function ({ store }) {    
    if (store.state.auth_info.loggedIn) {
        let user_role_access = store.state.auth_info.user_data.role_info.role_accesses;
        // console.log('Get access data', user_role_access);

        store.commit('USER_ROLE_ACCESS',user_role_access);
        // console.log('Access data', store.state.user_role_access);
        store.commit('FEATURE_ACCESS_LIST');
    }
}