<template>
  <div>
    <div
      style="margin-top: -10px"
      v-if="discussionList.length > 0"
      v-for="(discussion, i) in discussionList"
      :key="i"
    >
      <v-col>
        <v-avatar v-if="discussion.employee_id != null" size="36px" color="red">
          <span v-if="discussion.employee.photo == null" class="white--text">{{
              discussion.employee.name.substr(0, 2)
            }}</span>
          <img v-else alt="Avatar" :src="`/storage/${discussion.employee.photo}`">
        </v-avatar>
        <v-avatar v-else size="36px" color="red">
          <span v-if="discussion.student.photo == null" class="white--text">{{
              discussion.student.name.substr(0, 2)
            }}</span>
          <img v-else alt="Avatar" :src="`/storage/${discussion.student.photo}`">
        </v-avatar>
        <strong v-if="discussion.employee_id != null" class="name">{{ discussion.employee.name }}</strong>
        <strong v-else class="name">{{ discussion.student.name }}</strong>
        <v-chip
          class="ma-2"
          x-small
          color="purple"
          text-color="white"
        >
          {{ discussion.post_time }}
        </v-chip>
        <div style="float: right;">
          <v-menu offset-y>
            <template v-slot:activator="{ on }">
              <v-btn v-on="on" icon>
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
        <p style="margin-left: 45px">{{ discussion.message }}</p>
        <v-btn
          style="margin-left: 35px; margin-top: -10px"
          text
          x-small
          color="red"
          @click="toggleActive(discussion.id)"
        >
          Balas
        </v-btn>
        <v-col cols="12" sm="12" md="12" style="margin-bottom: -20px">
          <transition name="slide">
            <v-textarea
              :disabled="disableTextAnswer"
              v-model="messageAnswer"
              auto-grow
              v-show="isActive(discussion.id)"
              solo
              rows="1"
              background-color="#F7F7F7"
              rounded
              placeholder="Ketikan sesuatu..."
              @keydown.enter.exact.prevent="answerDiscussion(discussion.id)"
            ></v-textarea>
          </transition>
        </v-col>
        <div v-if="discussion.answer != ''">
          <v-btn
            style="margin-left: 35px; margin-top: -10px"
            text
            x-small
            color="primary"
            @click="discussion.visible = !discussion.visible"
          >
            {{ changeText(discussion.answer.length, discussion.visible) }}
          </v-btn>
          <div v-for="(answer, idx) in discussion.answer" :key="idx">
            <v-col cols="12" sm="12" md="12" style="margin-bottom: -20px">
              <transition name="slide">
                <div v-show="discussion.visible" style="margin-left: 30px">
                  <v-avatar v-if="answer.employee_id != null" size="36px" color="green">
                    <span v-if="answer.employee.photo == null" class="white--text">{{
                        answer.employee.name.substr(0, 2)
                      }}</span>
                    <img v-else alt="Avatar" :src="`/storage/${answer.employee.photo}`">
                  </v-avatar>
                  <v-avatar v-else size="36px" color="green">
                    <span v-if="answer.student.photo == null" class="white--text">{{
                        answer.student.name.substr(0, 2)
                      }}</span>
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
                      <template v-slot:activator="{ on }">
                        <v-btn v-on="on" icon>
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
            </v-col>
          </div>
        </div>
      </v-col>
    </div>
    <div v-if="total > limitPerPage">
      <v-col v-if="showBtnLoadMore" cols="12">
        <v-btn block color="primary" @click.prevent="getDiscussion(nextPageUrl)" dark>Load More</v-btn>
      </v-col>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "Discussion",
  props: ['materialId'],
  data() {
    return {
      nextPageUrl: '',
      currentPage: 1,
      limitPerPage: 3,
      total: 0,
      discussionList: [],
      showBtnLoadMore: true,
      disableTextAnswer: false,
      disableTextDiscussion: false,
      clickBtnAnswer: null,
      messageAnswer: '',
    }
  },
  mounted() {
    this.getDiscussion();
  },
  computed: {
    ...mapGetters([
      'getClassId',
    ])
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

    getDiscussion(url = '') {
      axios.get(url ? url : '/e-learning/load/discussion', {
        params: {
          material_id: this.materialId,
          class_id: this.getClassId
        }
      }).then(response => {
        const data = response.data.discussion.data;
        const mergeData = data.map(data => ({
            ...data,
            visible: false
          })
        );
        this.discussionList.push(...mergeData);
        this.nextPageUrl = response.data.discussion.next_page_url;
        this.currentPage = response.data.discussion.current_page;
        this.total = response.data.discussion.total;
        if (this.currentPage === response.data.discussion.last_page) {
          this.showBtnLoadMore = false;
        }
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
        })
          .catch(resp => {
            this.disableTextAnswer = false;
            alert(resp.response.data.message);
          });
      }
    },
  },
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
