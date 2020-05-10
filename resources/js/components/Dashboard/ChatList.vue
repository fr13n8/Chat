<template>
<v-content  style="height:100%" class="pa-0 ma-0">
<v-container style="height:100%" fluid class="mx-0" >
        <v-row align="stretch" dense style="height:100%">
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
            <v-col :cols="9" elevation=12>
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
                           <v-btn text icon><v-icon tile>mdi-dots-vertical</v-icon></v-btn>
                    </v-card-actions>
                    </v-card>
                </v-col>
                <v-divider></v-divider>
                    <v-col cols=12 style="height:74%" >
                        <v-container  fluid class="py-0 pt-1 pl-0" style="height:100%">
                        <div class="vuebar-element " v-bar>
                        <div style="max-height:470px;" class="px-1 pt-2 my-0" v-chat-scroll="{always: false, smooth: true, scrollonremoved:true, smoothonremoved: false}">
                                <v-row v-for="(message, index) in messages"
                                                    :key="index"
                                                    @click=""
                                                    class="pa-0 pl-2" style="width:100%;">
                                                    
                                                  
                                    <v-col class="pt-0" cols=1>
                                    <v-skeleton-loader
                                                      :loading="loading"
                                                      transition="fade-transition"
                                                      type="avatar"
                                                      height=40
                                                      width=40
                                        >
                                        <v-avatar max-width=40px size=40>
                                        <v-img
                                        :aspect-ratio="1"
                                        :src="'/images/static/avatars/avatar_' + (message.user.avatar_id) + '.jpg'" 
                                        :alt="message.user.name" ></v-img>
                                        </v-avatar>
                                    </v-skeleton-loader>  
                                        
                                    </v-col>
                                    <v-col class="px-0 pt-0" cols=11>
                                    
                                    <v-skeleton-loader
                                                      :loading="loading"
                                                      type="list-item-two-line"
                                                      width="150"
                                        >
                                        <v-card-text
                                        class="font-weight-medium subtitle-2 pa-0"
                                        :class=" currentRoom.admin_id == message.user.id ? 'red--text':message.user.id != userData.id ? 'white--text': 'purple--text'"
                                        >
                                        <v-icon class="mb-1" v-show="currentRoom.admin_id == message.user.id" size=16> mdi-account-tie </v-icon>
                                        {{message.user.name}} <span style="font-size:10px" class="pl-1 overline white--text font-weight-thin">{{message.created_at}}</span>
                                        </v-card-text>
                                        <v-card color="#282e33" elevation=12 raised style="display:inline-block" class="pa-1 ml-1" max-width="50%">
                                        <v-card-text 
                                        class="font-weight-light pa-0 px-2"
                                        style="max-width:300px; font-size:12px"
                                        v-text=message.message>
                                        </v-card-text>
                                        <v-row v-show="message.photos"  no-gutters>
                                            <v-col :cols="photo.col" v-for=" (photo, index) in message.photos" :key="photo.id">
                                                <v-card class="pa-1" flat outlined><v-img height=100   aspect-ratio :src="photo.path"></v-img></v-card>
                                            </v-col>
                                            
                                        </v-row>
                                        </v-card>
                                        </v-skeleton-loader>  
                                    </v-col>
                                </v-row>
                        </div>
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
                loading: true,
                loaded: false
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
                    message = this.photosSizing(message);
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
                const readyHandler = () => {
                if (document.readyState == 'complete') {
                        this.loading = false;
                        this.loaded = true;
                        document.removeEventListener('readystatechange', readyHandler);
                    }
                  };

              document.addEventListener('readystatechange', readyHandler);
              readyHandler();
        },
        methods: {
            ...mapActions([
                "getCurrentRoom"
            ]),
            fetchMessages() {
                axios.post('/api/messages', {
                    room_id : this.$route.params.id
                }).then(response => {
                    let messages = response.data;
                    messages.forEach(message => {
                        let time = new Date(message.created_at);
                        time = time.toLocaleTimeString('en-GB').slice(0, -3);
                        message.created_at = time;
                        messages = this.photosSizing(message);
                    })
                    this.messages = response.data;
                    console.log(this.messages);
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
            sendTypingEvent() {
                console.log("asdfasf")
                Echo.join('chat.' + this.$route.params.id)
                    .whisper('typing', this.user);
            },
            
            photosSizing(message){
                switch (message.photos.length) {
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                            message.photos.forEach(photo => {
                                photo.col = 12 / message.photos.length;
                            })
                        break;
                        case 5:
                        case 9:
                            message.photos.forEach((photo, id) => {
                                photo.col = id == (message.photos.length - 1) ? 12 : 3 ; 
                            })
                        break;
                        case 6: 
                        case 10:
                            message.photos.forEach((photo, id) => {
                                photo.col = id >= (message.photos.length - 2) ? 6 : 3 ;
                            })
                        break;
                        case 7:
                            message.photos.forEach((photo, id) => {
                                photo.col = id >= (message.photos.length - 3) ? 4 : 3 ; 
                            })
                        break;
                        case 8:
                            message.photos.forEach(photo => {
                                photo.col =  3;
                            })
                        break;
                        default:
                        break;
                    }
                    return message;
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
    .v-skeleton-loader__avatar{
        width: 40px;
        height:40px;
    }

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