/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.config.productionTip = false;
/**
 * The following block of code may be used to import any npm packages
 */
import App from "./App.vue";
// Import VueAxios for axios requests
import VueAxios from 'vue-axios';
import axios from 'axios';
Vue.use(VueAxios, axios);

// Import vuetify
import Vuetify from 'vuetify';
Vue.use(Vuetify);

//import chat-scroll
import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)

// Impoert VueBar
import Vuebar from 'vuebar';
Vue.use(Vuebar);

// Import SeetAlert 2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css'; // Light theme
// import '@sweetalert2/theme-dark/dark.css'; // Dark theme
const options = {
    confirmButtonColor: '#41b882',
    cancelButtonColor: '#ff7674',
};
Vue.use(VueSweetalert2, options);

// Import Vee Validaye for form validation 
import * as rules from 'vee-validate/dist/rules';
import { extend, ValidationObserver, ValidationProvider, setInteractionMode } from 'vee-validate';
import { messages } from 'vee-validate/dist/locale/en.json';
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

Object.keys(rules).forEach(rule => {
    extend(rule, {
      ...rules[rule], // copies rule configuration
      message: messages[rule] // assign message
    });
  });


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))



// import AvatarPicker from "./components/User/AvatarPicker.vue";

// Import store from vuex
import {store} from './store';

/**
 * There we will create routes for vue router
 */

 // Middlewares


import router from "./router";


router.beforeEach((to, from, next) => {
    if (!to.meta.middleware) {
        return next();
    }
    const middleware = to.meta.middleware;
    const context = {
        to,
        from,
        next,
        store
    }
    return middleware[0]({
        ...context
    })
    });
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue(Vue.util.extend(
                        {
                            router,
                            vuetify: new Vuetify({
                            }),
                            store,
                        },
                        App
                    )).$mount("#app");
