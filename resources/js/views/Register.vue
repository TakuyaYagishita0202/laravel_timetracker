<template>
    <v-card max-width="600px" class="mx-auto mt-5">
        <v-card-title>
            <h1 class="display-1">会員登録</h1>
        </v-card-title>
        <v-card-text>
            <v-form>
                <v-text-field
                    label="Username"
                    prepend-icon="mdi-account-circle"
                />
                <v-text-field
                    label="Email"
                    prepend-icon="mdi-email"
                />
                <v-text-field
                    :type="showPassword ? 'text' : 'password'"
                    label="Password"
                    prepend-icon="mdi-lock"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    @click:append="showPassword = !showPassword"
                />
                <v-text-field
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
            <v-btn color="success" text>登録</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "LoginPage",
    data() {
        return {
            showPassword: false
        };
    }
};
</script>

<style></style>
