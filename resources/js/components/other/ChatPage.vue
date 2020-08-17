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
                @click="showCombobox = true"
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
          <h3 style="display: inline-block; margin-left: 27%">Personal Chat</h3>
          <div style="margin-bottom: -70px" v-if="showCombobox">
            <v-col style="margin-bottom: -10px">
              <label v-if="checkGuard === 'employee'"><b>Pilih Siswa</b></label>
              <label v-else><b>Pilih Guru</b></label>
              <v-autocomplete
                @change="choosePerson"
                v-model="personValue"
                :items="personList"
                item-text="name"
                item-value="id"
                outlined
                dense
                chips
                small-chips
              ></v-autocomplete>
            </v-col>
          </div>
          <br><br>
          <v-divider></v-divider>
          <div class="list-user">
            <v-list>
              <v-list-item-group v-model="item" color="primary">
                <v-list-item
                  v-for="(item, index) in chatLists"
                  :key="index"
                  @click="getChat(item.id)"
                >
                  <v-badge
                    bordered
                    bottom
                    color="green"
                    dot
                    offset-x="22"
                    offset-y="20"
                  >
                    <v-list-item-avatar color="red">
                      <span v-if="item.photo == null" class="white--text">{{ item.name.substr(0, 2) }}</span>
                      <v-img v-else :src="item.photo"></v-img>
                    </v-list-item-avatar>
                  </v-badge>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.name"></v-list-item-title>
                    <v-list-item-subtitle v-text="item.message"></v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </div>
        </v-col>
        <v-col cols="12" md="8" style="margin-bottom: -10px; border-left: 1px solid #E0E0E0">
          <div v-if="showChatBox">
            <div class="chat-box" id="chat-box">
              <div v-for="(item, index) in chats" :key="index">
                <div v-if="checkGuard === 'employee'">
                  <v-card
                    max-width="400"
                    class="chat_guest"
                    v-if="user !== item.employee_id"
                  >
                    <v-card-text>
                      <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                      <div style="clear: both"></div>
                      <p class="message">{{ item.message }}</p>
                    </v-card-text>
                  </v-card>
                  <v-card
                    v-else
                    max-width="400"
                    class="my_chat"
                  >
                    <v-card-text>
                      <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                      <div style="clear: both"></div>
                      <p class="message">{{ item.message }}</p>
                    </v-card-text>
                  </v-card>
                  <div style="clear: both"></div>
                  <br>
                </div>
                <div v-else>
                  <v-card
                    max-width="400"
                    class="chat_guest"
                    v-if="user !== item.student_id"
                  >
                    <v-card-text>
                      <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                      <div style="clear: both"></div>
                      <p class="message">{{ item.message }}</p>
                    </v-card-text>
                  </v-card>
                  <v-card
                    v-else
                    max-width="400"
                    class="my_chat"
                  >
                    <v-card-text>
                      <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                      <div style="clear: both"></div>
                      <p class="message">{{ item.message }}</p>
                    </v-card-text>
                  </v-card>
                  <div style="clear: both"></div>
                  <br>
                </div>
              </div>
            </div>
            <v-card
              class="mx-auto"
              style="border-radius: 0; background-color: #eaeaea;"
            >
              <v-card-text>
                <v-col v-if="userTyping" style="margin-bottom: -20px; margin-top: -20px; color: black">
                  <p>{{ userTyping.name }} sedang mengetik...</p>
                </v-col>
                <v-textarea
                  :disabled="disabledTextarea"
                  v-model="message"
                  name="input-7-1"
                  solo
                  rounded
                  rows="1"
                  placeholder="Ketikan sesuatu disini ..."
                  auto-grow
                  style="margin-bottom: -30px"
                  @keydown="typingMessage"
                  @keydown.enter.exact.prevent="sendMessage"
                ></v-textarea>
              </v-card-text>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
import Sidebar from '../layouts/Sidebar';
import Account from './Account';
import Announcement from "../other/Announcement";
import moment from 'moment';
import {mapActions, mapGetters} from "vuex";

export default {
  name: "ChatPage",
  components: {
    Announcement,
    Sidebar,
    Account
  },
  data: () => ({
    showCombobox: false,
    openLeftNavigationDrawer: false,
    showChatBox: false,
    disabledTextarea: false,
    userTyping: false,
    typingTimer: false,
    item: '',
    chatLists: [],
    chats: [],
    classList: [],
    personList: [],
    classValue: null,
    personValue: null,
    userId: '',
    message: ''
  }),
  filters: {
    convertFormatDatetimeToTime: function (datetime) {
      return moment(datetime).format('HH:mm');
    }
  },
  mounted() {
    this.getChatList();
    this.listenForUser();
    this.autoScrollDown();
    this.getTeacherOrStudent();
  },
  updated() {
    this.autoScrollDown();
  },
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

    user: function () {
      const user = JSON.parse(this.getUser);
      return user.user_id;
    },

    checkGuard: function () {
      const user = JSON.parse(this.getUser);
      return user.guard;
    }
  },
  methods: {
    sendMessage() {
      this.disabledTextarea = true;
      if (this.message !== '') {
        axios.post('/chat/send', {
          user_id: (this.personValue == null) ? this.userId : this.personValue,
          message: this.message
        })
          .then(response => {
            this.getChatList();
            const data = response.data.chat;
            this.chats.push(data);
            this.disabledTextarea = false;
            this.message = '';
            if (this.checkGuard === 'employee') {
              this.userId = response.data.chat.chat.student_id;
            } else {
              this.userId = response.data.chat.chat.employee_id;
            }
          })
          .catch(resp => {
            this.disabledTextarea = false;
            alert(resp.response.data.message);
          });
      }
    },

    getChat(id) {
      this.showChatBox = true;
      axios.post('/chat/get', {
        chat_id: id
      })
        .then(response => {
          this.chats = response.data.conversation;
          if (this.checkGuard === 'employee') {
            this.userId = response.data.chat.student_id;
          } else {
            this.userId = response.data.chat.employee_id;
          }
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    typingMessage() {
      const user = this.getUser;
      Echo.join('user.' + this.userId)
        .whisper('typing', user);
    },

    listenForUser() {
      Echo.join('user.' + this.user)
        .listen('NewChattingMessage', (e) => {
          let userId = (this.checkGuard === 'employee') ? e.chat.chat.student_id : e.chat.chat.employee_id;
          console.log(userId + " : " + this.user);
          if (userId == this.userId) {
            console.log('oce')
            this.chats.push(e.chat);
          }
          this.getChatList();
        })
        .listenForWhisper('typing', (user) => {
          this.userTyping = user;

          if (this.typingTimer) {
            clearTimeout(this.typingTimer);
          }

          this.typingTimer = setTimeout(() => {
            this.userTyping = false;
          }, 2000);
        });
    },

    choosePerson() {
      this.showCombobox = false;
      axios.post('/chat/check/user', {
        user_id: this.personValue
      })
        .then(response => {
          this.showChatBox = true;
          this.chats = [];
          const data = response.data.chat;
          if (data != null) {
            this.getChat(data.id);
          }
        })
    },

    getTeacherOrStudent() {
      axios.post('/chat/get/person')
        .then(response => {
          this.personList = response.data.person;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    getChatList() {
      axios.post('/chat/list')
        .then(response => {
          this.chatLists = response.data.chat;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    autoScrollDown() {
      let container = document.getElementById('chat-box');
      if (container != null) {
        container.scrollTop = container.scrollHeight;
      }
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

.title {
  background-color: #3F51B5;
  color: white;
}

.time {
  float: right;
  font-size: 11px;
  margin-top: -13px;
  margin-bottom: -5px;
}

.chat_guest {
  float: left;
  margin: 5px;
}

.my_chat {
  float: right;
  margin: 5px;
}

.message {
  margin-bottom: -5px;
  color: black;
}
</style>
