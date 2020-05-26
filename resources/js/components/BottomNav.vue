<template>
  <v-bottom-navigation app v-if="isLogin" v-model="bottomNav" shift class="d-lg-none">
    <v-btn v-for="item in items" :key="item.title" :to="item.url" :disabled="item.disabled">
      <span>{{item.title}}</span>
      <v-icon>{{item.icon}}</v-icon>
    </v-btn>
    <v-btn @click="logout">
      <span>ログアウト</span>
      <v-icon>mdi-exit-to-app</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
export default {
  data() {
    return {
      bottomNav: 0,
      items: [
        {
          title: "タイマー",
          icon: "mdi-clock",
          url: "/",
        },
        {
          title: "ダッシュボード",
          icon: "mdi-poll-box",
          url: "/dashboard",
        },
      ]
    };
  },
  methods: {
    async logout() {
      if(confirm('ログアウトしますか？')){
        await this.$store.dispatch("auth/logout");

        if (this.apiStatus) {
          this.$router.push("/login");
        }
      }
    },
  },
  computed: {
    isLogin() {
      return this.$store.getters["auth/check"];
    },
    activeTimer() {
      return this.$store.state.timer.active;
    },
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
  }
};
</script>

<style scoped>
a {
  text-decoration: none;
}
</style>