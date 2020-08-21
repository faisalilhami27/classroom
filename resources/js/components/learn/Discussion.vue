<template>
  <div>
    <v-btn
      style="margin-top: 10px"
      text
      small
      color="primary"
      @click="isShow = !isShow"
    >
      Tambah Diskusi
    </v-btn>
    <v-col cols="12" sm="12" md="12">
      <transition name="slide">
        <v-textarea
          :disabled="disableTextDiscussion"
          v-model="message"
          auto-grow
          v-if="isShow"
          solo
          rows="1"
          background-color="#F7F7F7"
          rounded
          placeholder="Ketikan sesuatu..."
          @keydown.enter.exact.prevent="makeDiscussion(materialId)"
        ></v-textarea>
      </transition>
    </v-col>
    <div
      style="margin-top: -10px"
      v-if="discussionList.length > 0"
      v-for="(discussion, i) in discussionList"
      :key="i"
    >
      <v-col>
        <v-avatar v-if="discussion.employee_id != null" size="36px" :color="discussion.employee.color">
          <span v-if="discussion.employee.photo == null" class="white--text">{{ discussion.employee.name.substr(0, 2) }}</span>
          <img v-else alt="Avatar" :src="`/storage/${discussion.employee.photo}`">
        </v-avatar>
        <v-avatar v-else size="36px" :color="discussion.student.color">
          <span v-if="discussion.student.photo == null" class="white--text">{{ discussion.student.name.substr(0, 2) }}</span>
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
            <template v-if="checkGuard === 'employee'" v-slot:activator="{ on }">
              <v-btn v-if="user === discussion.employee_id" v-on="on" icon>
                <v-icon small>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <template v-else v-slot:activator="{ on }">
              <v-btn v-if="user === discussion.student_id" v-on="on" icon>
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
        <answer-discussion :discussion-id="discussion.id" :visible.sync="discussion.visible"></answer-discussion>
      </v-col>
    </div>
    <div v-if="total > limitPerPage">
      <v-col v-if="showBtnLoadMore" cols="12">
        <v-btn block color="primary" @click.prevent="getDiscussion(nextPageUrl)" dark>{{ changeTextLoadMore }}</v-btn>
      </v-col>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import AnswerDiscussion from "./AnswerDiscussion";

export default {
  name: "Discussion",
  components: {AnswerDiscussion},
  props: ['materialId'],
  data() {
    return {
      nextPageUrl: '',
      message: '',
      changeTextLoadMore: 'Load More',
      currentPage: 1,
      limitPerPage: 3,
      total: 0,
      discussionList: [],
      showBtnLoadMore: true,
      isShow: false,
      disableTextAnswer: false,
      disableTextDiscussion: false,
      messageAnswer: '',
    }
  },
  mounted() {
    this.getDiscussion();
    this.listenForNewDiscussion();
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
    getDiscussion(url = '') {
      this.changeTextLoadMore = 'Memuat...';
      axios.get(url ? url : '/e-learning/load/discussion', {
        params: {
          material_id: this.materialId,
          class_id: this.getClassId
        }
      }).then(response => {
        this.changeTextLoadMore = 'Load More';
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
      })
        .catch(resp => {
          this.changeTextLoadMore = 'Load More';
          alert(resp.response.data.message);
        });
    },

    makeDiscussion(materialId) {
      if (this.message !== '') {
        this.disableTextDiscussion = true;
        axios.post('/e-learning/discussion', {
          message: this.message,
          material_id: materialId,
          class_id: this.getClassId
        }).then(response => {
          this.message = '';
          this.disableTextDiscussion = false;
          this.discussionList.push(response.data.discussion);
        })
          .catch(resp => {
            this.disableTextDiscussion = false;
            alert(resp.response.data.message);
          });
      }
    },

    listenForNewDiscussion() {
      Echo.join('class.' + this.getClassId)
        .listen('DiscussionEvent', (e) => {
          this.discussionList.push(e.discussion);
        });
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
</style>
