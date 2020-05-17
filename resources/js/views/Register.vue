<template>
  <v-card max-width="600px" class="mx-auto mt-5">
    <v-card-title>
      <h1 class="display-1">会員登録</h1>
    </v-card-title>
    <v-alert class="mb-0 py-0" v-if="registerErrors" border="right" colored-border type="error">
      <ul v-if="registerErrors.name">
        <li v-for="msg in registerErrors.name" :key>{{ msg }}</li>
      </ul>
      <ul v-if="registerErrors.email">
        <li v-for="msg in registerErrors.email" :key>
          <span class="error--text">{{ msg }}</span>
        </li>
      </ul>
      <ul v-if="registerErrors.password">
        <li v-for="msg in registerErrors.password" :key>
          <span class="error--text">{{ msg }}</span>
        </li>
      </ul>
    </v-alert>
    <v-card-text>
      <v-form>
        <v-text-field
          tabindex="1"
          v-model="registerForm.name"
          label="ユーザー名"
          prepend-icon="mdi-account-circle"
        />
        <v-text-field
          tabindex="2"
          v-model="registerForm.email"
          label="メールアドレス"
          prepend-icon="mdi-email"
        />
        <v-text-field
          tabindex="3"
          v-model="registerForm.password"
          :type="showPassword ? 'text' : 'password'"
          label="パスワード"
          prepend-icon="mdi-lock"
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="showPassword = !showPassword"
        />
        <v-text-field
          tabindex="4"
          v-model="registerForm.password_confirmation"
          :type="showPassword ? 'text' : 'password'"
          label="パスワード(確認用)"
          prepend-icon="mdi-lock"
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="showPassword = !showPassword"
        />
      </v-form>
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        tabindex="5"
        :disabled="registerForm.name === '' || registerForm.email === '' || registerForm.password === '' || registerForm.password_confirmation === ''"
        color="success"
        text
        @click="register"
      >登録</v-btn>
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
      registerForm: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      }
    };
  },
  methods: {
    async register() {
      // authストアのresigterアクションを呼び出す
      await this.$store.dispatch("auth/register", this.registerForm);

      // 通信に成功した場合は
      if (this.apiStatus) {
        // トップページに移動する
        this.$router.push("/");
      }
    },
    clearError() {
      this.$store.commit("auth/SET_REGISTER_ERROR_MESSAGES", null);
    }
  },
  computed: {
    apiStatus() {
      return this.$store.state.auth.apiStatus;
    },
    registerErrors() {
      return this.$store.state.auth.registerErrorMessages;
    }
  }
};
</script>

<style></style>
