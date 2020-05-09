<template>
<v-content class="pa-0 ma-0">
<v-container fluid>
        <v-row dense style="height:650px">
            <v-col cols=2 >
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
                    <v-list-item-avatar size=38>
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
            <v-col :cols="messagesBoxCol" elevation=12>
                <v-card class="boards" elevation=12 style="height:100%">
                <v-row no-gutters style="height:100%">
                <v-col cols=12 style="height:6%; pa-0">
                    <v-card class="messageBoxHeader" tile style="height:100%; background-color:#282E33">
                    <v-card-actions class="pa-0">
                    <div class="pl-2 darken-2 text-center ">
                        <span class="white--text title font-weight-bold">{{currentRoom.name}}</span>
                      </div>
                      <div class="pl-2 darken-2 text-bottom pa-0 pt-1" elevation=12>
                        <span class="white--text caption font-weight-thin">{{currentRoom.members.length}} members</span>
                      </div>
                    <v-spacer></v-spacer>
                    <v-btn @click="toggleBar" text icon><v-icon :color="toggleIconColor" tile>mdi-application</v-icon></v-btn>
                              
                           <v-btn text icon><v-icon tile>mdi-dots-vertical</v-icon></v-btn>
                    </v-card-actions>
                    </v-card>
                </v-col>
                <v-divider></v-divider>
                    <v-col cols=12 style="height:74%" >
                        <v-container  fluid class="py-0 pt-1" style="height:100%">
                        <div class="vuebar-element messages" v-bar>
                        <ul style="max-height:450px" class="pa-0 my-0" v-chat-scroll="{smooth: true, notSmoothOnInit: true}">
                            <li v-for="(message, index) in messages"
                                                    :key="index"
                                                    @click="" 
                                                    :class="userData.id == message.user.id? 'replies': 'sent'">
                                <img elevation=12 :src="'/images/static/avatars/avatar_' + (message.user.avatar_id) + '.jpg'" :alt="message.user.name" />
                                <p>{{message.message}}
                                </p>
                                
                            </li>
                        </ul>
                        </div>
                        </v-container>
                    </v-col>
                    <v-col cols=12 style="height:20%">
                        <v-container fluid style="height:100%">
                        <v-row class="px-2" dense>
                            <v-btn class="mr-1" rounded icon >
                                <v-icon color="primary" dark >mdi-folder-account</v-icon>
                            </v-btn>
                            <v-textarea
                            background-color="#282E33"
                              @keyup.enter="sendMessage"
                              @keydown="sendTypingEvent"
                              name="message"
                              placeholder="Enter your message..."
                              outlined
                              v-model="newMessage"
                              rows="3"
                              no-resize
                            ></v-textarea>
                            <v-btn class="ml-1" rounded icon  @click="sendMessage">
                              <v-icon>mdi-send</v-icon>
                            </v-btn>
                        </v-row>
                        </v-container>
                    </v-col>
                </v-row>
                </v-card>
            </v-col>
            <v-col v-show="barShow" cols=2>
                <v-card class="boards" elevation=12 style="height:100%">
                </v-card>
            </v-col>
            
        </v-row>
</v-container>
</v-content>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
    export default {
        // props:['user'],
        data() {
            return {
                
                toggleIconColor: "white",
                date: {
                    options : { 
                            month: 'short', 
                            day: 'numeric', 
                            hour: 'numeric', 
                            minute: 'numeric'
                        }
                },
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
                barShow: false,
                messagesBoxCol: 10
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
                    let time = new Date(message.created_at);
                    time = time.toLocaleTimeString('en-GB').slice(0, -3);
                    message.created_at = time;
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
                });
                
                this.getCurrentRoom(this.$route.params.id);
        },
        methods: {
            ...mapActions([
                "getCurrentRoom"
            ]),
            fetchMessages() {
                axios.post('/api/messages', {
                    room_id : this.$route.params.id
                }).then(response => {
                    console.log(response.data);
                    let messages = response.data;
                    messages.forEach(message => {
                        let time = new Date(message.created_at);
                        time = time.toLocaleTimeString('en-GB').slice(0, -3);
                        message.created_at = time;
                    })
                    this.messages = response.data;
                    // console.log(asdf);
                })
            }, 
            sendMessage() {
                if(this.newMessage.trim().length > 0){
                    let date = new Date();
                    this.messages.push({
                            user: this.user,
                            message: this.newMessage,
                            created_at: date.toLocaleTimeString('en-GB',).slice(0, -3)
                    });
                    axios.post('/api/sendMessage', {
                        message: this.newMessage,
                        room_id: this.$route.params.id
                        });
                    this.newMessage = '';
                }
            },
            toggleBar() {
                this.barShow = !this.barShow;
                this.messagesBoxCol = this.barShow ? this.messagesBoxCol - 2 : this.messagesBoxCol + 2;
                this.toggleIconColor = this.barShow ? "blue" : "white";
            },
            sendTypingEvent() {
                console.log("asdfasf")
                Echo.join('chat.' + this.$route.params.id)
                    .whisper('typing', this.user);
            }
        },
        computed: {
            ...mapGetters([
                "userData",
                "currentRoom"
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

<style >


    li {
        color: black;
    }
    
    .boards {
        border: 2px solid #f0f0f0 !important;
    }
    
    .messageBoxHeader{
        border-bottom: 1px solid #f0f0f0 !important;
    }
    
    .v-btn--icon:focus{
        outline: none;
    }
    
   /*  .v-list {
      overflow-y: auto;
    } */
    .vuebar-element {
      height: 100%;
      width: 100%;
    }
    
    .vb > .vb-dragger {
        z-index: 1;
        width: 12px;
        right: 0;
    }

    .vb > .vb-dragger > .vb-dragger-styler {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: rotate3d(0,0,0,0);
        transform: rotate3d(0,0,0,0);
        -webkit-transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        background-color: rgba(48, 121, 244,.1);
        margin: 5px 5px 5px 0;
        border-radius: 20px;
        height: calc(100% - 10px);
        display: block;
    }

    .vb.vb-scrolling-phantom > .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.3);
    }

    .vb > .vb-dragger:hover > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging > .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging-phantom > .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
    }
    
    .messages ul li {
  display: inline-block;
  clear: both;
  float: left;
  margin: 8px 5px 0px;
  width: calc(100% - 25px);
  font-size: 0.9em;
}

.messages ul li.sent img {
  margin: 6px 8px 0 0;
}
.messages ul li.sent p {
  background: #435f7a;
  color: #f5f5f5;
  float:left;
}
.messages ul li.replies img {
  float: right;
  margin: 6px 0 0 8px;
}
.messages ul li.replies p {
  background: #f5f5f5;
  float: right;
}
.messages ul li img {
  width: 28px;
  border-radius: 50%;
  float: left;
}
.messages ul li p {
  display: inline-block;
  padding: 8px 15px;
  border-radius: 20px;
  max-width: 225px;
  line-height: 130%;
}

.msg_time_send{
		position: relative;
		left: -40px;
		bottom: -35px;
		color: rgba(255,255,255,0.5);
		font-size: 10px;
	}
.msg_time{
		position: relative;
		left: 40px;
        float:right;
		bottom: -35px;
		color: rgba(255,255,255,0.5);
		font-size: 10px;
	}
</style>