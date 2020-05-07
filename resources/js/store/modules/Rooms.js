import Axios from "axios";

const state = () => ({
    rooms: {},
})

const getters = {
    rooms(state){
        return state.rooms;
    },
	
	members(state){
		return state.members;
	}
}

const mutations = {
    setRooms (state, rooms) {
        state.rooms = rooms;
    },
	
	setMembers (state, members){
		state.members = members;
	},
	
	joinRoom (state, room_id){
		let rooms = state.rooms;
		rooms.forEach(room => {
			if(room.id == room_id){
				room.joined = true;
			}
		})
		state.rooms = rooms;
	},
	
	leaveRoom (state, room_id){
		let rooms = state.rooms;
		rooms.forEach(room => {
			if(room.id == room_id){
				room.joined = false;
			}
		})
		state.rooms = rooms;
	}
}

const actions = {
    async fetchRooms ({ commit, state, rootState }) {
        /* if (Object.keys(state.avatars).length) {
            return state.avatars
        } */
			return new Promise((resolve, reject) => {
				Axios.post("/api/fetchRooms").then(response => {
						let rooms = response.data;
						let user_id = rootState.User.user.id;
						rooms.forEach(elem => {
							elem.joined = false;
							elem.admin = false;
							elem.members.forEach(member => {
								if(member.user_id == user_id){
									elem.joined = true;
								}
								if(elem.admin_id == user_id){
									elem.admin = true;
								}
							})
						})
						commit('setRooms', response.data);
						resolve("success");          
				}, error => {
					// http failed, let the calling function know that action did not work out
					reject(error);
				});
			})
    },
	
	async joinRoom({commit, state, dispatch}, room_id ){
		return new Promise((resolve, reject) => {
			Axios.post("/api/joinRoom", {room_id}).then(response => {
				commit('joinRoom', room_id);
				resolve("success"); 
			}, error => {
				// http failed, let the calling function know that action did not work out
					reject(error);
			})
		})
	},
	
	async leaveRoom({commit, state, dispatch}, room_id ){
		
		return new Promise((resolve, reject) => {
			Axios.post("/api/leaveRoom", {room_id}).then(response => {
				commit('leaveRoom', room_id);
				resolve("success"); 
			}, error => {
				// http failed, let the calling function know that action did not work out
					reject(error);
			})
		})
	}
	
	/* async fetchMembers ({commit, state}){
		return new Promise((resolve, reject) => {
			Axios.post(")
		})
	} */
}

export default {
    state,
    getters,
    actions,
    mutations,
  }