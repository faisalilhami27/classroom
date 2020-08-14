require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue';
import Vuetify from 'vuetify';
import VueConfirmDialog from 'vue-confirm-dialog'
import router from './router';
import store from './store';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import 'vuetify/dist/vuetify.min.css';
import 'material-design-icons-iconfont/dist/material-design-icons.css';

Vue.use(Vuetify)
Vue.use(Toast)
Vue.use(VueConfirmDialog)
Vue.use(require('vue-resource'))

const opts = {}
export default new Vuetify(opts)

Vue.component('app', require('./App.vue').default);
Vue.component('InfiniteLoading', require('vue-infinite-loading').default);
Vue.component('vue-confirm-dialog', VueConfirmDialog.default)

new Vue({
  vuetify: new Vuetify(),
  router,
  store
}).$mount('#app');
