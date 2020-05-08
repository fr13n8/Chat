<template>
<v-content class="pa-0 ma-0">
<v-container fluid>
        <v-row dense style="height:620px">
            <v-col cols=3 >
                <v-card class="boards"  elevation=12 style="height:100%">
                <v-list
                    subheader>
                    
                    
                 <v-list-item>
                 <v-list-item-content>
                 <v-text-field
                    placeholder="Search"
                    outlined
                    dense
                  ></v-text-field>
                  </v-list-item-content>
                 </v-list-item>
                    
                <v-list-item
                dense
                    v-for="(user, index) in users" 
                    :key="index"
                    @click=""
                    
                  >
                    <v-list-item-avatar size=64>
                      <v-img :src="'/images/static/avatars/avatar_' + (user.avatar_id) + '.jpg'"></v-img>
                    </v-list-item-avatar>

                    <v-list-item-content>
                      <v-list-item-title v-text="user.name"></v-list-item-title>
                      <v-list-item-subtitle v-text="user.email"></v-list-item-subtitle>
                    </v-list-item-content>

                  </v-list-item>
                </v-list>
                </v-card>
            </v-col>
            <v-col cols=9>
                <v-card class="boards" elevation=12 style="height:100%">
                <v-row no-gutters style="height:100%">
                    <v-col cols=12 style="height:80%">
                        <v-container fluid style="height:100%">
                        <v-list disabled>
                            <v-list-item
                                dense
                                v-for="(message, index) in messages"
                                :key="index"
                                @click=""
                                
                              >
                              
                              <v-list-item-avatar >
                              <v-img :src="'/images/static/avatars/avatar_' + (message.user.avatar_id) + '.jpg'"></v-img>
                            </v-list-item-avatar>
                              <v-list-item-content>
                                  <v-list-item-title v-text="message.user.name + ' ' + message.created_at">
                                  </v-list-item-title>
                                  <v-list-item-subtitle v-text="message.message"></v-list-item-subtitle>
                                </v-list-item-content>
                              </v-list-item>
                        </v-list>
                        </v-container>
                    </v-col>
                    <v-col cols=12 style="height:20%">
                        <v-container fluid style="height:100%">
                        <v-row class="px-2" dense>
                            <v-btn rounded icon >
                                <v-icon color="primary" dark >mdi-folder-account</v-icon>
                            </v-btn>
                            <v-textarea
                              @keyup.enter="sendMessage"
                              name="message"
                              placeholder="Enter your message..."
                              outlined
                              v-model="newMessage"
                              rows="3"
                              no-resize
                            ></v-textarea>
                            <v-btn icon >
                              <v-icon>mdi-send</v-icon>
                            </v-btn>
                        </v-row>
                        </v-container>
                    </v-col>
                </v-row>
                </v-card>
            </v-col>
            
        </v-row>
</v-container>
</v-content>
</template>

<script>
import {mapGetters} from "vuex";
    export default {
        // props:['user'],
        data() {
            return {
                items: [
                    { active: true, title: 'Jason Oner', avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg' },
                    { active: true, title: 'Ranee Carlson', avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg' },
                    { title: 'Cindy Baker', avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg' },
                    { title: 'Ali Connors', avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg' },
                  ],
                messages: [],
                newMessage: '',
                users:[],
                user: {},
                activeUser: false,
                typingTimer: false,
            }
        },
        created() {
            axios.post("/api/auth/me", {} ).then(response => {
                this.user =  response.data;
                
            });
            this.fetchMessages();
            Echo.join('chat.' + this.$route.params.id)
				.listen('MessageSend',({message}) => {
                    console.log(message)
                    console.log("message")
                    this.messages.push(message);
                })
                .here(user => {
                    console.log("here")
                    this.users = user;
                })
                .joining(user => {
                    console.log("joining")
                    this.users.push(user);
                })
                .leaving(user => {
                    console.log("leaving")
                    this.users = this.users.filter(u => u.id != user.id);
                })
                .listenForWhisper('typing', user => {
					console.log(user)
                    this.activeUser = user;
                    if(this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                    this.typingTimer = setTimeout(() => {
                       this.activeUser = false;
                    }, 3000);
                })
        },
        methods: {
            fetchMessages() {
                axios.post('/api/messages', {
                    room_id : this.$route.params.id
                }).then(response => {
                    console.log(response.data);
                    let messages = response.data;
                    messages.forEach(message => {
                        let time = new Date(message.created_at);
                        /* time = time.toLocaleDateString('it-IT').slice(0, -3); */
                        var options = { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric'};
                        time = time.toLocaleDateString('en-GB', options);
                        message.created_at = time;
                    })
                    this.messages = response.data;
                    // console.log(asdf);
                })
            }, 
            sendMessage() {
                this.messages.push({
                        user: this.user,
						message: this.newMessage,
                });
                axios.post('/api/sendMessage', {
                    message: this.newMessage,
                    room_id: this.$route.params.id
                    });
                this.newMessage = '';
            },
            // sendTypingEvent() {
            //     Echo.join('chat')
            //         .whisper('typing', this.user);
            // }
        },
        computed: {
            ...mapGetters([
                // 'userData'
                ]),
        },
        watch:{
            $route (to, from){
                console.log(to)
            }
        },
        beforeRouteLeave (to, from, next) {
            Echo.leave('chat.' + this.$route.params.id);
            next();
        }
    }
</script>

<style scoped>
    li {
        color: black;
    }
    
    .boards {
        border: 1px solid #f0f0f0 !important;
    }
    
</style>