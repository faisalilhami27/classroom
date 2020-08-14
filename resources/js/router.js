import Vue from "vue";
import VueRouter from 'vue-router';
import Home from "./components/Home";
import Detail from "./components/other/Detail";
import TaskDetail from "./components/task/TaskDetail";
import ExamPage from "./components/exam/ExamPage";
import Comment from "./components/forum/Comment";
import ExamDetail from "./components/exam/ExamDetail";

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      name: 'home',
      path: '*',
      component: Home
    },
    {
      name: 'detail',
      path: '/detail/:id/:subject',
      component: Detail
    },
    {
      name: 'task',
      path: '/task/detail/:id/:posting_id',
      component: TaskDetail
    },
    {
      name: 'comment',
      path: '/posting/comment/:id',
      component: Comment
    },
    {
      name: 'exam',
      path: '/exam/page/:id',
      component: ExamPage
    },
    {
      name: 'exam-detail',
      path: '/exam/detail/:id',
      component: ExamDetail
    },
  ]
})

export default router;
