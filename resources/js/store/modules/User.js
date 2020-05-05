import Axios from "axios";

const getDefaultState = () => {
    return {
        user: {},
        token: '',
        loggedIn: false,
    }
  }

const state = getDefaultState();

const getters = {
    userData(state){
        return state.user;
    },

    userToken(state){
        return state.token;
    },

    auth(state){
        return state.loggedIn;
    }
}

const mutations = {
    updateUser(state, user){
        state.user = user;
    },

    updateToken(state, token){
        state.token = token;
        // state.dispatch("fetchUser");
    },

    loggedIn(state, loggedIn){
        state.loggedIn = loggedIn;
    },

    resetState (state) {
        // Merge rather than replace so we don't lose observers
        // https://github.com/vuejs/vuex/issues/1118
        Object.assign(state, getDefaultState())
      }
}

const actions = {
    async tokenChecker({ commit, state }){
        return new Promise((resolve, reject) => {
                if(state.token){
                    commit('tokenError', true);
                }
                else{
                    commit('tokenError', false);
                }
              resolve();
          })
    },

    async refreshToken({commit, state}){
        let config = {
            headers: {
                Authorization: state.token.original.token_type + " " + state.token.original.access_token,
            }
          }
        await Axios.post("/api/auth/refresh", {}, config ).then(response => {
            commit('updateToken', response.data);
        });
    },

    async login({commit, state, dispatch}, token ){
        commit('updateToken', token);
        await dispatch('fetchUser');
    },

    async fetchUser ({ commit, state, dispatch }) {
        // if(!state.tokenError){
            // if (Object.keys(state.user).length) {
            //     return state.user
            // }
            let config = {
                headers: {
                    Authorization: state.token.original.token_type + " " + state.token.original.access_token,
                }
              }
            await Axios.post("/api/auth/me", {}, config ).then(response => {
                commit('updateUser', response.data);
                commit('loggedIn', true);
            });
        // }
        // else{
        //     // When token was deleted
        // }
    },

    async avatarChange ({ commit, state, dispatch }, avatar) {
        // dispatch('tokenChecker');
        // if(!state.tokenError){
            let config = {
                headers: {
                    Authorization: state.token.original.token_type + " " + state.token.original.access_token,
                }
              }
            await Axios.post("/api/profile/changeAvatar", {
                avatar_id: avatar,
                Authorization: state.token.original.token_type + " " + state.token.original.access_token
            }, config).then(response => {
                if(response.data.message == "refresh"){
                    dispatch('refreshToken');
                }
                else{
                    commit('updateUser', response.data);
                }
            });
        // }
        // else{
        //     // When token was deleted
        // }
    },
    
    async updateUser({commit, state, dispatch}, data){
        return new Promise((resolve, reject) => {
            let config = {
                headers: {
                    Authorization: state.token.original.token_type + " " + state.token.original.access_token,
                }
              }
            Axios.post("/api/profile/updateUser", {
                data: data,
                Authorization: state.token.original.token_type + " " + state.token.original.access_token
            }, config).then(response => {
                switch (response.data.message) {
                    case "password":
                        resolve("password");
                        break;
                    case "name":
                        resolve("name");
                        break;
                    case "success":
                        commit('updateUser', response.data.user);
                        resolve("success");
                        break;
                    default:
                        break;
                }                
            }, error => {
                // http failed, let the calling function know that action did not work out
                reject(error);
            });
        })
    },

    async logOut({commit, state, dispatch}){
        let config = {
            headers: {
                Authorization: state.token.original.token_type + " " + state.token.original.access_token,
            }
          }
        await Axios.post("/api/auth/logout", {}, config).then(response => {
            commit('resetState');
        });
    }
}

export default {
    state,
    getters,
    actions,
    mutations,
  }