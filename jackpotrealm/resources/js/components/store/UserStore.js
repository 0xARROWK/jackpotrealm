const state = {
    user: null,
    logged: false,
    pageLoading: true
}

const mutations = {

    ADD_USER: (state, {id, username, email, verified, role, birthdate, color}) => {
        state.user = {
            id,
            username,
            email,
            verified,
            role,
            birthdate,
            color,
            key: 1
        }
    },

    UPDATE_USER: (state, {id, username, email, verified, role, birthdate, color}) => {
        state.user = {
            id,
            username,
            email,
            verified,
            role,
            birthdate,
            color,
            key: 1
        }
    },

    SET_LOGGED: (state, status) => {
        state.logged = status
    },

    SET_PAGE_LOADING: (state, status) => {
        state.pageLoading = status
    }

}

const getters = {

    user: state => state.user,
    logged: state => state.logged,
    pageLoading: state => state.pageLoading

}

const actions = {

    addUser: (store, {id, username, email, verified, role, birthdate, color}) => {
        store.commit('ADD_USER', {id, username, email, verified, role, birthdate, color})
    },

    updateUser: (store, {id, username, email, verified, role, birthdate, color}) => {
        store.commit('UPDATE_USER', {id, username, email, verified, role, birthdate, color})
    },

    setLogged: (store, status) => {
        store.commit('SET_LOGGED', status)
    },

    setPageLoading: (store, status) => {
        store.commit('SET_PAGE_LOADING', status)
    }

}

export default {
    state,
    mutations,
    actions,
    getters,
    strict: true
}
/*export default new Vuex.Store({

    state,
    mutations,
    getters,
    actions,
    strict: true

})*/
