<template>
	<v-container>
		<v-container fluid>
		<v-row dens class="mb-5">
			<v-col class="pa-0" cols=12 >
		  	<v-container raised elevation=12  class="pa-1 d-flex justify-space-between "> 
			<v-dialog v-model="dialog" persistent max-width="600px" >
			  <template v-slot:activator="{ on }">
				<v-btn v-on="on" outlined tile color="indigo"><v-icon left>mdi-card-plus</v-icon> Create Room</v-btn>
			  </template>
			  <v-card :loading="creating" outlined >
				<v-card-title>
				  <span class="headline">New Room</span>
				</v-card-title>
				<v-card-text class="pb-0">
				  <v-container>
					<v-row>
					  <v-col cols="12" sm="12" md="12">
						<v-text-field v-model="newRoom.name" label="Name*" required></v-text-field>
					  </v-col>
					  <v-col cols="12" sm="6">
						<v-select
						  :items="['0-17', '18-29', '30-54', '54+']"
						  label="Age*"
						  required
						></v-select>
					  </v-col>
					  <v-col cols="12" sm="6">
						<v-autocomplete
						  :items="['Skiing', 'Ice hockey', 'Soccer', 'Basketball', 'Hockey', 'Reading', 'Writing', 'Coding', 'Basejump']"
						  label="Interests"
						  multiple
						></v-autocomplete>
					  </v-col>
					  <v-col cols=12 sm=12 md=12>
					  		<v-textarea
                              background-color="#282E33"
                              name="message"
                              class="ma-0"
                              placeholder="Room description..."
                              outlined
                              v-model="newRoom.description"
                              rows="3"
                              no-resize
                            ></v-textarea>
					  </v-col>
					  <v-col cols=12>
					  	<v-file-input  accept="image/*" label="Room image"></v-file-input>
					  </v-col>
					</v-row>
				  </v-container>
				  <small>*indicates required field</small>
				</v-card-text>
				<v-card-actions>
				  <v-spacer></v-spacer>
				  <v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
				  <v-btn color="blue darken-1" text @click="createRoom">Save</v-btn>
				</v-card-actions>
			  </v-card>
			</v-dialog>
				
				<v-btn outlined v-show="checker" @click="showRooms('my')" tile color="indigo"><v-icon left>mdi-card-plus</v-icon> My Rooms</v-btn>
				<v-btn outlined v-show="!checker" @click="showRooms('all')" tile color="indigo"><v-icon left>mdi-card-plus</v-icon> All Rooms</v-btn>
			</v-container>
		  </v-col>
		</v-row>
		  <v-row dense>
			<v-col
			  v-for="room in rooms"
			  :key="room.name"
			  :xs=12
			  :sm=12
			  :md="room.flex + 2"
			  :lg="room.flex"
			  :xl="room.flex"
			>
			
				<v-skeleton-loader
				  v-if="loading == true" 
				  :loading="loading"
				  transition="fade-transition"
				  class="mx-auto"
				  type="card"
				></v-skeleton-loader>
			
			  <v-card  v-show="loaded" elevation=12>
				<v-img
				  :src="src"
				  class="white--text align-start pt-1 pl-1"
				  gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
				  height="200"
				>
				<v-rating small dense v-model="room.id"></v-rating>
				<v-card-title class="justify-center mb-5" v-text="room.name"></v-card-title>
				<v-card-text class="text--primary text-center" v-text="room.description"></v-card-text>
				</v-img>
				<v-card-actions>
				  
					<v-btn elevation=6 :disabled="!room.joined" color="primary" :to="'/dashBoard/room/id' + room.id">
							Join a chat
					</v-btn>
					
				<v-spacer></v-spacer>
	
				<v-btn icon v-if="room.admin">
					<v-tooltip bottom>
						<template v-slot:activator="{ on }" >
							<v-icon color="primary" dark v-on="on">mdi-cog</v-icon>
						</template>
						<span >Settings</span>
					 </v-tooltip>
				  </v-btn>
	
				  <v-btn icon>
					<v-tooltip bottom>
						<template v-slot:activator="{ on }" v-if="room.joined">
							<v-icon color="red" @click="leaveRoom(room.id)" dark v-on="on">mdi-account-arrow-left</v-icon>
						</template>
						<template v-slot:activator="{ on }" v-else>
							<v-icon color="green" @click="joinRoom(room.id)" dark v-on="on">mdi-account-arrow-right</v-icon>
						</template>
						<span v-if="room.joined">Left</span>
						<span v-else>Join</span>
					 </v-tooltip>
				  </v-btn>
				</v-card-actions>
			  </v-card>
			</v-col>
		  </v-row>
		</v-container>
	</v-container>
</template>

<script>
import {mapActions, mapGetters, mapMutations} from "vuex";
export default{
	data: () => ({
		dialog: false,
		src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg',
		rating: 3,
		loading: true,
		loaded: false,
		newRoom: {
			name: '',
			description: '',
			photo: '',
		},
		creating: false,
		checker: true,
	}),
	mounted() {
        // this.$vuetify.theme.dark = true;
		this.fetchRooms().then(response => {
			console.log(response);
		}, error => {
			console.error(error)
		})
    },
	methods: {
		joinRoom: function(room_id){
			console.log(room_id)
			this.joinRoom(room_id).then(response => {
				console.log(response);
			}, error => {
				console.error(error);
			});
		},
		leaveRoom: function(room_id){
			console.log(room_id)
			this.leaveRoom(room_id).then(response => {
				console.log(response);
			}, error => {
				console.error(error);
			});
		},
		...mapMutations([
				"roomsType"
    			]),
		...mapActions([
                'fetchRooms',
				'joinRoom',
				'leaveRoom',
				'addRoom'
                ]),
		createRoom(){
			this.creating = true;
			console.log("Create")
			this.addRoom(this.newRoom).then(response => {
				if(response.message == "success"){
					this.creating = false; 
					this.dialog = false;
				}
				else if(response.message == "error"){
					console.log(response.body)
				}
			}, error => {
				console.log(error)
			})
		},
		showRooms(type){
			this.checker = !this.checker;
			this.roomsType(type);
		}
	},
	computed: {
		...mapGetters([
                'rooms',
				'userData',
                ]),
	},
	created(){
		const readyHandler = () => {
		if (document.readyState == 'complete') {
				this.loading = false;
				this.loaded = true;
				document.removeEventListener('readystatechange', readyHandler);
			}
		  };

	  document.addEventListener('readystatechange', readyHandler);
	  readyHandler();
	}
}
</script>

<style scoped>
.v-btn
{
	color: #0060B6;
    text-decoration: none;
}

.v-btn:hover 
{
     text-decoration:none; 
     cursor:pointer;  
}


</style>