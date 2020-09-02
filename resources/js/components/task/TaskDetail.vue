<template>
  <v-app id="insipre">
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
          <router-link :to="`/detail/${getClassId}/${getSubject.split(' ').join('-')}`"
                       style="color: white; text-decoration: none"><span class="link">{{ pageTitle }}</span>
          </router-link>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <div style="margin-right: 10px">
          <chat></chat>
        </div>
        <div style="margin-right: 20px">
          <announcement></announcement>
        </div>
        <account></account>
        <template v-if="checkGuard === 'employee'" v-slot:extension>
          <v-tabs
            v-model="tabs"
            centered
          >
            <v-tab v-for="item in items" :key="item.tab">
              {{ item.tab }}
            </v-tab>
            <v-tabs-slider color="pink"></v-tabs-slider>
          </v-tabs>
        </template>
      </v-toolbar>
      <v-fab-transition>
        <v-tabs-items v-model="tabs">
          <v-tab-item v-for="item in items" :key="item.tab">
            <v-card flat>
              <v-card-text>
                <component :is="item.content"></component>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs-items>
      </v-fab-transition>
    </v-card>
    <!-- sidebar -->
    <sidebar v-model="openLeftNavigationDrawer"></sidebar>

    <!-- only student -->
    <div v-if="checkGuard === 'student'">
      <v-row>
        <v-col md="9" sm="12">
          <task-instruction></task-instruction>
        </v-col>
        <v-col md="3" sm="12" class="my-task">
          <v-card
            class="mx-auto"
            max-width="344"
            outlined
          >
            <v-card-title style="margin-left: -5px">Tugas Anda</v-card-title>
            <p :class="convertClassName(status)">{{ status }}</p>
            <v-tooltip v-if="selectedFile.length !== 0" bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-chip
                  v-bind="attrs"
                  v-on="on"
                  class="ma-2"
                  color="purple"
                  text-color="white"
                  @click="openFile(url)"
                  style="margin-left: 10px"
                >
                  <v-avatar left>
                    <v-icon>mdi-download-circle</v-icon>
                  </v-avatar>
                  {{ splitFilename(selectedFile.name) }}
                </v-chip>
              </template>
              <span>Klik untuk membuka</span>
            </v-tooltip>
            <v-card-actions>
              <v-btn
                color="purple"
                text
                v-show="checkImage === true"
                :loading="isSelecting"
                @click="onButtonClick"
              >
                Tambah File
              </v-btn>
              <input
                id="upload-file"
                ref="uploader"
                class="d-none"
                type="file"
                @change="onFileChanged"
              >
              <v-btn
                v-if="checkImage === true"
                color="indigo"
                text
                :disabled="disabledButton"
                @click="sendTask"
              >
                {{ changeTextSend }}
              </v-btn>
              <v-btn
                v-else
                color="indigo"
                text
                :disabled="disabledButton"
                @click="cancelSendTask"
              >
                {{ changeTextCancel }}
              </v-btn>
            </v-card-actions>
          </v-card>
          <br>
          <v-card
            class="mx-auto"
            max-width="344"
            outlined
            v-if="showScore === 2"
          >
            <h3 class="title-score">Nilai Tugas Anda</h3>
            <p :class="changeScoreColor(score)">{{ score }}</p>
          </v-card>
        </v-col>
      </v-row>
    </div>
    <vue-confirm-dialog></vue-confirm-dialog>
  </v-app>
</template>

<script>
  import Sidebar from '../layouts/Sidebar';
  import Account from '../other/Account';
  import TaskInstruction from './TaskInstruction';
  import Chat from "../chat/Chat";
  import StudentTask from './StudentTask';
  import {mapActions, mapGetters} from "vuex";
  import Announcement from "../other/Announcement";

  export default {
    name: "TaskDetail",
    components: {
      Announcement,
      Sidebar,
      Account,
      TaskInstruction,
      StudentTask,
      Chat
    },
    data: () => ({
      openLeftNavigationDrawer: false,
      isSelecting: false,
      checkImage: true,
      disabledButton: true,
      showScore: true,
      tabs: null,
      score: 0,
      changeTextSend: 'Kirim Tugas',
      changeTextCancel: 'Batalkan Kirim',
      url: '',
      status: '',
      selectedFile: [],
      items: [
        {tab: 'Petunjuk', content: 'TaskInstruction'},
        {tab: 'Tugas Siswa', content: 'StudentTask'},
      ]
    }),
    mounted() {
      document.title = this.getSubject;
      this.getStudentTask();
      let pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
        cluster: process.env.MIX_PUSHER_APP_CLUSTER
      });
      let channel = pusher.subscribe('my-channel');
      channel.bind('my-event', (data) => {
        const user = JSON.parse(this.getUser);
        this.showScore = data;
        if (user.username === data.username) {
          this.score = data.score;
        }
      });
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

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      }
    },
    methods: {
      ...mapActions({
        setAlert: "setAlert"
      }),

      convertClassName(status) {
        if (status === 'Belum Menyerahkan') {
          return 'unSubmit-task';
        } else if (status === 'Terlambat') {
          return 'late-task';
        } else {
          return 'submit-task';
        }
      },

      changeScoreColor(score) {
        if (score >= 80 && score <= 100) {
          return 'perfect-score';
        } else if (score >= 60 && score < 80) {
          return 'medium-score';
        } else {
          return 'low-score';
        }
      },

      splitFilename(filename) {
        const length = filename.length;
        if (length > 30) {
          return filename.substr(0, 30) + ' ...';
        } else {
          return filename
        }
      },

      onButtonClick() {
        this.isSelecting = true
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
        this.selectedFile = selectedFiles[0];
        this.disabledButton = false;
      },

      urlToFile: function (url, filename, mimeType) {
        return (fetch(url, {
            mode: "no-cors",
          })
            .then(function (res) {
              return res.arrayBuffer();
            })
            .then(function (buf) {
              return new File([buf], filename, {type: mimeType});
            })
        );
      },

      getStudentTask: function () {
        const taskId = this.$route.params.id;
        axios.post('/task/student', {
          task_id: taskId
        })
          .then(response => {
            if (response.data.status === 200) {
              const data = response.data.data;
              const image = response.data.image;
              if (data != null) {
                this.statusTask(data.status);
                this.showScore = data.task.show_score;
                this.score = data.score;
                if (data.task_file != null) {
                  this.checkImage = false;
                  this.disabledButton = false;
                  this.url = image.url;
                  this.urlToFile(image.url, image.filename, image.mime)
                    .then(file => {
                      this.selectedFile = file;
                    });
                }
              }
            }
          })
      },

      sendTask: function () {
        this.disabledButton = true;
        this.changeTextSend = 'Loading...';
        const taskId = this.$route.params.id;
        const formData = new FormData();
        formData.append('task_id', taskId);
        formData.append('file', this.selectedFile);

        axios.post('/task/send', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
          .then(response => {
            this.disabledButton = true;
            this.changeTextSend = 'Kirim Tugas';
            this.setAlert({
              message: response.data.message,
              status: response.data.status,
            });
            if (response.data.status === 200) {
              this.dialog = false;
              setTimeout(function () {
                location.reload();
              }, 2000);
            }
          })
          .catch(resp => {
            this.disabledButton = false;
            this.changeTextSend = 'Kirim Tugas';
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, function (val) {
                alert(val[0]);
              });
            }
          })
      },

      cancelSendTask: function () {
        this.$confirm({
          title: 'Apakah anda yakin ?',
          message: 'Tugas yang dibatalkan harus dikirim ulang.',
          button: {
            yes: 'Ya',
            no: 'Batal'
          },
          callback: confirm => {
            if (confirm) {
              this.disabledButton = true;
              this.changeTextCancel = 'Loading...';
              const taskId = this.$route.params.id;

              axios.delete('/task/cancel', {
                params: {
                  task_id: taskId
                }
              })
                .then(response => {
                  this.disabledButton = true;
                  this.changeTextCancel = 'Batalkan Kirim';
                  this.setAlert({
                    message: response.data.message,
                    status: response.data.status,
                  });
                  if (response.data.status === 200) {
                    this.dialog = false;
                    setTimeout(function () {
                      location.reload();
                    }, 2000);
                  }
                })
                .catch(resp => {
                  this.disabledButton = false;
                  this.changeTextCancel = 'Batalkan Kirim';
                  alert(resp.response.data.message);
                })
            }
          }
        });
      },

      statusTask: function (data) {
        switch (data) {
          case 0:
            this.status = 'Belum menyerahkan';
            break;
          case 1:
            this.status = 'Terlambat';
            break
          case 2:
            this.status = 'Diserahkan';
            break;
        }
      },

      openFile: function (url) {
        if (this.checkImage === false) {
          window.open(url, '_blank');
        }
      },
    }
  }
</script>

<style scoped>
  .link:hover {
    text-decoration: underline;
  }

  .my-task {
    right: 15px;
    top: 23px;
    position: relative;
  }

  .title-score {
    text-align: center;
    margin-top: 10px;
  }

  .submit-task {
    margin-right: 10px;
    font-size: 13px;
    float: right;
    margin-top: -42px;
    color: #00a000;
  }

  .late-task {
    margin-right: 10px;
    font-size: 13px;
    float: right;
    margin-top: -42px;
    color: #FB8C00;
  }

  .unSubmit-task {
    margin-right: 10px;
    font-size: 13px;
    float: right;
    margin-top: -42px;
    color: #F44336;
  }

  .perfect-score {
    text-align: center;
    font-size: 50px;
    font-weight: bold;
    color: #00a000;
  }

  .medium-score {
    text-align: center;
    font-size: 50px;
    font-weight: bold;
    color: #FB8C00;
  }

  .low-score {
    text-align: center;
    font-size: 50px;
    font-weight: bold;
    color: #F44336;
  }
</style>
