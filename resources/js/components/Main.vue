<template>
  <v-container>
      <v-card>
        <v-app-bar app d-flex justify-space-between :clipped-left="$vuetify.breakpoint.lgAndUp">
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
              <v-list-item :to=item.path exact>
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
          </v-list>
        </v-navigation-drawer>
      </v-card>
      <v-container>
        <router-view></router-view>
      </v-container>
  </v-container>
</template>

<script>
export default {
      data: () => ({
        drawer: {
          status: false,
          miniVariant: true,
          expandOnHover: true,
          items: [
              { icon: 'home', text: 'Main Page', path: '/dashBoard' },
              { icon: 'account-cog', text: 'Account Settings', path:"/dashBoard/profile/settings" },
              { icon: 'history', text: 'History', path: '' },
              { icon: 'logout', text: 'Log Out', path: '' },
          ],
        }
      }),
      created() {
        // this.$vuetify.theme.dark = true;
      }

}
</script>

<style>
  .v-item-group a {
    text-decoration: none;
  }
</style>