<template>
  <v-app>
    <AppBar />
    <NavDrawer />
    <v-content>
      <v-container fluid>
        <RouterView />
      </v-container>
    </v-content>
  </v-app>
</template>
<script>
import AppBar from "./components/AppBar.vue";
import NavDrawer from "./components/NavDrawer.vue";
export default {
  components: {
    AppBar,
    NavDrawer
  }
};
</script>
<style>
.v-content{
    background: #eee;
}
</style>