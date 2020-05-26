<template>
  <v-row>
    <v-spacer></v-spacer>
    <v-col cols="9" md="6" class="mt-n11">
      <v-img :src="'./svg/login.svg'"></v-img>
    </v-col>
    <v-spacer></v-spacer>
    <v-col cols="12" md="5">
      <v-card elevation="0">
        <v-card-title>
          <h1 class="display-1 font-weight-bold">
            おかえりなさい！
            <br />さあ、記録を始めましょう。
          </h1>
        </v-card-title>
        <v-card-text>
          <div class="text--primary subtitle-2">
            日々の積み上げを記録することで自制心が高まり
            <br />目標の達成率が高まるという研究結果があります。
            <br />継続的に記録することでなりたい自分に近づきましょう。
          </div>
        </v-card-text>
        <v-card-title class="pb-0">
          <h2 class="title">アカウントにログイン</h2>
        </v-card-title>
        <v-card-text>
          <v-form>
            <v-text-field
              tabindex="1"
              v-model="loginForm.email"
              label="メールアドレス"
              prepend-icon="mdi-email"
            />
            <v-text-field
              tabindex="2"
              v-model="loginForm.password"
              :type="showPassword ? 'text' : 'password'"
              label="パスワード"
              prepend-icon="mdi-lock"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append="showPassword = !showPassword"
              @keyup.enter="login"
            />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <!-- <v-btn text color="#00acee">
            <v-icon left>mdi-twitter</v-icon>twitterで続行
          </v-btn>-->
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn text color="#00acee" v-on="on">
                <v-icon left>mdi-twitter</v-icon>twitterで続行
              </v-btn>
            </template>
            <span>
              <v-icon dark left>mdi-alert-circle</v-icon>現在準備中です
            </span>
          </v-tooltip>
          <v-spacer></v-spacer>
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn text color="success" v-on="on" 　@click="testLogin">お試しログイン</v-btn>
            </template>
            <span>
              <v-icon dark left>mdi-alert-circle</v-icon>一定期間後、データは初期化されます。
            </span>
          </v-tooltip>
          <v-btn
            tabindex="3"
            class="target"
            :disabled="loginForm.email === ''||loginForm.password === ''"
            color="info"
            text
            @click="login"
          >ログイン</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-spacer></v-spacer>
    <template v-if="loginErrors">
      <v-snackbar v-model="snackbar" multi-line vertical color="error" right bottom>
        <ul v-if="loginErrors.email">
          <li v-for="msg in loginErrors.email" :key>
            <span>{{ msg }}</span>
          </li>
        </ul>
        <ul v-if="loginErrors.password">
          <li v-for="msg in loginErrors.password" :key>
            <span>{{ msg }}</span>
          </li>
        </ul>
        <v-btn text dark @click="snackbar = false">閉じる</v-btn>
      </v-snackbar>
    </template>
  </v-row>
</template>

<script>
export default {
  created() {
    this.clearError();
  },
  data() {
    return {
      snackbar: false,
      showPassword: false,
      loginForm: {
        email: "",
        password: ""
      }
    };
  },
  methods: {
    testLogin() {
      this.loginForm = {
        email: "test@qmail.com",
        password: "12345678"
      };
      this.login()
    },
    async login() {
      // authストアのloginアクションを呼び出す
      await this.$store.dispatch("auth/login", this.loginForm);

      //通信に成功した場合は
      if (this.apiStatus) {
        // トップページに移動する
        this.$router.push("/");
      }
    },
    clearError() {
      this.$store.commit("auth/SET_LOGIN_ERROR_MESSAGES", null);
    }
  },
  computed: {
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
    loginErrors() {
      this.snackbar = true;
      return this.$store.state.auth.loginErrorMessages;
    }
  }
};
</script>

<style scoped>
.v-btn {
  text-transform: none !important;
}
</style>
