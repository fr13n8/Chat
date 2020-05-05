<template>
  <v-app>
      <v-content>
        <router-view></router-view>
      </v-content>
      <v-footer
        :inset="footer.inset"
        app
        flat
        tile
      >
      <v-card-text class="py-2 text-center">
        &copy; {{ new Date().getFullYear() }} — <strong>nChat</strong>
      </v-card-text>
      </v-footer>
  </v-app>
</template>

<script>
export default {
      data: () => ({
        footer: {
          inset: true
        },
      }),

      created() {
        this.$vuetify.theme.dark = true;
      }

}
</script>

<style>

</style>