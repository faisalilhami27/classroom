<template>
  <v-app>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="9">
          <v-card
            class="pa-2 main-card"
            outlined
            tile
          >

            <span class="posting-date">Dibuat : {{ date }}</span>
            <span class="posting-deadline">Tenggat : {{ deadline }}</span>
            <div class="clearfix"></div>
            <v-card-title style="position: relative; left: -12px;">{{ maker }}</v-card-title>
            <v-card-subtitle class="detail">{{ title }}</v-card-subtitle>
            <div v-if="checkInstruction">
              <v-divider class="mx-1"></v-divider>
              <p class="instruction">{{ instruction }}</p>
            </div>
            <div class="show-file" v-if="files.length !== 0">
              <v-divider class="mx-1"></v-divider>
              <v-row>
                <v-col
                  class="images"
                  v-for="(item, index) in files"
                  :key="index"
                  cols="12"
                  sm="6">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        class="ma-2"
                        v-bind="attrs"
                        v-on="on"
                        color="purple"
                        text-color="white"
                        @click="openFile(item.file)"
                      >
                        <v-avatar left>
                          <v-icon>mdi-download-circle</v-icon>
                        </v-avatar>
                        {{ splitFilename(item.filename) }}
                      </v-chip>
                    </template>
                    <span>Klik untuk membuka</span>
                  </v-tooltip>
                </v-col>
              </v-row>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="9">
          <v-card
            class="pa-2 list-chat"
            id="list-chat"
            outlined
            tile
          >
            <div
              v-for="(item, index) in comment"
              :key="index"
            >
              <v-col md="12">
                <v-avatar color="red" size="40">
                  <span v-if="item.user.photo == null" class="white--text">{{ item.user.name.substr(0, 2) }}</span>
                  <img v-else :alt="item.user.name.substr(0, 2)" :src="item.user.photo">
                </v-avatar>
                <p class="username">{{ item.user.name }}</p>
                <p class="reply-date">{{ item.date }}</p>
                <p class="chat">{{ item.message }}</p>
              </v-col>
              <v-divider class="mx-3 divider"></v-divider>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="9">
          <v-card
            class="pa-2 reply-chat"
            outlined
            tile
            style="background-color: #F0F0F0"
          >
            <v-row class="row-reply">
              <v-col v-if="userTyping" style="margin-bottom: -50px;">
                <p>{{ userTyping.name }} typing.....</p>
              </v-col>
              <v-col sm="12" style="margin-bottom: -40px; margin-top: 20px">
                <v-textarea
                  auto-grow
                  solo
                  v-model="text"
                  @keyup="checkTextReply"
                  @keydown="typingComment"
                  placeholder="Ketikan sesuatu disini ..."
                  rows="1"
                  class="text-field-chat"
                ></v-textarea>
                <v-tooltip style="" bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      v-on="on"
                      color="teal"
                      class="btn-reply"
                      :disabled="disabledButton"
                      @click.prevent="postingComment"
                      icon>
                      <v-icon size="30">mdi-send</v-icon>
                    </v-btn>
                  </template>
                  <span>Posting</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
  import {mapGetters} from "vuex";

  export default {
    name: "TaskInstruction",
    data: function () {
      return {
        maker: '',
        title: '',
        date: '',
        text: '',
        instruction: '',
        deadline: '',
        comment: [],
        files: [],
        disabledButton: true,
        userTyping: false,
        typingTimer: false
      }
    },
    mounted() {
      this.getPosting();
      this.getComment();
      this.listenForNewComment();
      this.autoScrollDown();
    },
    updated() {
      this.autoScrollDown();
    },
    computed: {
      ...mapGetters([
        'getUser',
        'getClassId'
      ]),

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      },

      checkInstruction: function () {
        return this.instruction !== "";
      }
    },
    methods: {
      getPosting() {
        const id = this.$route.params.posting_id;

        axios.get('/posting/show', {
          params: {
            id: id
          }
        }).then(response => {
          const data = response.data.data;
          const images = data.task.files;
          this.maker = data.maker;
          this.title = data.title;
          this.date = data.date;
          this.deadline = data.task.deadline;
          this.instruction = data.task.instruction;
          if (images != null) {
            images.forEach((item) => {
              this.files.push(item);
            });
          }
        })
          .catch(resp => {
            alert(resp.response.data.message);
          })
      },

      getComment() {
        const id = this.$route.params.id;

        axios.get('/comment/get', {
          params: {
            posting_id: id
          }
        }).then(response => {
          const data = response.data.data;
          data.forEach(item => {
            this.comment.push(item);
          });
        })
          .catch(resp => {
            alert(resp.response.data.message);
          })
      },

      postingComment() {
        const id = this.$route.params.id;
        const text = this.text;

        axios.post('/comment/add', {
          posting_id: id,
          message: text
        }).then(response => {
          const data = response.data.data;
          this.comment.push(data);
          this.text = '';
          this.disabledButton = true;
        })
          .catch(resp => {
            alert(resp.response.data.message);
          })
      },

      checkTextReply() {
        this.disabledButton = this.text === '';
      },

      splitFilename(filename) {
        const length = filename.length;
        if (length > 35) {
          return filename.substr(0, 35) + ' ...';
        } else {
          return filename
        }
      },

      openFile: function (file) {
        window.open('/storage/' + file, '_blank');
      },

      typingComment() {
        const id = this.$route.params.id;
        const user = this.getUser;
        Echo.private('groups.' + id)
          .whisper('typing', user);
      },

      listenForNewComment() {
        Echo.private('groups.' + this.$route.params.id)
          .listen('AddNewComment', (e) => {
            this.comment.push(e);
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

      autoScrollDown() {
        let container = document.getElementById('list-chat');
        container.scrollTop = container.scrollHeight;
      }
    }
  }
</script>

<style scoped>
  .list-chat {
    border-top-left-radius: 7px;
    border-top-right-radius: 7px;
    overflow: auto;
    overflow-x: hidden;
    height: 330px;
  }

  .reply-chat {
    margin-top: -30px;
    box-shadow: 0 6px 6px rgba(0, 0, 0, 0.2);
    border-bottom-left-radius: 7px;
    border-bottom-right-radius: 7px;
  }

  .divider {
    margin-top: -15px
  }

  .main-card {
    box-shadow: 0 6px 6px rgba(0, 0, 0, 0.2);
    border-radius: 7px
  }

  .posting-date {
    margin-left: 5px;
    font-size: 13px;
    color: #A5A5A5;
  }

  .posting-deadline {
    float: right;
    margin-right: 5px;
    font-size: 13px;
    color: #A5A5A5;
  }

  .username {
    margin-left: 50px;
    margin-top: -40px
  }

  .reply-date {
    float: right;
    margin-top: -40px;
    font-size: 14px;
    color: #A5A5A5
  }

  .chat {
    margin-left: 50px;
    margin-top: -18px;
    color: #737373;
    font-size: 14px
  }

  .text-field-chat {
    border-radius: 40px
  }

  .row-reply {
    margin-top: -10px
  }

  .btn-reply {
    margin-top: -73px;
    left: -10px;
    float: right;
    position: relative;
  }

  .instruction {
    white-space: pre-line;
    margin-left: 5px;
    font-size: 14px;
    margin-top: 5px;
  }

  .detail {
    margin-left: -12px;
  }

  .list-file:hover {
    border: 1px solid blue;
  }

  .clearfix {
    clear: both;
  }
</style>
