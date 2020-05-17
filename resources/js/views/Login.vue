<template>
  <v-card max-width="600px" class="mx-auto mt-5">
    <v-card-title>
      <h1 class="display-1">ログイン</h1>
    </v-card-title>
    <v-alert class="mb-0 py-0" v-if="loginErrors" border="right" colored-border type="error">
      <ul v-if="loginErrors.email">
        <li v-for="msg in loginErrors.email" :key>
          <span class="error--text">{{ msg }}</span>
        </li>
      </ul>
      <ul v-if="loginErrors.password">
        <li v-for="msg in loginErrors.password" :key>
          <span class="error--text">{{ msg }}</span>
        </li>
      </ul>
    </v-alert>
    <v-card-text>
      <v-form>
        <v-text-field tabindex="1" v-model="loginForm.email" label="メールアドレス" prepend-icon="mdi-email" />
        <v-text-field tabindex="2"
          v-model="loginForm.password"
          :type="showPassword ? 'text' : 'password'"
          label="パスワード"
          prepend-icon="mdi-lock"
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="showPassword = !showPassword"
        />
      </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn tabindex="3"
        class="target"
        :disabled="loginForm.email === ''||loginForm.password === ''"
        color="info"
        text
        @click="login"
      >ログイン</v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  created() {
    this.clearError();
  },
  data() {
    return {
      showPassword: false,
      loginForm: {
        email: "",
        password: ""
      }
    };
  },
  methods: {
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
      return this.$store.state.auth.loginErrorMessages;
    }
  }
};
</script>

<style></style>
