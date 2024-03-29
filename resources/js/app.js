import './bootstrap'
// 削除？
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
import VueLoading from 'vue-loading-template'
import "./echarts";

window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VueTheMask);
Vue.use(DatetimePicker);
Vue.use(VueLoading);

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
            theme: {
                options: {
                    customProperties: true
                },
                dark: false,
                themes: {
                    light: {
                        background: '#FDFDFD'
                    }
                }
            }
        })
    })
};

createApp()
