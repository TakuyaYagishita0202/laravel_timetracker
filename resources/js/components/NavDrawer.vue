<template>
  <v-navigation-drawer v-if="isLogin" :src="'./img/NavDrawer_01.png'" v-model="drawer" app dark>
    <v-list-item class="px-2">
      <v-list-item-avatar>
        <!-- Userimageを受け取る -->
        <v-img
          src="https://lh3.googleusercontent.com/ogw/ADGmqu9ZM0D6-WQu9hYQ8IW-30gUdCh0F-ZoYZjTKaDP=s83-c-mo"
        ></v-img>
      </v-list-item-avatar>
      <!-- Usernameを受け取る -->
      <v-list-item-title>{{ username }}</v-list-item-title>
    </v-list-item>

    <v-divider></v-divider>

    <v-list rounded>
      <v-list-item v-for="item in items" :key="item.title" :to="item.url" :disabled="item.disabled">
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <template v-slot:append>
      <div class="pa-2">
        <v-btn block @click="logout">ログアウト</v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>
<script>
export default {
  data() {
    return {
      drawer: true,
      items: [
        {
          title: "タイマー",
          icon: "mdi-clock",
          url: "/",
          disabled: false
        },
        {
          title: "ダッシュボード",
          icon: "mdi-poll-box",
          url: "/dashboard",
          disabled: false
        },
        { title: "設定", icon: "mdi-cog", url: "", disabled: true }
      ],
      mini: true
    };
  },
  methods: {
    async logout() {
      await this.$store.dispatch("auth/logout");

      if (this.apiStatus) {
        this.$router.push("/login");
      }
    }
  },
  computed: {
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
    isLogin() {
      return this.$store.getters["auth/check"];
    },
    username() {
      return this.$store.getters["auth/username"];
    }
  }
};
</script>
<style scoped>
a {
  text-decoration: none;
}
</style>
