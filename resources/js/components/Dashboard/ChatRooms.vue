<template>
	<v-container>
		<v-container fluid>
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
				  height="200px"
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
import {mapActions, mapGetters} from "vuex";
export default{
	data: () => ({
		src: 'https://cdn.vuetifyjs.com/images/cards/road.jpg',
		rating: 3,
		loading: true,
		loaded: false
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
		...mapActions([
                'fetchRooms',
				'joinRoom',
				'leaveRoom',
                ]),
	},
	computed: {
		...mapGetters([
                'rooms',
				'userData'
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