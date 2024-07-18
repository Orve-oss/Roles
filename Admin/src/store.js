import { createStore } from 'vuex'

export default createStore({
    state: {
        role: null,
    },
    mutations: {
        setRole(state, role){
            state.role = role;
        },
    },
    actions: {
        setRole({ commit }, role){
            commit('setRole', role);
        },
    },
    getters: {
        role: (state) => state.role,
    },
})
