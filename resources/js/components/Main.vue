<template>
      <v-app>
        <v-app-bar elevation=12 app d-flex justify-space-between :clipped-left="$vuetify.breakpoint.lgAndUp">
          <v-app-bar-nav-icon @click.stop="drawer.status = !drawer.status"></v-app-bar-nav-icon>
          <v-toolbar-title>nChat</v-toolbar-title>
          <v-spacer></v-spacer>
        </v-app-bar>
        <v-navigation-drawer
          app
          :clipped="$vuetify.breakpoint.lgAndUp"
          v-model="drawer.status"
          :mini-variant="drawer.miniVariant"
          :expand-on-hover="drawer.expandOnHover"
		  :disable-route-watcher="drawer.disableRouteWatcher"
          :disable-resize-watcher="drawer.disableResizeWatcher"
          fixed
        >
          <v-list
            nav
            dense
            height="100%" 
          >
            <v-list-item-group
              v-for="item in drawer.items" :key="item.text"
              active-class="deep-purple text--accent-4"
            >
                  <!-- <router-link :to=item.path> -->
              <v-list-item :to=item.path exact @click="itemEvent(item.name)">
                <v-list-item-icon>
                  <v-icon>mdi-{{item.icon}}</v-icon>
                </v-list-item-icon>
                <v-list-item-title>{{item.text}}</v-list-item-title>
              </v-list-item>
                <!-- </router-link> -->
            </v-list-item-group>
            <v-switch
              align="end"
              v-model="$vuetify.theme.dark"
              primary
            />
			<v-divider></v-divider>
            <div class="vuebar-element pa-0 ma-0"  v-bar>
            <div style="max-height:400px;" class="pa-0 ma-0" v-chat-scroll="{always: false, smooth: true, enabled: false}">
            <v-list v-if="joinedRooms" subheader>
                <v-list-item
                    dense
                    v-for="(room, index) in joinedRooms" 
                    :key="index"
                    @click=""
                    class="px-1"
                    link
                    :to ="'/dashBoard/room/id' + room.id"
                    exact
                  >
                    <v-list-item-avatar size=32>
                      <v-img :src="'http://localhost:8000/images/RoomImgs/' + room.photo"></v-img>
                    </v-list-item-avatar>

                    <v-list-item-content>
                      <v-list-item-title v-text="room.name"></v-list-item-title>
                      <v-list-item-subtitle v-text="room.description"></v-list-item-subtitle>
                    </v-list-item-content>
                  </v-list-item>
                  </v-list>
                  </div>
              </div>
          </v-list>
        </v-navigation-drawer>
        <v-content >
        <v-container style="height:100%" fluid>
          <router-view :key="$route.params.id"></router-view>
        </v-container>
        </v-content>
       
      </v-app>
</template>

<script>
import {mapMutations, mapActions, mapGetters} from "vuex";
export default {
      data: () => ({
          footer: {
              inset: false
          },
          drawer: {
              status: false,
              miniVariant: true,
              expandOnHover: true,
              disableRouteWatcher: true,
              disableResizeWatcher: false,
              items: [
                  { icon: 'home', text: 'Main Page', path: '/dashBoard', name: 'main' },
                  { icon: 'account-cog', text: 'Account Settings', path:"/dashBoard/profile/settings", name: 'settings' },
                  //{ icon: 'history', text: 'History', path: '', name: 'history' },
                  //{ icon: 'card-plus', text: 'Create Room', path: '/dashboard/newRoom', name: 'newRoom'},
                  { icon: 'group', text: 'Rooms', path: '/dashboard/chatRooms', name: 'rooms'},
                  { icon: 'logout', text: 'Log Out', path: '', name: 'logOut'},
              ],
        }
      }),
      created() {
        // this.$vuetify.theme.dark = true;
      },
      methods: {
        ...mapActions([
                'logOut',
                'fetchRooms'
                ]),

        itemEvent: function(name){
          switch (name) {
            case "logOut":
              this.logOut().then(() => {
                this.$router.push("/signIn");
              });
              break;
              
            default:
              break;
          }
        }
      },
      watch: {
          
      },
      computed:{
          ...mapGetters([
                'joinedRooms',
                ]),
      },
      mounted(){
          this.fetchRooms().then(response => {
              console.log(response);
              console.log(this.joinedRooms)
          }, error => {
              console.error(error)
          })
      }

}
</script>

<style >
  .v-item-group a {
    text-decoration: none;
  }
  
  .v-list a {
    text-decoration: none;
  }
  
  .vuebar-element {
    height: 400px;
    width: 100%;
  }
    
   .vb > .vb-dragger {
      z-index: 5;
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
</style>