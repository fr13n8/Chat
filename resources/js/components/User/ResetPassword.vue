<template>
      <v-container
        class="fill-height"
        fluid
        v-if="main"
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
                    absolute
                    >
              <v-card
                tile
                color="primary"
                dark
                flat
                class="d-flex justify-center"
              >
                <v-card-title>New Password</v-card-title>
              </v-card>
              <v-form v-model="rules.isFormValid" ref="signUp">
              <v-card-text>
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
                  @click="changePassword()"
                  >Change</v-btn>
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
          password: '',
          password_confirmation: '',
          hash: this.$route.params.hash,
          id: this.$route.params.id
        },

        main: true
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
       this.axios.get(`/api/resetPasswordCheck/${this.$route.params.hash}/${this.$route.params.id}`).then(response => {
        switch (response.data) {
            case 0:
                this.$router.push("/signIn");
                break;
            case 2:
                this.$router.push("/signIn");
                break;
            default:
                this.main = true;
                break;
          }
      })
     },

    methods: {
      changePassword: function() {
        let alert = this.alert;
        let userData = this.user;
        let form  = this.$refs.signUp;
        this.loader.status = true;
        for (const value in userData) {
                    if (userData.hasOwnProperty(value)) {
                      userData[value] = userData[value].trim();
                    }
                  }
        this.axios.post("/api/user/changePassword", userData).then(response => {
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
                  setTimeout(() => {
                      this.$router.push("/signIn");
                  }, 3000);
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