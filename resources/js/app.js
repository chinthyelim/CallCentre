/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap')

window.Vue = require('vue')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
//support vuex
//import Vuex from 'vuex'
//Vue.use(Vuex)
//import storeData from "./store/index"

//const store = new Vuex.Store(
///   storeData
//)
//import store from './store/index'

const store = require('./store/store').default;
//https://stackoverflow.com/questions/57053728/vuetify-icon-not-showing
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import Vuetify from 'vuetify';
Vue.use(Vuetify);

import VModal from 'vue-js-modal'
Vue.use(VModal)

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('telephone-issue', require('./components/TelephoneIssue.vue').default);
Vue.component('submissions', require('./components/Submissions.vue').default);

export const serverBus = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store,
    vuetify: new Vuetify()
});

try {
    window.$ = window.jQuery = require('jquery');
    window.Popper = require('popper.js').default;
    require('bootstrap');
} catch (e) {}
