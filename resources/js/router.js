import Vue from "vue";

import Router from "vue-router";

// ページコンポーネントのインポート
// import Home from './views/Home.vue'
import Timer from "./views/Timer.vue";
import Dashboard from "./views/Dashboard.vue";
import Login from "./views/Login.vue";
import Register from "./views/Register.vue";

import store from "./store";

// VueRouterプラグインを使用
Vue.use(Router);

// VueRouterインスタンスをエクスポート
// app.jsでインポートする
export default new Router({
    mode: "history",
    routes: [
        // {
        //   path: '/home',
        //   name: 'home',
        //   component: Home
        // },
        {
            path: "/",
            component: Timer,
            beforeEnter(to, from, next) {
                if (!store.getters["auth/check"]) {
                    next("/login");
                } else {
                    next();
                }
            }
        },
        {
            path: "/login",
            component: Login,
            beforeEnter(to, from, next) {
                if (store.getters["auth/check"]) {
                    next("/");
                } else {
                    next();
                }
            }
        },
        {
            path: "/register",
            component: Register,
            beforeEnter(to, from, next) {
                if (store.getters["auth/check"]) {
                    next("/");
                } else {
                    next();
                }
            }
        },
        {
            path: "/dashboard",
            component: Dashboard,
            beforeEnter(to, from, next) {
                if (!store.getters["auth/check"]) {
                    next("/login");
                } else {
                    next();
                }
            }
        },
        {
            path: "*",
            redirect: "/login"
        }
    ]
});
