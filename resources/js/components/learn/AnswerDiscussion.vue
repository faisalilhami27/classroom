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
                  </template>
                  <template v-else v-slot:activator="{ on }">
                    <v-btn v-if="user === answer.student_id" v-on="on" icon>
                      <v-icon small>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item @click="">
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="">
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
        <div v-if="total > limitPerPage && visible === true">
          <v-col v-if="showBtnLoadMore" cols="12">
            <v-btn block color="primary" outlined @click.prevent="getAnswerDiscussion(nextPageUrl)" dark>
              {{ changeTextLoadMore }}
            </v-btn>
          </v-col>
        </div>
      </v-col>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "AnswerDiscussion",
  props: {
    discussionId: '',
    visible: ''
  },
  data() {
    return {
      newVisible: this.visible,
      nextPageUrl: '',
      changeTextLoadMore: 'Load More',
      currentPage: 1,
      limitPerPage: 3,
      total: 0,
      answerList: [],
      showBtnLoadMore: true,
      disableTextAnswer: false,
      userTyping: false,
      typingTimer: false,
      clickBtnAnswer: null,
      messageAnswer: '',
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
    isActive(i) {
      return this.clickBtnAnswer === i;
    },
    toggleActive(i) {
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
      this.changeTextLoadMore = 'Memuat...';
      axios.get(url ? url : '/e-learning/load/answer/discussion', {
        params: {
          discussion_id: this.discussionId
        }
      }).then(response => {
        this.changeTextLoadMore = 'Load More';
        const data = response.data.discussion.data;
        const mergeData = data.map(data => ({
            ...data,
            visible: false
          })
        );
        this.answerList.push(...mergeData);
        this.nextPageUrl = response.data.discussion.next_page_url;
        this.currentPage = response.data.discussion.current_page;
        this.total = response.data.discussion.total;
        if (this.currentPage === response.data.discussion.last_page) {
          this.showBtnLoadMore = false;
        }
      })
        .catch(resp => {
          this.changeTextLoadMore = 'Load More';
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
          this.messageAnswer = '';
          this.answerList.push(response.data.answer);
          this.total = response.data.count;
        })
          .catch(resp => {
            this.disableTextAnswer = false;
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
          this.answerList.push(e.answer);
          this.total = e.count;
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
  }
}
</script>

<style scoped>
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
