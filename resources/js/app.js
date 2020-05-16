import './bootstrap'
require("./bootstrap");

import Vue from "vue";
import VueRouter from "vue-router";
import router from "./router";

import store from './store'

// ルートコンポーネントをインポート
import App from './App.vue'

import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import ja from 'vuetify/es5/locale/ja.js'
import "@mdi/font/css/materialdesignicons.css";
import VueTheMask from 'vue-the-mask'
import DatetimePicker from 'vuetify-datetime-picker'

window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VueTheMask);
Vue.use(DatetimePicker);

const createApp = async () => {
    await store.dispatch('auth/currentUser')

    new Vue({
        el: "#app",
        router,
        store,
        components: {
            App
        },
        template: '<App />',
        vuetify: new Vuetify({
            lang: {
                locales: {
                    ja
                },
                current: 'ja',
            },
        })
    })
};

createApp()
