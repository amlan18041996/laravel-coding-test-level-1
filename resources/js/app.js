/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VueRouter from "vue-router";
import VueAxios from "vue-axios";
import axios from "axios";
import Vue from "vue";

window.Vue = require('vue').default;

Vue.use(VueRouter);
Vue.use(VueAxios, axios);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('index-component', require('./components/Index.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Index from "./components/Index.vue";
import Create from "./components/Create.vue";
import Edit from "./components/Edit.vue";
import Delete from "./components/Delete.vue";

import utils from "./helpers/util.js";
Vue.prototype.$utils = utils;

import VueSweetalert2 from 'vue-sweetalert2';
window.Swal = require("sweetalert2");
Vue.use(VueSweetalert2);

const routes = [
    {
        name: "/",
        path: "/",
        component: Index
    },
    {
        name: "/create",
        path: "/create",
        component: Create
    },
    {
        name: "/edit",
        path: "/edit/:id",
        component: Edit
    },
    {
        name: "/delete",
        path: "/delete/:id",
        component: Delete
    }
];

const router = new VueRouter({ mode: 'history', routes: routes });

import App from "./App.vue";
const app = new Vue(Vue.util.extend({ router }, App)).$mount("#app");
