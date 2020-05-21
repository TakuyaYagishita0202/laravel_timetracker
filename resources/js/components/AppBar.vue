<template>
    <v-app-bar v-if="!isLogin" color="transparent" app flat>
        <v-toolbar-title>quitter</v-toolbar-title>
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
