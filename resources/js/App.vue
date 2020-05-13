<template>
  <v-app class="px-0 mx-0" >
        
      <v-content class="pa-0 ma-0">
      <v-container fluid class="py-0 my-0" >
        <router-view></router-view>
      </v-container>
      </v-content>
      
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

<style scoped >
.container {
  max-width: 1400px;
}
</style>