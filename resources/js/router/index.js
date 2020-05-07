import Vue from 'vue'
import Router from 'vue-router';
Vue.use(Router);

import App from "../App.vue";
import SignUp from "../components/User/SignUp.vue";
import SignIn from "../components/User/SignIn.vue";
import DashBoard from "../components/Main.vue";
import ForgotPassword from "../components/User/ForgotPassword.vue";
import ResetPassword from "../components/User/ResetPassword.vue";
import Profile from "../components/User/Profile.vue";
import ChatsList from "../components/Dashboard/ChatList.vue";
import ChatRooms from "../components/Dashboard/ChatRooms.vue";

import guest from './middleware/guest.js';
import auth from './middleware/auth.js';

export default new Router({
    routes : [
        {
            path: '/signUp',
            component : SignUp,
            name: 'SignUp',
            meta: {
                middleware: [
                    guest
                ]
            }
        },
        {
            path: '/',
            component : App,
            name: 'App'
        },
        {
            path: '/signIn',
            component : SignIn,
            name: 'SignIn',
            meta: {
                middleware: [
                    guest
                ]
            }
        },
        {
            path: '/forgotPassword',
            component : ForgotPassword,
            name: 'ForgotPassword'
        },
        {
            path: '/resetPassword/:hash/:id',
            component : ResetPassword,
            name: 'ResetPassword'
        },
        {
            path: '/dashBoard',
            component : DashBoard,
            name: 'DashBoard',
            children: [
                {
                    path: 'profile/settings',
                    component : Profile,
                    name: 'Profile'
                },
                {
                    path: 'room/id:id',
                    component : ChatsList,
                    name: 'Chats' 
                },
				{
					path: 'chatRooms',
					component: ChatRooms,
					name: 'ChatRooms'
				}
            ],
            meta: {
                middleware: [
                    auth
                ]
            },
        },
    ],
    mode : "history"
});
