<template>
  <v-bottom-navigation app v-if="isLogin" v-model="bottomNav" shift class="d-lg-none">
    <v-btn v-for="item in items" :key="item.title" :to="item.url" :disabled="item.disabled">
      <span>{{item.title}}</span>
      <v-icon>{{item.icon}}</v-icon>
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
          disabled: false
        },
        {
          title: "ダッシュボード",
          icon: "mdi-poll-box",
          url: "/dashboard",
          disabled: false
        },
        {
          title: "設定",
          icon: "mdi-cog",
          url: "/config",
          disabled: true
        }
      ]
    };
  },
  computed: {
    isLogin() {
      return this.$store.getters["auth/check"];
    },
    activeTimer() {
      return this.$store.state.timer.active;
    }
  }
};
</script>

<style scoped>
a {
  text-decoration: none;
}
</style>