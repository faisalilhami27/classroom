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
              label="Cari atau buat chat baru"
              solo-inverted
            ></v-autocomplete>
          </v-app-bar>
          <div class="list-user">
            <v-list>
              <v-list-item-group v-model="item" color="primary">
                <h4 v-if="chatLists.length < 1" class="text-center">Belum ada riwayat chat</h4>
                <v-list-item
                  v-else
                  v-for="(item, index) in chatLists"
                  :key="index"
                  @click="getChat(item.id)"
                >
                  <v-badge
                    bordered
                    bottom
                    :color="(checkOnline(userOnline, item)) ? 'green' : '#DEDEDE'"
                    dot
                    offset-x="22"
                    offset-y="20"
                  >
                    <v-list-item-avatar :color="item.color">
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
                  <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                      <v-btn style="left: 10px; position: relative" v-on="on" icon>
                        <v-icon small>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item @click="deleteChat(item.id)">
                        <v-list-item-title>Hapus</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
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
              <chat-list :chats="chats" :color="color"></chat-list>
              <picker v-if="emoji" set="apple" @select="onInput" title="Pilih emoticon..."></picker>
            </v-card>
            <v-card
              class="mx-auto overflow-hidden"
              height="67"
              color="#F0F0F0"
              style="border-radius: 0"
            >
              <v-row>
                <v-col md="1" sm="12">
                  <v-btn @click="toggleEmoji" style="margin-left: 20px; margin-top: 5px" icon>
                    <v-icon>mdi-emoticon-outline</v-icon>
                  </v-btn>
                </v-col>
                <v-col md="1" sm="12">
                  <v-speed-dial
                    style="margin-top: 5px; margin-left: -5px"
                    direction="right"
                    transition="slide-x-reverse-transition"
                  >
                    <template v-slot:activator>
                      <v-btn icon>
                        <v-icon>mdi-paperclip</v-icon>
                      </v-btn>
                    </template>
                    <v-tooltip left>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          fab
                          dark
                          small
                          color="green"
                          @click="onButtonClickDocument"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon>mdi-file-document</v-icon>
                        </v-btn>
                        <input
                          ref="uploader"
                          class="d-none"
                          type="file"
                          multiple
                          accept=".xls, .xlsx, .doc, .docx, .pdf, .ppt, .pptx"
                          @change="onFileChanged"
                        >
                      </template>
                      <span>Dokumen</span>
                    </v-tooltip>
                    <v-tooltip right>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          fab
                          dark
                          small
                          color="indigo"
                          @click="onButtonClickImage"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon>mdi-image</v-icon>
                        </v-btn>
                        <input
                          ref="uploader"
                          class="d-none"
                          type="file"
                          multiple
                          accept=".png, .jpg, .jpeg"
                          @change="onFileChanged"
                        >
                      </template>
                      <span>Gambar</span>
                    </v-tooltip>
                  </v-speed-dial>
                </v-col>
                <v-col md="10" sm="12">
                  <v-textarea
                    v-model="message"
                    name="input-7-1"
                    solo
                    style="border-radius: 30px; margin-left: -30px"
                    rows="1"
                    placeholder="Ketikan pesan"
                    :disabled="disabledTextarea"
                    @focus="updateStatusRead"
                    @keydown="typingMessage"
                    @keydown.enter.exact.prevent="sendMessage"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
    <vue-confirm-dialog></vue-confirm-dialog>
  </v-app>
</template>

<script>
import Sidebar from '../layouts/Sidebar';
import Account from '../other/Account';
import ChatList from './ChatList';
import Announcement from "../other/Announcement";
import moment from 'moment';
import {mapActions, mapGetters} from "vuex";
import {Picker} from 'emoji-mart-vue'

export default {
  name: "ChatPage",
  components: {
    Announcement,
    Sidebar,
    Account,
    ChatList,
    Picker
  },
  data() {
    return {
      search: null,
      select: null,
      classValue: null,
      personValue: null,
      emoji: false,
      loading: false,
      openLeftNavigationDrawer: false,
      showChatBox: false,
      disabledTextarea: false,
      userTyping: false,
      isSelecting: false,
      typingTimer: false,
      chatLists: [],
      items: [],
      chats: [],
      classList: [],
      personList: [],
      selectedFile: [],
      userOnline: [],
      item: '',
      type: '',
      userId: '',
      message: '',
      color: '',
      chatId: '',
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
    ...mapActions({
      setAlert: 'setAlert'
    }),

    toggleEmoji() {
      this.emoji = !this.emoji;
    },

    onInput(e) {
      if (!e) {
        return false;
      }

      if (!this.message) {
        this.message = e.native;
      } else {
        this.message = this.message + e.native;
      }
      this.emoji = false;
    },

    checkOnline(user, item) {
      if (this.checkGuard == 'employee') {
        const online = user.find(param => param.student_id === item.student_id);
        if (online != null) {
          return online.student_id === item.student_id;
        }
      } else {
        const online = user.find(param => param.employee_id === item.employee_id);
        if (online != null) {
          return online.employee_id === item.employee_id;
        }
      }
    },

    onButtonClickImage() {
      this.isSelecting = true
      this.type = 'image'
      window.addEventListener('focus', () => {
        this.isSelecting = false
      }, {once: true})

      this.$refs.uploader.click()
    },

    onButtonClickDocument() {
      this.isSelecting = true
      this.type = 'document'
      window.addEventListener('focus', () => {
        this.isSelecting = false
      }, {once: true})

      this.$refs.uploader.click()
    },

    onFileChanged: function (e) {
      let selectedFiles = e.target.files;

      if (!selectedFiles.length) {
        return false;
      }

      for (let i = 0; i < selectedFiles.length; i++) {
        if (this.type === 'image') {
          if (selectedFiles[i]['type'] !== 'image/png' && selectedFiles[i]['type'] !== 'image/jpeg') {
            this.selectedFile.splice(0, this.selectedFile.length);
            alert(`Format yang diizinkan jpg dan png`);
            return false;
          }
        } else {
          if (selectedFiles[i]['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
            selectedFiles[i]['type'] !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' &&
            selectedFiles[i]['type'] !== 'application/vnd.openxmlformats-officedocument.presentationml.presentation' &&
            selectedFiles[i]['type'] !== 'application/x-zip-compressed' &&
            selectedFiles[i]['type'] !== 'application/pdf') {
            this.selectedFile.splice(0, this.selectedFile.length);
            alert('Format yang diizinkan xlsx, xls, doc, docx, ppt, pptx, pdf dan zip');
            return false;
          }
        }
        this.selectedFile.push(selectedFiles[i]);
      }

      if (this.selectedFile.length > 10) {
        alert('Maaf, maksimal upload 10 file');
        this.selectedFile.splice(0, this.selectedFile.length);
      } else {
        this.sendFile();
      }
    },

    sendFile() {
      const formData = new FormData();
      for (let i = 0; i < this.selectedFile.length; i++) {
        let file = this.selectedFile[i];
        formData.append('file[]', file);
      }

      formData.append('type', this.type);
      formData.append('user_id', (this.personValue == null) ? this.userId : this.personValue);
      axios.post('/chat/send', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(response => {
          this.personValue = null;
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
          this.selectedFile.splice(0, this.selectedFile.length);
        })
        .catch(resp => {
          this.selectedFile.splice(0, this.selectedFile.length);
          if (_.has(resp.response.data, 'errors')) {
            _.map(resp.response.data.errors, function (val) {
              alert(val[0]);
            })
          } else {
            alert(resp.response.data.message);
          }
        });
    },

    sendMessage() {
      this.disabledTextarea = true;
      this.type = 'text';
      if (this.message !== '') {
        axios.post('/chat/send', {
          user_id: (this.personValue == null) ? this.userId : this.personValue,
          message: this.message,
          type: this.type
        })
          .then(response => {
            this.getChatList();
            const data = response.data.chat;
            this.chats.push(data);
            this.disabledTextarea = false;
            this.message = '';
            this.personValue = null;
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
      this.chatId = id;
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

    updateStatusRead() {
      axios.post('/chat/get', {
        chat_id: this.chatId
      })
        .then(() => {
          this.getChatList();
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
          let userId = (this.checkGuard === 'employee') ? e.chat.student_id : e.chat.employee_id;
          if (userId === this.userId) {
            if (e.type == 'update') {
              this.chatId = e.chat.id;
              this.chats.map(user => {
                user.status_read = 1
              });
              this.color = 'blue';
            } else {
              this.chats.push(e.chat);
            }
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
      this.chats = [];
      if (this.personValue !== undefined) {
        this.showChatBox = true;
        axios.post('/chat/check/user', {
          user_id: this.personValue
        })
          .then(response => {
            const data = response.data.chat;
            if (data != null) {
              this.getChat(data.id);
            }
          })
      } else {
        this.showChatBox = false;
        this.userTyping = false;
      }
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

    deleteChat(id) {
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: 'Chat yang dihapus tidak bisa dikembalikan.',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            axios.delete('/chat/delete', {
              params: {
                chat_id: id
              }
            })
              .then(response => {
                if (response.data.status === 200) {
                  this.getChatList();
                  this.showChatBox = false;
                  this.userTyping = false;
                } else {
                  this.setAlert({
                    message: response.data.message,
                    status: response.data.status
                  });
                }
              })
              .catch(resp => {
                alert(resp.response.data.message);
              });
          }
        }
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

/* This is for documentation purposes and will not be needed in your application */
#create .v-speed-dial {
  position: absolute;
}

#create .v-btn--floating {
  position: relative;
}
</style>
