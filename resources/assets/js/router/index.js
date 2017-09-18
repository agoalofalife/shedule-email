import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/postman/',
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            path: '/dashboard',
            component: require('../pages/Dashboard.vue'),
        },
    ],
})