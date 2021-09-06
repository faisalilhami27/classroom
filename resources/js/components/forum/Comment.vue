<template>
  <v-app>
    <Header></Header>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="9">
          <v-card
            class="pa-2 main-card"
            outlined
            tile
          >
            <p class="posting-date">Diposting : {{ date }}</p>
            <v-card-title class="headline">{{ maker }}</v-card-title>
            <v-card-subtitle>{{ title }}</v-card-subtitle>
          </v-card>
        </v-col>
        <v-col cols="12" sm="9">
          <v-card
            id="list-chat"
            class="pa-2 list-chat"
            outlined
            tile
            height="330"
          >
            <div
              v-for="(item, index) in comment"
              :key="index"
            >
              <v-col md="12">
                <v-avatar :color="item.user.color" size="40">
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
  import Header from "../layouts/Header";
  import {mapGetters} from "vuex";

  export default {
    name: "Comment",
    components: {Header},
    data: function () {
      return {
        maker: '',
        title: '',
        date: '',
        text: '',
        comment: [],
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
    computed: {
      ...mapGetters([
        'getUrl',
        'getUser',
        'getClassId'
      ]),

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      },
    },
    updated() {
      this.autoScrollDown();
    },
    methods: {
      getPosting() {
        const id = this.$route.params.id;

        axios.get('/posting/show', {
          params: {
            id: id
          }
        }).then(response => {
          const data = response.data.data;
          this.maker = data.maker;
          this.title = data.title;
          this.date = data.date
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

      typingComment() {
        const id = this.$route.params.id;
        const user = this.getUser;
        Echo.private('groups.' + id)
          .whisper('typing', user);
      },

      listenForNewComment() {
        Echo.private('groups.' + this.$route.params.id)
          .listen('AddNewComment', (e) => {
            console.log(e);
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
    overflow-x: hidden
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
    float: right;
    font-size: 13px;
    color: #A5A5A5
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
</style>
