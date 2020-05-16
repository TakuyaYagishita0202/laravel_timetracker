<template>
    <v-card max-width="600px" class="mx-auto mt-5">
        <v-card-title>
            <h1 class="display-1">会員登録</h1>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-text-field
                    v-model="registerForm.name"
                    label="Username"
                    prepend-icon="mdi-account-circle"
                />
                <v-text-field
                    v-model="registerForm.email"
                    label="Email"
                    prepend-icon="mdi-email"
                />
                <v-text-field
                    v-model="registerForm.password"
                    :type="showPassword ? 'text' : 'password'"
                    label="Password"
                    prepend-icon="mdi-lock"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                />
                <v-text-field
                    v-model="registerForm.password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    label="Password(confirm)"
                    prepend-icon="mdi-lock"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                />
            </v-form>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="success" text @click="register">登録</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "LoginPage",
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

            // トップページに移動する
            this.$router.push("/");
        }
    }
};
</script>

<style></style>
