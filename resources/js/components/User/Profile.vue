<template>
    <v-container fluid>
        <v-layout column>
            <v-card>
                <v-card-text>
                    <v-flex class="mb-4">
                        <v-avatar size="96" class="mr-4">
                            <img v-if="userData.avatar_id" :src="'/images/static/avatars/avatar_' + (userData.avatar_id.toLowerCase()) + '.jpg'" alt="Avatar">
                        </v-avatar>
                        <v-btn @click="openAvatarPicker">Change Avatar</v-btn>
                    </v-flex>
                    <v-form ref="update" v-model="rules.isFormValid">
                        <v-text-field
                            v-model="form.name"
                            :placeholder="userData.name"
                            :rules="rules.userName"
                            label="Username">
                        </v-text-field>
                        <v-text-field
                            disabled
                            v-model="form.email"
                            :placeholder="userData.email"
                            :rules="rules.email"
                            label="Email Address">
                        </v-text-field>
                        <v-text-field
                            v-model="form.password"
                            :rules="rules.password"
                            placeholder="Password"
                            type="password"
                            label="Password">
                        </v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn :disabled="!rules.isFormValid" color="primary" :loading="loading" @click.native="update">
                        <v-icon left dark>mdi-check</v-icon>
                        Save Changes
                    </v-btn>
                </v-card-actions>
            </v-card>
            <br>
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
                <strong> {{alert.message}}! </strong>
              </v-card-text>
            </v-alert>
        </v-layout>
        <avatar-picker
            v-model="showAvatarPicker"
            :current-avatar="userData.avatar_id"
            @selected="selectAvatar"></avatar-picker>
    </v-container>
</template>

<script>
    import AvatarPicker from './AvatarPicker.vue';
    import {mapGetters, mapActions} from "vuex";
    export default {
        pageTitle: 'My Profile',
        components: { AvatarPicker },
        data () {
            return {
                alert: {
                    status: true,
                    message: 'Error',
                    color: 'red'
                },
                loading: false,
                form: {
                    name: '',
                    email: '',
                    password: ''
                },
                showAvatarPicker: false,
                rules: {
                userName: [
                    value => !!value || 'Required.',
                    value => {
                        const pattern = /^[0-9a-zA-Z]+$/
                        return pattern.test(value) || 'Allowed only latin characters and numbers.'
                      },
                      value => (value || '').length <= 15 || 'Max 15 characters.',
                      value => (value || '').length >= 3 || 'Min 3 characters'
                ],
                email: [
                    // value => !!value || 'Required.',
                    // value => (value || '').length <= 20 || 'Max 20 characters.',
                    value => {
                        if(value == ''){
                            return true
                        }
                        else{
                            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                            return pattern.test(value) || 'Invalid e-mail.'
                        }
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
            }
        },
        methods: {
            openAvatarPicker () {
                this.showAvatarPicker = true
            },
            selectAvatar (avatar) {
                console.log(avatar)
                this.avatarChange(avatar);
            },
            ...mapActions([
                'fetchUser',
                'avatarChange',
                'updateUser'
                ]),
            update(){
                let data = {
                    name: this.form.name,
                    email: this.form.email,
                    password: this.form.password
                }
                for (const value in data) {
                    if (data.hasOwnProperty(value)) {
                      data[value] = data[value].trim();
                    }
                }
                this.loading = true;
                this.updateUser(data).then(() => {
                    this.loading = false;
                    this.$refs.update.reset();
                });
            }
        },
        watch: {
        },
        async mounted () {
                // this.fetchUser();
                // console.log("getUser");
        },
        computed: {
            ...mapGetters([
                'userData'
                ]),
        }
    }
</script>