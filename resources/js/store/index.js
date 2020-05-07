import Vue from 'vue';
import Vuex from 'vuex';
import Axios from 'axios';
import User from './modules/User';
import AvatarPicker from './modules/AvatarPicker';
import Rooms from './modules/Rooms';
import createPersistedState from "vuex-persistedstate";
import * as Cookies from "js-cookie";

Vue.use(Vuex);

export const store = new Vuex.Store({
  // state: {},
  // getters: {},
  // mutations: {},
  // actions: {},
  modules: {
    User,
    AvatarPicker,
	Rooms
  },
  plugins: [
    createPersistedState()
  ]
});