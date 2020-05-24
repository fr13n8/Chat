const state = () => ({
    avatars: {}
})

const getters = {
    avatars(state){
        return state.avatars;
    },
}

const mutations = {
    setAvatars (state, avatars) {
        state.avatars = avatars
    }
}

const actions = {
    fetchAvatars ({ commit, state }) {
        //if (Object.keys(state.avatars).length) {
        //    return state.avatars
        //}

        let avatars = {}
        let files = require.context('./../../../../public/images/static/avatars', true, /\.jpg$/i)
        files.keys().map((key) => {
            let id = key.split('/').pop().split('.')[0].substring(7).toUpperCase()
            avatars[id] = {
                path: key.split('/').pop(),
                id: id
            }
        })

        commit('setAvatars', avatars)
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
  }