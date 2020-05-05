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
            <v-card height="450px"
                    class="elevation-12"
                    outlined
                    flat
                    absolute
                    >
              <v-card
                tile
                color="primary"
                dark
                flat
                class="d-flex justify-center"
              >
                <v-card-title>SignUp Form</v-card-title>
              </v-card>
              <v-form v-model="rules.isFormValid" ref="signUp">
              <v-card-text>
                  <v-text-field
                    label="name"
                    name="user-name"
                    prepend-icon="mdi-account"
                    type="text"
                    v-model="user.name"
                    :rules="rules.name"
                    :counter="15"
                  />

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

                  <v-text-field
                    label="Confirm Password"
                    name="confirm-password"
                    prepend-icon="mdi-lock"
                    type="password"
                    v-model="user.password_confirmation"
                    :rules="rules.password_confirmation"
                  />
              </v-card-text>
              <v-card-actions height="200px" class='d-flex justify-center align-content-center'>
                <v-btn
                  :disabled="!rules.isFormValid" 
                  color="primary"
                  @click="userSignUp()"
                  >Sign UP</v-btn>
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
                <router-link class="float-left" to="/signIn"><strong>I already have an account.</strong></router-link>
                <router-link class="float-right" to="/forgotPassword"><strong>Forgot Password.</strong></router-link>
              </v-card-text>
          </v-col>
        </v-row>
      </v-container>
</template>

<script>
  export default {
    data () {
      return {
        loader: {
          valueDeterminate: 0,
          status: false
        },
        
        alert: {
          status: false,
          messages: [],
          color: ''
        },

        rules: {
          email: [
                    value => !!value || 'Required.',
                    // value => (value || '').length <= 20 || 'Max 20 characters.',
                    value => {
                      const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                      return pattern.test(value) || 'Invalid e-mail.'
                    }
                  ],
          name: [
                      value => !!value || 'Required.',
                      value => {
                        const pattern = /^[0-9a-zA-Z]+$/
                        return pattern.test(value) || 'Allowed only latin characters and numbers.'
                      },
                      value => (value || '').length <= 15 || 'Max 15 characters.',
                      value => (value || '').length >= 3 || 'Min 3 characters'
                    ],
          password: [
                      value => {
                        const pattern = /^[0-9a-zA-Z]+$/
                        return pattern.test(value) || 'Allowed only latin characters and numbers.'
                      },
                      value => !!value || 'Required.',
                      value => (value || '').length >= 5 || 'Min 5 characters'
                    ],
          password_confirmation: [
                              value => !!value || 'Required.',
                              value => (value || '').length >= 5 || 'Min 5 characters',
                              value => this.user.password === this.user.password_confirmation || "Passwords do not match."
                            ],
          isFormValid: false,
        },

        user: {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        },
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
      // this.$vuetify.theme.dark = true
     },

    methods: {
      userSignUp: function() {
        let alert = this.alert;
        let userData = this.user;
        let form  = this.$refs.signUp;
        this.loader.status = true;
        for (const value in userData) {
                    if (userData.hasOwnProperty(value)) {
                      userData[value] = userData[value].trim();
                    }
                  }
        this.axios.post("/api/auth/registration", userData).then(response => {
                this.loader.status = false;
                alert.messages = [];
                console.log(response.data);
                if(response.data.code == 1){
                  alert.status = true;
                  for (const key in response.data.message) {
                    if (response.data.message.hasOwnProperty(key)) {
                      const message = response.data.message[key];
                      alert.messages.push(message[0]);
                    }
                  }
                  alert.color = 'green';
                  form.reset();
                }
                else if(response.data.code == 0){
                  alert.status = true;
                  for (const key in response.data.message) {
                    if (response.data.message.hasOwnProperty(key)) {
                      const message = response.data.message[key];
                      alert.messages.push(message[0]);
                    }
                  }
                  alert.color = 'red';
                }
                // this.users = response.data
            })
      }
    },

    watch: {
      status (val) {
        if (!val) return;
      }
    }
  }
</script>