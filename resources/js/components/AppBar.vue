<template>
    <v-app-bar v-if="!isLogin" color="transparent" app flat>
        <v-img :src="'./svg/logo.svg'" max-height="40" max-width="40" class="mr-2" contain></v-img>
        <v-toolbar-title>
            Quitter
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn rounded to="/login" text>ログイン</v-btn>
        <v-btn rounded to="/register" text>会員登録</v-btn>
    </v-app-bar>
</template>

<script>
export default {
    computed: {
        isLogin() {
            return this.$store.getters["auth/check"];
        },
    }
};
</script>

<style scoped>
a {
    text-decoration: none;
}
</style>
