<template>
  <v-app>
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
        <v-toolbar-title>
          <router-link :to="`/home`" style="color: white; text-decoration: none"><span
            class="link"><b>Classroom</b></span>
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
    <v-container fluid>
      <v-row no-gutters>
        <v-col sm="12" md="4">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="mx-2"
                fab
                dark
                small
                color="primary"
                v-bind="attrs"
                v-on="on"
              >
                <v-icon dark>mdi-plus</v-icon>
              </v-btn>
            </template>
            <span>Buat chat</span>
          </v-tooltip>
          <h3 style="display: inline-block; margin-left: 27%">Private Chat</h3>
          <br><br>
          <v-divider></v-divider>
          <div class="list-user">
            <v-list>
              <v-list-item-group v-model="item" color="primary">
                <v-list-item
                  v-for="item in items"
                  :key="item.title"
                  @click=""
                >
                  <v-badge
                    bordered
                    bottom
                    color="green"
                    dot
                    offset-x="22"
                    offset-y="20"
                  >
                    <v-list-item-avatar>
                      <v-img :src="item.avatar"></v-img>
                    </v-list-item-avatar>
                  </v-badge>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.title"></v-list-item-title>
                    <v-list-item-subtitle>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias aliquid
                      corporis facere impedit natus nostrum praesentium quae, suscipit, ullam vitae voluptate
                      voluptatibus. Deleniti quibusdam, sequi. Aspernatur enim itaque quas repellat.
                    </v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </div>
        </v-col>
        <v-col cols="12" md="8" style="margin-bottom: -10px">
          <div class="chat-box">
            <v-card
              max-width="300"
              style="margin: 5px"
            >
              <v-card-text>
                <p style="float: right; font-size: 11px; margin-top: -13px; margin-bottom: -5px">22.30</p>
                <div style="clear: both"></div>
                <p style="margin-bottom: -5px; color: black">Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
              </v-card-text>
            </v-card>
            <br>
            <v-card
              max-width="300"
              style="float:right; margin: 5px"
            >
              <v-card-text>
                <p style="float: right; font-size: 11px; margin-top: -13px; margin-bottom: -5px">22.30</p>
                <div style="clear: both"></div>
                <p style="margin-bottom: -5px; color: black">Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
              </v-card-text>
            </v-card>
            <div style="clear: both"></div>
          </div>
          <v-card
            class="mx-auto"
            style="border-radius: 0; background-color: #eaeaea;"
          >
            <v-card-text>
              <v-textarea
                name="input-7-1"
                solo
                rounded
                rows="1"
                placeholder="Ketikan sesuatu disini ..."
                auto-grow
                style="margin-bottom: -30px"
                @keydown.enter.exact.prevent="sendMessage"
              ></v-textarea>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
import Sidebar from '../layouts/Sidebar';
import Account from './Account';
import Announcement from "../other/Announcement";
import {mapActions, mapGetters} from "vuex";

export default {
  name: "ChatPage",
  components: {
    Announcement,
    Sidebar,
    Account
  },
  data: () => ({
    openLeftNavigationDrawer: false,
    item: 0,
    items: [
      {icon: true, title: 'Jason Oner', avatar: 'https://cdn.vuetifyjs.com/images/lists/1.jpg'},
      {title: 'Travis Howard', avatar: 'https://cdn.vuetifyjs.com/images/lists/2.jpg'},
      {title: 'Ali Connors', avatar: 'https://cdn.vuetifyjs.com/images/lists/3.jpg'},
      {title: 'Cindy Baker', avatar: 'https://cdn.vuetifyjs.com/images/lists/4.jpg'},
    ],
  }),
  computed: {
    ...mapGetters([
      'getClassId',
      'getSubject',
      'getUser',
      'getColor'
    ]),

    pageTitle: function () {
      return this.getSubject;
    },

    checkGuard: function () {
      const user = JSON.parse(this.getUser);
      return user.guard;
    }
  },
  methods: {
    sendMessage() {
      alert('oces');
    }
  }
}
</script>

<style scoped>
.link:hover {
  text-decoration: underline;
}

.chat-box {
  height: 485px;
  margin-top: -10px;
  overflow: auto;
  background-color: #b5b5b5;
  border: 1px solid #b5b5b5;
}

.list-user {
  margin-left: -8px;
  overflow: auto;
  height: 490px;
}
</style>
