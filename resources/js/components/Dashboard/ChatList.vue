<template>
   <div class="row">

       <div class="col-8">
           <div class="card card-default">
               <div class="card-header">Messages</div>
               <div class="card-body p-0">
                   <ul class="list-unstyled" style="height:300px; overflow-y:scroll" >  
                       <li class="p-2" v-for="(message, index) in messages" :key="index">
                           <strong>{{ message.user.name }}</strong>
                           {{ message.message }}
                       </li>
                   </ul>
               </div>

                    <!-- @keydown="sendTypingEvent" -->
               <input
                    @keyup.enter="sendMessage"
                    v-model="newMessage"
                    type="text"
                    name="message"
                    placeholder="Enter your message..."
                    class="form-control">
           </div>
            <span class="text-muted" v-if="activeUser" >{{ activeUser.name }} is typing...</span>
       </div>

        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">Active Users</div>
                <div class="card-body">
                    <ul>
                        <li class="py-2" v-for="(user, index) in users" :key="index">
                            {{ user.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

   </div>
</template>

<script>
import {mapGetters} from "vuex";
    export default {
        // props:['user'],
        data() {
            return {
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
        }
    }
</script>

<style scope>
    li {
        color: black;
    }
</style>