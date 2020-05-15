import Vue from 'vue'

import Router from 'vue-router'

// ページコンポーネントのインポート
// import Home from './views/Home.vue'
import Timer from './views/Timer.vue'
import Dashboard from './views/Dashboard.vue'
import Login from './views/Login.vue'
import Register from './views/Register.vue'

// VueRouterプラグインを使用
Vue.use(Router)

// VueRouterインスタンスをエクスポート
// app.jsでインポートする
export default new Router({
    mode: 'history',
    routes: [
        // {
        //   path: '/home',
        //   name: 'home',
        //   component: Home
        // },
        {
            path: '/',
            component: Timer
        },
        {
            path: '/login',
            component: Login
        },
        {
            path: '/register',
            component: Register
        },
        {
            path: '/dashboard',
            component: Dashboard
        },
    ]
});
