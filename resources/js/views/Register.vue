<template>
  <v-row>
    <v-spacer></v-spacer>
    <v-col cols="9" md="6" class="mt-n11">
      <v-img :src="'./svg/register.svg'"></v-img>
    </v-col>
    <v-spacer></v-spacer>
    <v-col cols="12" md="5" class="mt-n11">
      <v-card elevation="0">
        <v-card-title>
          <h1 class="display-1 font-weight-bold">三日坊主を卒業しましょう。</h1>
        </v-card-title>
        <v-card-text>
          <div class="text--primary subtitle-2">
            Quitterは「習慣化」を後押しするタイムトラッキングツールです。
            <br />プログラミングやデザイン、語学や資格の学習など
            <br />日々の自己投資を記録して、なりたい自分に近づきましょう。
          </div>
        </v-card-text>
        <v-card-title class="pb-0">
          <h2 class="title">アカウントを作成する</h2>
        </v-card-title>
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
              @keyup.enter="register"
            />
            <small>
              *利用を継続することで Quitter の
              <v-dialog v-model="termsDialog" width="600px">
                <template v-slot:activator="{ on }">
                  <span class="link" v-on="on">利用規約</span>
                </template>
                <TermsOfUse @termsDialog="termsDialog = false" />
              </v-dialog>と
              <v-dialog v-model="privacyDialog" width="600px">
                <template v-slot:activator="{ on }">
                  <span class="link" v-on="on">プライバシーポリシー</span>
                </template>
                <PrivacyPolicy @privacyDialog="privacyDialog = false" />
              </v-dialog>
              に同意したものとみなされます。
            </small>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <!-- <v-btn text color="#00acee">
            <v-icon left>mdi-twitter</v-icon>twitterで登録
          </v-btn>-->
          <v-tooltip right>
            <template v-slot:activator="{ on }">
              <v-btn text color="#00acee" v-on="on">
                <v-icon left>mdi-twitter</v-icon>twitterで登録
              </v-btn>
            </template>
            <span>
              <v-icon dark left>mdi-alert-circle</v-icon>現在準備中です
            </span>
          </v-tooltip>
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
    </v-col>
    <v-spacer></v-spacer>
    <template v-if="registerErrors">
      <v-snackbar v-model="snackbar" multi-line vertical color="error" right bottom>
        <ul v-if="registerErrors.email">
          <li v-for="msg in registerErrors.email" :key>
            <span>{{ msg }}</span>
          </li>
        </ul>
        <ul v-if="registerErrors.password">
          <li v-for="msg in registerErrors.password" :key>
            <span>{{ msg }}</span>
          </li>
        </ul>
        <v-btn text dark @click="snackbar = false">閉じる</v-btn>
      </v-snackbar>
    </template>
  </v-row>
</template>

<script>
import TermsOfUse from "../components/TermsOfUse.vue";
import PrivacyPolicy from "../components/PrivacyPolicy.vue";
export default {
  components: {
    TermsOfUse,
    PrivacyPolicy
  },
  created() {
    this.clearError();
  },
  data() {
    return {
      snackbar: false,
      showPassword: false,
      registerForm: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      },
      termsDialog: false,
      privacyDialog: false
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
      this.snackbar = true;
      return this.$store.state.auth.registerErrorMessages;
    }
  }
};
</script>

<style scoped>
.v-btn {
  text-transform: none !important;
}
.link {
  color: #0000ee;
}
.link:hover {
  cursor: pointer;
}
</style>
