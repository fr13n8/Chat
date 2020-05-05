<template>
      <v-container
        class="fill-height"
        fluid
      >
        <v-row
          align="center"
          justify="center"
        >
          <v-col 
            cols = "12"
            md = "8"
            sm = "10"
            xs="10"
            >
            <v-alert
              :color="alert.color"
              v-model="alert.status"
              dark
              dense
              outlined
              dismissible
              transition="scale-transition"
            >
              <v-card-text class="py-2 text-center">
                <strong v-for="message in alert.messages" :key="message">{{message}}! </strong>
              </v-card-text>
            </v-alert>
          </v-col>
          <v-col
            cols="12"
            lg="6"
            md="8"
            sm="10"
            xs="10"
          >
            <v-card height="300px"
                    class="elevation-12"
                    outlined
                    flat
                    >
              <v-card
                tile
                color="primary"
                dark
                flat
                class="d-flex justify-center"
              >
                <v-card-title>SignIn Form</v-card-title>
              </v-card>
              <v-form v-model="rules.isFormValid" ref="signIn">
              <v-card-text>
                  <v-text-field
                    label="Email"
                    name="email"
                    prepend-icon="mdi-at"
                    type="text"
                    v-model="user.email"
                    :rules="rules.email"
                  />

                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock"
                    type="password"
                    v-model="user.password"
                    :rules="rules.password"
                  />
              </v-card-text>
              <v-card-actions height="200px" class='d-flex justify-center align-content-center'>
                <v-btn
                  :disabled="!rules.isFormValid"
                  color="primary"
                  @click="userSignIn()"
                  >Sign IN</v-btn>
              </v-card-actions>
              <v-card v-if="loader.status">
                <v-progress-linear
                indeterminate
                v-model="loader.valueDeterminate"
                color="deep-purple accent-4"
              ></v-progress-linear>
              </v-card>
              </v-form>
            </v-card>
            <v-card-text class="py-2 flex-wrap  justify-space-between d-flex">
              <router-link class="float-left" to="/signUp"><strong>I don't have an account.</strong></router-link>
              <router-link class="float-right" to="/forgotPassword"><strong>Forgot Password.</strong></router-link>
            </v-card-text>
          </v-col>
        </v-row>
      </v-container>
</template>

<script>
import {mapMutations, mapActions} from "vuex";
  export default {
    data () {
      return {
        loader: {
          valueDeterminate: 0,
          status: false
        },

        alert: {
          status: false,
          message: '',
          color: ''
        },

        rules: {
          email: [
                    value => !!value || 'Required.',
                    // value => (value || '').length <= 20 || 'Max 20 characters.',
                    value => {
                      const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                      return pattern.test(value) || 'Invalid e-mail.'
                    },
                  ],
          password: [
                      value => !!value || 'Required.',
                      value => {
                        const pattern = /^[0-9a-zA-Z]+$/
                        return pattern.test(value) || 'Allowed only latin characters and numbers.'
                      },
                      value => (value || '').length >= 5 || 'Min 5 characters'
                    ],
          isFormValid: false
        },

        user: {
          email: '',
          password: '',
        }
      }
    },

    props: {
      source: String,
    },

    computed: {
      // comparePasswords() {
      //   return this.user.password !== this.user.confirmPassword ? 'Passwords do not match.' : true;
      // }
    },

    created () {
    //   this.$vuetify.theme.dark = true
    },

    methods: {
      userSignIn: function() {
        let alert = this.alert;
        let userData = this.user;
        let form  = this.$refs.signIn;
        this.loader.status = true;
        for (const value in userData) {
                    if (userData.hasOwnProperty(value)) {
                      userData[value] = userData[value].trim();
                    }
                  }
        this.axios.post("/api/auth/login", userData).then(response => {
                console.log(response.data);
                alert.messages = [];
                if(response.data.code == 1){
                  alert.status = false;
                  alert.messages = [];
                  // for (const key in response.data.message) {
                  //   if (response.data.message.hasOwnProperty(key)) {
                  //     const message = response.data.message[key];
                  //     alert.messages.push(message[0]);
                  //   }
                  // }
                  // alert.color = 'green';
                  // form.reset();
                  // this.$router.push(`${response.data.path}`);
                  this.login(response.data.message.token).then(() => {
                    window.location.href = `${response.data.message.path}`;
                  });
                  
                }
                else if(response.data.code == 0){
                  this.loader.status = false;
                  alert.status = true;
                  for (const key in response.data.message) {
                    if (response.data.message.hasOwnProperty(key)) {
                      const message = response.data.message[key];
                      alert.messages.push(message[0]);
                    }
                  }
                  alert.color = 'red';
                }
            })
      },
      // ...mapMutations(["updateToken"]),
      ...mapActions([
                'login',
                ]),
    },

    watch: {
      status (val) {
        if (!val) return;
      }
    }
  }
</script>