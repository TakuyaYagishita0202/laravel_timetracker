<template>
    <v-navigation-drawer
        v-if="isLogin"
        :src="images[image]"
        v-model="drawer"
        app
        dark
    >
        <v-list-item class="px-2">
            <v-list-item-avatar>
                <!-- Userimageを受け取る -->
                <v-img
                    src="https://lh3.googleusercontent.com/ogw/ADGmqu9ZM0D6-WQu9hYQ8IW-30gUdCh0F-ZoYZjTKaDP=s83-c-mo"
                ></v-img>
            </v-list-item-avatar>
            <!-- Usernameを受け取る -->
            <v-list-item-title>{{ username }}</v-list-item-title>
        </v-list-item>

        <v-divider></v-divider>

        <v-list rounded>
            <v-list-item
                v-for="item in items"
                :key="item.title"
                :to="item.url"
                :disabled="item.disabled"
            >
                <v-list-item-icon v-if="item.badge">
                    <v-badge v-if="activeTimer" color="pink" dot overlap>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-badge>
                    <v-icon v-else>{{ item.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-icon v-else>
                    <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                    <v-list-item-title>
                        {{ item.title }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>

        <template v-slot:append>
            <div class="px-2">
                <v-menu top offset-x :close-on-content-click="false">
                    <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                            <v-icon>mdi-cog</v-icon>
                        </v-btn>
                    </template>
                    <v-card max-width="500">
                        <v-slide-group
                            v-model="image"
                            class="pa-2"
                            mandatory
                            center-active
                            show-arrows
                        >
                            <v-slide-item
                                v-for="image in images"
                                :key="image"
                                v-slot:default="{ active, toggle }"
                            >
                                <v-sheet
                                    :class="active && 'image_active'"
                                    class="ma-2 image"
                                    @click="toggle"
                                >
                                    <v-img
                                        :src="image"
                                        height="100"
                                        width="50"
                                    />
                                </v-sheet>
                            </v-slide-item>
                        </v-slide-group>

                        <v-list>
                            <v-list-item>
                                <v-list-item-action>
                                    <v-switch
                                        v-model="$vuetify.theme.dark"
                                        color="purple"
                                    ></v-switch>
                                </v-list-item-action>
                                <v-list-item-title>
                                    ダークモード
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-menu>
            </div>
            <div class="pa-2">
                <v-btn block @click="logout">ログアウト</v-btn>
            </div>
        </template>
    </v-navigation-drawer>
</template>
<script>
export default {
    data() {
        return {
            image: 0,
            drawer: true,
            items: [
                {
                    title: "タイマー",
                    icon: "mdi-clock",
                    url: "/",
                    disabled: false,
                    badge: true
                },
                {
                    title: "ダッシュボード",
                    icon: "mdi-poll-box",
                    url: "/dashboard",
                    disabled: false,
                    badge: false
                }
            ],
            images: [
                "./img/NavDrawer_01.png",
                "./img/NavDrawer_02.png",
                "./img/NavDrawer_03.png",
                "./img/NavDrawer_04.png",
                "./img/NavDrawer_05.png",
                "./img/NavDrawer_06.png",
                "./img/NavDrawer_07.png",
                "./img/NavDrawer_08.png",
                "./img/NavDrawer_09.png",
                "./img/NavDrawer_10.png",
            ],
            mini: true,
        };
    },
    methods: {
        async logout() {
            await this.$store.dispatch("auth/logout");

            if (this.apiStatus) {
                this.$router.push("/login");
            }
        },
        toggleTheme() {
            this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
        }
    },
    computed: {
        apiStatus() {
            return this.$store.state.auth.apiStatus;
        },
        isLogin() {
            return this.$store.getters["auth/check"];
        },
        username() {
            return this.$store.getters["auth/username"];
        },
        activeTimer() {
            return this.$store.state.timer.active;
        }
    },
};
</script>
<style scoped>
a {
    text-decoration: none;
}
.image {
    border: 3px solid #CFD8DC !important;
}
.image_active {
    border-color: #00cae3 !important;
}
</style>
