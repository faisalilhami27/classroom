import Vue from 'vue'
import Vuex from 'vuex'
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

Vue.use(Vuex);
Vue.use(Toast, {
  // top-right, top-center, top-left, bottom-right, bottom-center, bottom-left.
  position: 'top-right',
  // place newest toast on the top
  newestOnTop: true,
  // the max number of toasts
  maxToasts: 20,
  // name of the Vue Transition or object with classes to use
  // only enter-active, leave-active and move are applied.
  transition: 'Vue-Toastification__bounce',
  // duration in ms
  // or an object: {enter: Number, leave: Number}
  transitionDuration: 750,
  // allows to dismiss by drag & swipe events
  draggable: true,
  draggablePercent: 0.6,
  // auto pause when out of focus
  pauseOnFocusLoss: true,
  // auto pause on hover
  pauseOnHover: true,
  // close on click
  closeOnClick: true,
  // auto dismiss after this timeout
  timeout: 2000,
  // container element
  container: document.body,
  // custom classes
  toastClassName: [],
  // body classes
  bodyClassName: [],
  // show/hide the progress bar
  hideProgressBar: false,
  // show/hide the close button
  hideCloseButton: false,
  // custom icons here
  icon: true
});

const store = new Vuex.Store({
  state: {
    color: '#3F51B5',
    url: window.location.origin + '/',
    user: localStorage.getItem('user'),
    subject: localStorage.getItem('subject'),
    classId: localStorage.getItem('classId'),
    subjectId: localStorage.getItem('subjectId'),
  },
  mutations: {
    setUser: (state, payload) => {
      state.user = payload;
    },

    setSubject: (state, payload) => {
      state.subject = payload;
    },

    setClassId: (state, payload) => {
      state.classId = payload;
    },

    setSubjectId: (state, payload) => {
      state.subjectId = payload;
    },

    setAlert: (state, payload) => {
      if (payload.status === 200) {
        Vue.$toast.success(payload.message);
      } else {
        Vue.$toast.error(payload.message);
      }
    }
  },
  getters: {
    getUrl: state => state.url,
    getUser: state => state.user,
    getColor: state => state.color,
    getSubject: state => state.subject.split('-').join(' '),
    getClassId: state => state.classId,
    getSubjectId: state => state.subjectId,
  },
  actions: {
    setUser: ({commit}, payload) => {
      localStorage.setItem('user', payload);
      commit('setUser', payload)
    },

    setAlert: ({commit}, payload) => {
      commit('setAlert', payload)
    },

    setSubject: ({commit}, payload) => {
      localStorage.setItem('subject', payload);
      commit('setSubject', payload)
    },

    setClassId: ({commit}, payload) => {
      localStorage.setItem('classId', payload);
      commit('setClassId', payload)
    },

    setSubjectId: ({commit}, payload) => {
      localStorage.setItem('subjectId', payload);
      commit('setSubjectId', payload)
    },
  }
});

export default store;
