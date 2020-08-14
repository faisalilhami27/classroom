<template>
  <v-app id="insipre">
    <v-card id="lateral">
      <v-toolbar
        dark
        tabs
        flat
        :color="getColor"
        style="border-radius: 0px"
      >
        <v-app-bar-nav-icon
          @click.stop="openLeftNavigationDrawer = !openLeftNavigationDrawer"
        ></v-app-bar-nav-icon>
        <v-toolbar-title>{{ pageTitle }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <div style="margin-right: 20px">
        <announcement></announcement>
        </div>
        <account></account>
        <template v-slot:extension>
          <v-tabs
            v-model="tabs"
            centered
          >
            <v-tab v-for="item in items" :key="item.tab">
              {{ item.tab }}
            </v-tab>
            <v-tabs-slider color="pink"></v-tabs-slider>
          </v-tabs>
        </template>
      </v-toolbar>
      <v-fab-transition>
        <v-tabs-items v-model="tabs">
          <v-tab-item v-for="item in items" :key="item.tab">
            <v-card flat>
              <v-card-text>
                <component :is="item.content"></component>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-fab-transition>
    </v-card>
    <!-- sidebar -->
    <sidebar v-model="openLeftNavigationDrawer"></sidebar>
  </v-app>
</template>

<script>
  import Sidebar from '../layouts/Sidebar';
  import Account from './Account';
  import Forum from '../forum/Forum';
  import Learn from '../learn/Learn';
  import Task from '../task/Task';
  import Exam from '../exam/Exam';
  import Announcement from "./Announcement";
  import {mapGetters} from "vuex";

  export default {
    name: "detail",
    components: {
      Announcement,
      Sidebar,
      Account,
      Forum,
      Learn,
      Task,
      Exam
    },
    data: () => ({
      openLeftNavigationDrawer: false,
      tabs: null,
      items: [
        {tab: 'Forum', content: 'Forum'},
        {tab: 'Tugas Kelas', content: 'Task'},
        {tab: 'Belajar', content: 'Learn'},
        {tab: 'Ujian', content: 'Exam'}
      ]
    }),
    created() {
      document.title = this.getSubject;
    },
    computed: {
      ...mapGetters([
        'getClassId',
        'getSubject',
        'getColor'
      ]),

      pageTitle: function () {
        return this.getSubject;
      }
    },
  }
</script>

<style scoped>
  #lateral .v-btn--example {
    bottom: 0;
    position: absolute;
    margin: 0 0 16px 16px;
  }
</style>
