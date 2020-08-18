<template>
  <v-app>
    <v-container class="container" fluid>
      <v-row no-gutters>
        <v-col sm="12" md="4">
          <v-app-bar color="primary" dark>
            <v-btn title="Kembali" :href="`/home`" color="orange" fab x-small dark>
              <v-icon>mdi-arrow-left-circle</v-icon>
            </v-btn>
            <v-autocomplete
              @change="choosePerson"
              v-model="personValue"
              :items="personList"
              item-text="name"
              item-value="id"
              :loading="loading"
              :search-input.sync="search"
              cache-items
              clearable
              class="mx-4"
              flat
              hide-no-data
              hide-details
              label="Buat chat baru"
              solo-inverted
            ></v-autocomplete>
          </v-app-bar>
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
                    :color="(checkOnline(userOnline, item)) ? 'green' : '#B2B2B2'"
                    dot
                    offset-x="22"
                    offset-y="20"
                  >
                    <v-list-item-avatar color="red">
                      <span v-if="item.photo == null" class="white--text">{{ item.name.substr(0, 2) }}</span>
                      <v-img v-else :src="`/storage/${item.photo}`"></v-img>
                    </v-list-item-avatar>
                  </v-badge>
                  <v-list-item-content>
                    <v-list-item-title v-text="item.name"></v-list-item-title>
                    <v-list-item-subtitle v-text="item.message"></v-list-item-subtitle>
                  </v-list-item-content>
                  <div v-if="item.count !== 0" style="margin-right: 10px">
                    <v-badge color="green" :content="item.count"></v-badge>
                  </div>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </div>
        </v-col>
        <v-col cols="12" md="8" style="margin-bottom: -10px; border-left: 1px solid #E0E0E0">
          <v-app-bar color="primary" dark>
            <div v-if="userTyping" style="margin-top: 15px">
              <p>{{ userTyping.name }} sedang mengetik...</p>
            </div>
          </v-app-bar>
          <div v-if="showChatBox">
            <v-card
              id="chat-box"
              class="mx-auto overflow-auto container-box"
              height="516"
              color="#E1D9D2"
              style="border-radius: 0"
            >
              <chat-list :chats="chats"></chat-list>
            </v-card>
            <v-card
              class="mx-auto overflow-hidden"
              height="67"
              color="#F0F0F0"
              style="border-radius: 0"
            >
              <v-textarea
                v-model="message"
                name="input-7-1"
                solo
                style="border-radius: 30px; margin: 10px"
                rows="1"
                placeholder="Ketikan pesan"
                :disabled="disabledTextarea"
                @keydown="typingMessage"
                @keydown.enter.exact.prevent="sendMessage"
              ></v-textarea>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
import Sidebar from '../layouts/Sidebar';
import Account from '../other/Account';
import ChatList from './ChatList';
import Announcement from "../other/Announcement";
import moment from 'moment';
import {mapGetters} from "vuex";

export default {
  name: "ChatPage",
  components: {
    Announcement,
    Sidebar,
    Account,
    ChatList
  },
  data() {
    return {
      search: null,
      select: null,
      classValue: null,
      personValue: null,
      loading: false,
      showCombobox: false,
      openLeftNavigationDrawer: false,
      showChatBox: false,
      disabledTextarea: false,
      userTyping: false,
      typingTimer: false,
      item: '',
      chatLists: [],
      items: [],
      chats: [],
      classList: [],
      personList: [],
      userOnline: [],
      userId: '',
      message: ''
    }
  },
  filters: {
    convertFormatDatetimeToTime: function (datetime) {
      return moment(datetime).format('HH:mm');
    }
  },
  created() {
    const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
      cluster: process.env.MIX_PUSHER_APP_CLUSTER
    });
    const channel = pusher.subscribe('my-channel');
    channel.bind('my-event', () => {
      this.getChatList();
    });
  },
  mounted() {
    this.getChatList();
    this.listenForUser();
    this.autoScrollDown();
    this.getTeacherOrStudent();
    this.chatroom();
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
    checkOnline(user,item) {
      if (this.checkGuard === 'employee') {
        const online = user.find(fruit => fruit.student_id === item.student_id);
        if (online != null) {
          return online.student_id === item.student_id;
        }
      } else {
        const online = user.find(fruit => fruit.employee_id === item.employee_id);
        if (online != null) {
          return online.employee_id === item.employee_id;
        }
      }
    },

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
          this.getChatList();
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

    checkConversation(id) {
      axios.post('/chat/get/by/user', {
        chat_id: id
      })
        .then(response => {
          return response.data.chat !== 0;
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
          if (userId === this.userId) {
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
          }, 1000);
        });
    },

    chatroom() {
      Echo.join('chatroom')
        .here((users) => {
          this.userOnline = users;
        })
        .joining((user) => {
          this.userOnline.push(user);
        })
        .leaving((user) => {
          this.userOnline.splice(this.userOnline.indexOf(user), 1);
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
          this.chatLists.map(user => {
            if (this.checkGuard === 'employee') {
              this.userOnline.push(user.student_id);
            } else {
              this.userOnline.push(user.employee_id);
            }
          })
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
  },
}
</script>

<style scoped>
.list-user {
  margin-left: -23px;
  overflow: auto;
  height: 490px;
}

.container {
  margin: 0;
  padding: 0;
}
</style>
