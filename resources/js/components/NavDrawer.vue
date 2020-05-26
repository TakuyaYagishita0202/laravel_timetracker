<template>
  <v-navigation-drawer
    v-if="isLogin"
    :disable-route-watcher="true"
    :src="navImages[navImage]"
    app
    dark
  >
    <v-list-item class="px-2">
      <!-- 以下仮仕様 -->
      <v-menu top offset-x :close-on-content-click="false">
        <template v-slot:activator="{ on }">
          <v-list-item-avatar class="avatar" v-on="on">
            <v-img :src="avatarImages[avatarImage]"></v-img>
          </v-list-item-avatar>
        </template>
        <v-card max-width="500">
          <v-slide-group v-model="avatarImage" class="pa-2" mandatory center-active show-arrows>
            <v-slide-item
              v-for="image in avatarImages"
              :key="image"
              v-slot:default="{ active, toggle }"
            >
              <v-sheet
                :class="active && 'image_active'"
                class="ma-2 image"
                style="borderRadius:50%"
                @click="toggle"
              >
                <v-img :src="image" height="30" width="30" />
              </v-sheet>
            </v-slide-item>
          </v-slide-group>
        </v-card>
      </v-menu>

      <!-- 仕様変更後は下記を利用 -->
      <!-- <v-list-item-avatar class="avatar">
        <v-img :src="userimage"></v-img>
      </v-list-item-avatar>-->

      <v-list-item-title>{{ username }}</v-list-item-title>
    </v-list-item>

    <v-divider></v-divider>

    <!-- ナビゲーション -->
    <v-list rounded>
      <v-list-item v-for="item in items" :key="item.title" :to="item.url" :disabled="item.disabled">
        <v-list-item-icon v-if="item.badge">
          <v-badge v-if="activeTimer" color="pink" dot overlap>
            <v-icon>{{ item.icon }}</v-icon>
          </v-badge>
          <v-icon v-else>{{ item.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-icon v-else>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title>{{ item.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <!-- 下部 -->
    <template v-slot:append>
      <div class="px-2">
        <!-- 設定ボタンと押下後のメニュー -->
        <v-menu top offset-x :close-on-content-click="false">
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-icon>mdi-cog</v-icon>
            </v-btn>
          </template>
          <v-card max-width="500">
            <!-- 背景選択のスライドグループ -->
            <v-slide-group v-model="navImage" class="pa-2" mandatory center-active show-arrows>
              <v-slide-item
                v-for="image in navImages"
                :key="image"
                v-slot:default="{ active, toggle }"
              >
                <v-sheet :class="active && 'image_active'" class="ma-2 image" @click="toggle">
                  <v-img :src="image" height="100" width="50" />
                </v-sheet>
              </v-slide-item>
            </v-slide-group>
            <v-list>
              <!-- ダークモード選択 -->
              <v-list-item>
                <v-list-item-action>
                  <v-switch v-model="$vuetify.theme.dark" color="purple"></v-switch>
                </v-list-item-action>
                <v-list-item-title>ダークモード</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </div>
      
      <!-- ログアウトボタン -->
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
      items: [
        {
          title: "タイマー",
          icon: "mdi-clock",
          url: "/",
          disabled: false,
          badge: true
        },
        {
          title: "ダッシュボード",
          icon: "mdi-poll-box",
          url: "/dashboard",
          disabled: false,
          badge: false
        }
      ],
      navImage: 0,
      navImages: [
        "./img/NavDrawer/01.png",
        "./img/NavDrawer/02.png",
        "./img/NavDrawer/03.png",
        "./img/NavDrawer/04.png",
        "./img/NavDrawer/05.png",
        "./img/NavDrawer/06.png",
        "./img/NavDrawer/07.png",
        "./img/NavDrawer/08.png",
        "./img/NavDrawer/09.png",
        "./img/NavDrawer/10.png"
      ],
      avatarImage: 0,
      avatarImages: [
        "./img/avatar/01.png",
        "./img/avatar/02.png",
        "./img/avatar/03.png",
        "./img/avatar/04.png",
        "./img/avatar/05.png",
        "./img/avatar/06.png",
        "./img/avatar/07.png",
        "./img/avatar/08.png",
        "./img/avatar/09.png",
        "./img/avatar/10.png",
        "./img/avatar/11.png",
        "./img/avatar/12.png",
        "./img/avatar/13.png",
        "./img/avatar/14.png",
        "./img/avatar/15.png",
        "./img/avatar/16.png"
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
    },
    toggleTheme() {
      this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
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
.image {
  border: 3px solid #cfd8dc !important;
}
.image_active {
  border-color: #00cae3 !important;
}
.v-btn:focus {
  outline: 0 !important;
}
.avatar:hover {
  cursor: pointer;
  transition: 0.2s;
  opacity: 0.8;
}
.avatar_select {
  border-radius: 50%;
}
</style>
