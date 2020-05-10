import Router from 'vue-router'
import Home from './views/Home.vue'

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/home',
      name: 'home',
      component: Home
    },
  ]
});