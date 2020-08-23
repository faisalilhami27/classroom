<template>
  <div>
    <v-btn
      style="margin-left: 35px; margin-top: -10px"
      text
      x-small
      color="red"
      @click="toggleActive(discussionId)"
    >
      Balas
    </v-btn>
    <v-col cols="12" sm="12" md="12" style="margin-bottom: -20px">
      <transition name="slide">
        <v-textarea
          :disabled="disableTextAnswer"
          v-model="messageAnswer"
          auto-grow
          v-show="isActive(discussionId)"
          solo
          rows="1"
          background-color="#F7F7F7"
          rounded
          placeholder="Ketikan sesuatu..."
          @keydown="typingMessage"
          @keydown.enter.exact.prevent="answerDiscussion(discussionId)"
        ></v-textarea>
      </transition>
    </v-col>
    <div v-if="answerList.length > 0">
      <v-btn
        style="margin-left: 35px; margin-top: -10px"
        text
        x-small
        color="primary"
        @click="newVisible = !newVisible"
      >
        {{ changeText(total, newVisible) }}
      </v-btn>
      <v-col cols="12" sm="12" md="12" style="margin-bottom: -20px">
        <p v-if="userTyping" style="font-size: 13px; margin-left: 30px">{{ userTyping.name }} sedang mengetik...</p>
        <div v-for="(answer, index) in answerList" :key="index">
          <transition name="slide">
            <div v-show="newVisible" style="margin-left: 30px">
              <v-avatar v-if="answer.employee_id != null" size="36px" :color="answer.employee.color">
                <span v-if="answer.employee.photo == null" class="white--text">{{ answer.employee.name.substr(0, 2) }}</span>
                <img v-else alt="Avatar" :src="`/storage/${answer.employee.photo}`">
              </v-avatar>
              <v-avatar v-else size="36px" :color="answer.student.color">
                <span v-if="answer.student.photo == null" class="white--text">{{ answer.student.name.substr(0, 2) }}</span>
                <img v-else alt="Avatar" :src="`/storage/${answer.student.photo}`">
              </v-avatar>
              <strong v-if="answer.employee_id != null" class="name">{{ answer.employee.name }}</strong>
              <strong v-else class="name">{{ answer.student.name }}</strong>
              <v-chip
                class="ma-2"
                x-small
                color="purple"
                text-color="white"
              >
                {{ answer.post_time }}
              </v-chip>
              <div class="action">
                <v-menu offset-y>
                  <template v-if="checkGuard === 'employee'" v-slot:activator="{ on }">
                    <v-btn v-if="user === answer.employee_id" v-on="on" icon>
                      <v-icon small>mdi-dots-vertical</v-icon>
                    </v-btn>
                    <v-btn v-else icon></v-btn>
                  </template>
                  <template v-else v-slot:activator="{ on }">
                    <v-btn v-if="user === answer.student_id" v-on="on" icon>
                      <v-icon small>mdi-dots-vertical</v-icon>
                    </v-btn>
                    <v-btn v-else icon></v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="openModalUpdate(answer.id)">
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="openModalDelete(answer.id)">
                      <v-list-item-title>Hapus</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
              <div class="clearfix"></div>
              <p style="margin-left: 45px">{{ answer.message }}</p>
            </div>
          </transition>
        </div>
      </v-col>
    </div>
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Edit Balasan</v-card-title>
        <v-card-text>
          <v-textarea
            :disabled="disableUpdateTextDiscussion"
            v-model="messageUpdate"
            auto-grow
            solo
            rows="1"
            background-color="#F7F7F7"
            rounded
            placeholder="Ketikan sesuatu..."
            @keydown="typingMessage"
            @keydown.enter.exact.prevent="updateAnswerDiscussion"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" persistent max-width="320">
      <v-card>
        <v-card-title class="headline">Apakah anda yakin ?</v-card-title>
        <v-card-text>Data yang sudah dihapus tidak bisa dikembalikan</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="dialogDelete = false">Batal</v-btn>
          <v-btn color="green darken-1" text @click="deleteAnswerDiscussion">Ok</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "AnswerDiscussion",
  props: [
    "discussionId",
    "visible"
  ],
  data() {
    return {
      newVisible: this.visible,
      total: 0,
      answerList: [],
      disableTextAnswer: false,
      disableUpdateTextDiscussion: false,
      dialog: false,
      dialogDelete: false,
      userTyping: false,
      typingTimer: false,
      clickBtnAnswer: null,
      messageAnswer: '',
      answerId: '',
      messageUpdate: '',
      type: '',
    }
  },
  mounted() {
    this.getAnswerDiscussion();
    this.listenForNewAnswerDiscussion(this.discussionId);
  },
  computed: {
    ...mapGetters([
      'getClassId',
      'getUser'
    ]),

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

    isActive(i) {
      return this.clickBtnAnswer === i;
    },
    toggleActive(i) {
      this.type = 'create';
      this.clickBtnAnswer = this.isActive(i) ? null : i;
    },

    changeText(amount, visible) {
      if (visible) {
        return 'Sembunyikan';
      } else {
        return `Lihat ${amount} balasan`;
      }
    },

    getAnswerDiscussion(url = '') {
      axios.get(url ? url : '/e-learning/load/answer/discussion', {
        params: {
          discussion_id: this.discussionId
        }
      }).then(response => {
        const data = response.data.answer;
        const mergeData = data.map(data => ({
            ...data,
            visible: false
          })
        );
        this.answerList.push(...mergeData);
        this.total = this.answerList.length;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    answerDiscussion(discussionId) {
      if (this.messageAnswer !== '') {
        this.disableTextAnswer = true;
        axios.post('/e-learning/answer/discussion', {
          message: this.messageAnswer,
          discussion_id: discussionId
        }).then(response => {
          this.disableTextAnswer = false;
          if (response.data.status === 200) {
            this.messageAnswer = '';
            this.answerList.push(response.data.answer);
            this.total = response.data.count;
            this.clickBtnAnswer = null;
          } else {
            this.setAlert({
              message: response.data.message,
              status: response.data.status
            });
          }
        })
          .catch(resp => {
            this.disableTextAnswer = false;
            alert(resp.response.data.message);
          });
      }
    },

    openModalUpdate(answerId) {
      this.type = 'update';
      this.dialog = true;
      this.answerList.map(item => {
        if (item.id === answerId) {
          this.messageUpdate = item.message;
        }
      });
      this.answerId = answerId;
    },

    openModalDelete(answerId) {
      this.dialogDelete = true;
      this.answerId = answerId;
    },

    updateAnswerDiscussion() {
      if (this.messageUpdate !== '') {
        this.disableUpdateTextDiscussion = true;
        axios.put('/e-learning/update/answer/discussion', {
          message: this.messageUpdate,
          answer_id: this.answerId
        }).then(response => {
          const answer = response.data.answer;
          this.disableUpdateTextDiscussion = false;
          if (response.data.status === 200) {
            this.messageUpdate = '';
            this.dialog = false;
            this.answerList.map(item => {
              if (item.id === answer.id) {
                item.message = answer.message;
              }
            });
          } else {
            this.setAlert({
              message: response.data.message,
              status: response.data.status
            });
          }
        }).catch(resp => {
          this.disableUpdateTextDiscussion = false;
          alert(resp.response.data.message);
        });
      }
    },

    typingMessage() {
      const user = this.getUser;
      Echo.join('class-answer.' + this.getClassId + '.' + this.discussionId)
        .whisper('typing', user);
    },

    listenForNewAnswerDiscussion(id) {
      Echo.join('class-answer.' + this.getClassId + '.' + id)
        .listen('AnswerDiscussionEvent', (e) => {
          if (e.type === 'create') {
            this.answerList.push(e.answer);
            this.total = e.count;
          } else {
            this.answerList.map(item => {
              if (item.id === e.answer.id) {
                item.message = e.answer.message;
              }
            });
          }
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

      Echo.join('delete-answer.' + this.getClassId + '.' + id)
        .listen('DeleteAnswerDiscussionEvent', (e) => {
          const index = this.answerList.findIndex(params => params.id == e.answerId);
          if (index !== -1) this.answerList.splice(index, 1);
        });
    },

    deleteAnswerDiscussion () {
      axios.delete('/e-learning/delete/answer/discussion', {
        params: {
          answer_id: this.answerId,
          class_id: this.getClassId,
          discussion_id: this.discussionId
        }
      }).then(response => {
        if (response.data.status === 200) {
          this.dialogDelete = false;
          const index = this.answerList.findIndex(params => params.id === this.answerId);
          if (index !== -1) this.answerList.splice(index, 1);
        }
      })
    },
  }
}
</script>

<style scoped>
.slide-enter-active {
  -moz-transition-duration: 0.3s;
  -webkit-transition-duration: 0.3s;
  -o-transition-duration: 0.3s;
  transition-duration: 0.3s;
  -moz-transition-timing-function: ease-in;
  -webkit-transition-timing-function: ease-in;
  -o-transition-timing-function: ease-in;
  transition-timing-function: ease-in;
}

.slide-leave-active {
  -moz-transition-duration: 0.3s;
  -webkit-transition-duration: 0.3s;
  -o-transition-duration: 0.3s;
  transition-duration: 0.3s;
  -moz-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  -webkit-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  -o-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
}

.slide-enter-to, .slide-leave {
  max-height: 100px;
  overflow: hidden;
}

.slide-enter, .slide-leave-to {
  overflow: hidden;
  max-height: 0;
}

.clearfix {
  clear: both;
}

.name {
  display: inline-block;
  margin-left: 5px;
}

.action {
  float: right;
  position: relative;
  left: 10px;
}
</style>
