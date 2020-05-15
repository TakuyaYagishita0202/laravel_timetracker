require("./bootstrap");

import Vue from "vue";
import VueRouter from "vue-router";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import ja from 'vuetify/es5/locale/ja.js'
import "@mdi/font/css/materialdesignicons.css";
import VueTheMask from 'vue-the-mask'
import DatetimePicker from 'vuetify-datetime-picker'

import router from "./router";

// ルートコンポーネントをインポートする
import App from './App.vue'

window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VueTheMask);
Vue.use(DatetimePicker);

const app = new Vue({
    el: "#app",
    router,
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
});
