<template>
    <v-dialog :fullscreen="$vuetify.breakpoint.xs" width="500" transition="dialog-bottom-transition" v-model="show">
        <v-card>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click="show = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Select an Avatar</v-toolbar-title>
                <v-spacer></v-spacer>
            </v-toolbar>
            <v-layout  wrap v-if="avatars">
                <v-flex
                    v-for="avatar in avatars"
                    :key="avatar.id"
                    xs3 sm3
                    d-flex>
                    <v-card tile flat class="d-flex">
                        <v-card-text class="d-flex">
                            <v-avatar
                                size="96"
                                @click="selectAvatar(avatar)"
                                class="avatar-picker-avatar">
                                <img :src="'/images/static/avatars/' + (avatar.path)">
                            </v-avatar>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-card>
    </v-dialog>
</template>

<script>
    import {mapGetters, mapActions, mapState} from "vuex";
    export default {
        props: {
            value: Boolean
        },
        async mounted () {
            await this.fetchAvatars();
        },
        computed: {
            ...mapGetters([
                'avatars'
                ]),

            show: {
                get () {
                    return this.value
                },
                set (value) {
                    this.$emit('input', value)
                }
            }
        },
        methods: {
            selectAvatar (avatar) {
                this.$emit('selected', avatar.id)
                this.show = false
            },
            ...mapActions([
                'fetchAvatars',
                ])
        }
    }
</script>

<style scoped>
    .v-avatar{
        cursor: pointer;
    }
</style>