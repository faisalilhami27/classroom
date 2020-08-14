<template>
  <div>
    <v-card id="lateral" style="border-radius: 0px">
      <v-toolbar
        dark
        tabs
        flat
        :color="getColor"
        height="80px"
      >
        <v-app-bar-nav-icon
          @click.stop="openLeftNavigationDrawer = !openLeftNavigationDrawer"
        ></v-app-bar-nav-icon>
        <v-toolbar-title>
          <router-link :to="`/detail/${getClassId}/${getSubject.split(' ').join('-')}`"
                       style="color: white; text-decoration: none"><span class="link">{{ pageTitle }}</span>
          </router-link>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <div style="margin-right: 20px">
          <announcement></announcement>
        </div>
        <account></account>
      </v-toolbar>
    </v-card>
    <!-- sidebar -->
    <sidebar v-model="openLeftNavigationDrawer"></sidebar>
  </div>
</template>

<script>
  import Announcement from "../other/Announcement";
  import Account from "../other/Account";
  import Sidebar from "./Sidebar";
  import {mapGetters} from "vuex";

  export default {
    name: "Header",
    components: {
      Sidebar,
      Account,
      Announcement
    },
    data() {
      return {
        openLeftNavigationDrawer: false,
      }
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
    }
  }
</script>

<style scoped>
  .link:hover {
    text-decoration: underline;
  }
</style>
