<template>
  <v-footer transparent app v-if="!isLogin" color="transparent" class="d-none d-sm-flex">
    <v-spacer></v-spacer>
    <v-dialog v-model="termsDialog" width="600px">
      <template v-slot:activator="{ on }">
        <v-btn text v-on="on">
          <span class="font-weight-light">利用規約</span>
        </v-btn>
      </template>
      <TermsOfUse @termsDialog="termsDialog = false" />
    </v-dialog>

    <v-dialog v-model="privacyDialog" width="600px">
      <template v-slot:activator="{ on }">
        <v-btn text v-on="on">
          <span class="font-weight-light">プライバシーポリシー</span>
        </v-btn>
      </template>
      <PrivacyPolicy @privacyDialog="privacyDialog = false" />
    </v-dialog>
  </v-footer>
</template>

<script>
import TermsOfUse from "./TermsOfUse.vue";
import PrivacyPolicy from "./PrivacyPolicy.vue";
export default {
  components: {
    TermsOfUse,
    PrivacyPolicy
  },
  data() {
    return {
      termsDialog: false,
      privacyDialog: false
    };
  },
  computed: {
    isLogin() {
      return this.$store.getters["auth/check"];
    }
  }
};
</script>

<style scoped>
a {
  text-decoration: none;
}
</style>
