import Axios from "axios";

const state = () => ({
    rooms: {},
	currentRoom: {},
	getRooms: {},
})

const getters = {
    rooms(state){
        return state.getRooms;
    },
		
	currentRoom(state){
		return state.currentRoom;
	},

	joinedRooms(state){
		return state.rooms.filter(room => room.joined);
	},

}

const mutations = {
    setRooms (state, rooms) {
        state.rooms = rooms;
		state.getRooms = rooms.filter(room => !room.admin);
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
	},
	
	currentRoom(state, room){
		state.currentRoom = room;
	},
	
	roomsType(state, type){
		if(type == "all"){
			state.getRooms =  state.rooms.filter(room => !room.admin);
		}
		else if(type == "my"){
			state.getRooms =  state.rooms.filter(room => room.admin);
		}
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
							if(elem.admin_id == user_id){
								elem.admin = true;
							}
							elem.members.forEach(member => {
								if(member.user_id == user_id){
									elem.joined = true;
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
	
	async getCurrentRoom({ state, commit }, room_id){
			let rooms = state.rooms;
			let current = {};
			rooms.forEach(room => {
				if(room.id == room_id){
					current = room;
				}
			})
			commit('currentRoom',current);
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
	},
	
	async addRoom({commit, state, dispatch}, newRoom){
		const config = {
			headers : {
				'Content-type': 'multipart/form-data',
			}
		}
		let formData = new FormData();
        formData.append('photo', newRoom.photo);
		const json = JSON.stringify(newRoom);
		const blob = new Blob([json], {
		  type: 'application/json'
		});
		formData.append('newRoom', json);
		return new Promise((resolve, reject) => {
			Axios.post("/api/addRoom", formData, config).then(response => {
				if(response.data.message == "success"){
					
					resolve({
						message : "success"
					});
				}
				else if(response.data.message == "error"){
					resolve({
						message : "error",
						error : response.data.body
					})
				}
			}, error => {
				reject(error);
			})
		})
	},
	
	async deleteRoom({commit, state, dispatch}, roomId){
		return new Promise((resolve, reject) => {
			Axios.post("/api/deleteRoom", {roomId}).then(response => {
				if(response.data.message == "success"){
					resolve({
						message : "success"
					});
				}
			})
		}, error => {
			reject(error)
		})
	},
	
	async setgsRoom({commit, state, dispatch}, roomId){
		return new Promise((resolve, reject) => {
			let getRoom = state.rooms.filter(room => room.id == roomId );
			resolve(getRoom);
		}, error => {
			reject(error);
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