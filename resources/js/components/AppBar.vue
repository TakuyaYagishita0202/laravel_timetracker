<template>
    <v-app-bar color="transparent" app flat>
        <v-toolbar-title>TimerApp</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn rounded to="/login" text>ログイン</v-btn>
        <v-btn rounded to="/register" text>会員登録</v-btn>
    </v-app-bar>
</template>

<script>
export default {};
</script>

<style scoped>
a {
    text-decoration: none;
}
</style>
