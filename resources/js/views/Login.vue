<template>
    <v-card max-width="600px" class="mx-auto mt-5">
        <v-card-title>
            <h1 class="display-1">ログイン</h1>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-text-field
                    v-model="loginForm.email"
                    label="Email"
                    prepend-icon="mdi-email"
                />
                <v-text-field
                    v-model="loginForm.password"
                    :type="showPassword ? 'text' : 'password'"
                    label="Password"
                    prepend-icon="mdi-lock"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                />
            </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="info" text @click="login">ログイン</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "LoginPage",
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

            // トップページに移動する
            this.$router.push("/");
        }
    }
};
</script>

<style></style>
