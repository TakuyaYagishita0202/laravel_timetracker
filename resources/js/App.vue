<template>
  <v-app>
    <v-snackbar top v-model="systemErrorSnackbar" color="error">
      システムエラーが発生しました。
      <v-btn text @click="systemErrorSnackbar = false">閉じる</v-btn>
    </v-snackbar>
    <AppBar />
    <NavDrawer />
    <v-content>
      <v-container fluid>
        <RouterView />
      </v-container>
    </v-content>
    <BottomNav></BottomNav>
    <Footer></Footer>
  </v-app>
</template>
<script>
import AppBar from "./components/AppBar.vue";
import NavDrawer from "./components/NavDrawer.vue";
import Footer from "./components/Footer.vue";
import BottomNav from "./components/BottomNav.vue";
import { INTERNAL_SERVER_ERROR } from "./util";
export default {
  components: {
    AppBar,
    NavDrawer,
    Footer,
    BottomNav
  },
  data() {
    return {
      systemErrorSnackbar: false
    };
  },
  computed: {
    errorCode() {
      return this.$store.state.error.code;
    }
  },
  watch: {
    errorCode: {
      handler(val) {
        if (val === INTERNAL_SERVER_ERROR) {
          this.systemErrorSnackbar = true;
        }
      },
      immediate: true
    },
    $route() {
      this.$store.commit("error/SET_CODE", null);
    }
  }
};
</script>
<style scoped>
/* .v-content{
  background-color: #FDFDFD;
} */
.v-content{
  background-color: var(--v-background-base) !important;
}
</style>