<template>
  <div>
    <v-btn
      style="margin-top: 10px"
      text
      small
      color="primary"
      @click="openTextArea"
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
    <div id="discussion-box">
      <div
        style="margin-top: -10px"
        v-if="discussionList.length > 0"
        v-for="(discussion, i) in discussionList"
        :key="i"
      >
        <v-col>
          <v-avatar v-if="discussion.employee_id != null" size="36px" :color="discussion.employee.color">
          <span v-if="discussion.employee.photo == null" class="white--text">{{
              discussion.employee.name.substr(0, 2)
            }}</span>
            <img v-else alt="Avatar" :src="`/storage/${discussion.employee.photo}`">
          </v-avatar>
          <v-avatar v-else size="36px" :color="discussion.student.color">
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
                <v-list-item @click="openModalUpdate(discussion.id)">
                  <v-list-item-title>Edit</v-list-item-title>
                </v-list-item>
                <v-list-item @click="deleteDiscussion(discussion.id)">
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
    </div>
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Edit Diskusi</v-card-title>
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
            @keydown.enter.exact.prevent="updateDiscussion"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <vue-confirm-dialog></vue-confirm-dialog>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import AnswerDiscussion from "./AnswerDiscussion";

export default {
  name: "Discussion",
  components: {AnswerDiscussion},
  props: ['materialId'],
  data() {
    return {
      message: '',
      messageUpdate: '',
      type: 'create',
      discussionId: '',
      total: 0,
      discussionList: [],
      showBtnLoadMore: true,
      isShow: false,
      disableTextDiscussion: false,
      disableUpdateTextDiscussion: false,
      dialog: false,
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
    ...mapActions({
      setAlert: 'setAlert'
    }),

    getDiscussion() {
      axios.get('/e-learning/load/discussion', {
        params: {
          material_id: this.materialId,
          class_id: this.getClassId
        }
      }).then(response => {
        const data = response.data.discussion;
        const mergeData = data.map(data => ({
            ...data,
            visible: false
          })
        );
        this.discussionList.push(...mergeData);
        this.changeHeight();
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    changeHeight() {
      const box = document.getElementById('discussion-box');
      console.log(this.discussionList.length);
      if (this.discussionList.length > 4) {
        box.style.height = '500px';
        box.style.overflow = 'auto';
      } else {
        box.style.height = 'auto';
      }
    },

    openTextArea() {
      this.isShow = !this.isShow;
      this.type = 'create';
    },

    makeDiscussion(materialId) {
      if (this.message !== '') {
        this.disableTextDiscussion = true;
        axios.post('/e-learning/discussion', {
          message: this.message,
          material_id: materialId,
          class_id: this.getClassId
        }).then(response => {
          const discussion = response.data.discussion;
          this.disableTextDiscussion = false;
          if (response.data.status === 200) {
            this.message = '';
            this.discussionList.push(discussion);
            this.discussionList.reverse();
            this.changeHeight();
          } else {
            this.setAlert({
              message: response.data.message,
              status: response.data.status
            });
          }
        })
          .catch(resp => {
            this.disableTextDiscussion = false;
            alert(resp.response.data.message);
          });
      }
    },

    openModalUpdate(discussionId) {
      this.type = 'update';
      this.dialog = true;
      this.discussionList.map(item => {
        if (item.id === discussionId) {
          this.messageUpdate = item.message;
        }
      });
      this.discussionId = discussionId;
    },

    updateDiscussion() {
      if (this.messageUpdate !== '') {
        this.disableUpdateTextDiscussion = true;
        axios.put('/e-learning/update/discussion', {
          message: this.messageUpdate,
          discussion_id: this.discussionId
        }).then(response => {
          const discussion = response.data.discussion;
          this.disableUpdateTextDiscussion = false;
          if (response.data.status === 200) {
            this.messageUpdate = '';
            this.dialog = false;
            this.discussionList.map(item => {
              if (item.id === discussion.id) {
                item.message = discussion.message;
              }
            });
          } else {
            this.setAlert({
              message: response.data.message,
              status: response.data.status
            });
          }
        })
          .catch(resp => {
            this.disableUpdateTextDiscussion = false;
            alert(resp.response.data.message);
          });
      }
    },

    listenForNewDiscussion() {
      Echo.join('class.' + this.getClassId)
        .listen('DiscussionEvent', (e) => {
          if (e.type === 'create') {
            this.discussionList.push(e.discussion);
            this.discussionList.reverse();
          } else {
            this.discussionList.map(item => {
              if (item.id === e.discussion.id) {
                item.message = e.discussion.message;
              }
            });
          }

          if ((this.discussionList.length + 1) > 4) {
            document.getElementById('discussion-box').style.height = '500px';
            document.getElementById('discussion-box').style.overflow = 'auto';
          }
        });
    },

    deleteDiscussion: function (id) {
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: 'Data yang dihapus tidak bisa dikembalikan.',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            axios.delete('/e-learning/delete/discussion', {
              params: {
                discussion_id: id
              }
            }).then(response => {
              if (response.data.status === 200) {
                const index = this.discussionList.findIndex(function (params) {
                  return params.id === id;
                });
                if (index !== -1) this.discussionList.splice(index, 1);
              }
            })
          }
        }
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
