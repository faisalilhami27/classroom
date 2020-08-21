<template>
  <v-app id="inspire">
    <v-container class="container">
      <v-layout>
        <v-img
          :src="image"
          aspect-ratio="3.3"
          class="image"
        >
          <h1 class="subject">{{ subject }}</h1>
          <div class="clearfix"></div>
          <div class="class">
            <p>Kelas {{ className }}</p>
            <p class="school-year">Tahun Ajaran {{ schoolYear }}</p>
            <p class="code" v-if="checkGuard === 'employee'">
              Kode Kelas : {{ code }}
              <v-btn
                color="white"
                class="icon"
                @click.stop="dialog = true"
                icon>
                <v-icon>fullscreen</v-icon>
              </v-btn>
            </p>
            <p style="margin-top: -20px; font-size: 15px" v-if="checkGuard === 'employee'">
              Video Conference :
              <a @click="this.getMeeting" class="link-video" href="#">
                <span class="text-meeting" @mouseover="mouseOver" @mouseout="mouseOut">Buat Meeting</span>
                <v-avatar @mouseover="mouseOver" @mouseout="mouseOut"
                          style="background-color: #2D8CFF; margin-left: 3px;" size="22">
                  <v-icon dark size="15">mdi-video</v-icon>
                </v-avatar>
              </a>
            </p>
            <p style="margin-top: -10px; font-size: 15px" v-else>
              <a @click="this.getMeeting" class="link-video" href="#">
                <span class="text-meeting" @mouseover="mouseOver" @mouseout="mouseOut">Link Meeting</span>
                <v-avatar @mouseover="mouseOver" @mouseout="mouseOut"
                          style="background-color: #2D8CFF; margin-left: 3px;" size="22">
                  <v-icon dark size="15">mdi-video</v-icon>
                </v-avatar>
              </a>
            </p>
          </div>
        </v-img>
      </v-layout>
      <v-row class="row" justify="space-around">
        <v-col cols="3" class="d-none d-md-flex">
          <v-card
            class="pa-2 task"
            outlined
            :height="changeHeight"
          >
            <p class="title-task" style="margin-bottom: 5px; font-weight: bold">Tugas Mendatang</p>

            <div v-if="showTask">
              <p style="margin-top: 30px; text-align: center">Tidak ada tugas yang perlu segera diselesaikan</p>
            </div>
            <div v-else v-for="(task, index) in dataTask" :key="index">
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title style="font-size: 14px">Batas waktu : {{ task.date }}</v-list-item-title>
                  <v-list-item-subtitle link style="font-size: 12px">{{ task.time }} - {{
                      splitTaskName(task.title, 20)
                    }}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
              <v-divider v-if="(dataTask.length - 1) !== index"></v-divider>
            </div>
          </v-card>
        </v-col>
        <v-col
          cols="12"
          md="9"
          sm="12"
          xs="12">
          <v-card
            class="pa-2 share"
            outlined
            id="show"
            v-show="beforePosting"
            @click="beforePosting = false"
          >
            <v-row>
              <v-col
                sm="12"
                md="12"
              >
                <v-avatar :color="color" class="avatar">
                  <span v-if="!photo" class="white--text">{{ avatar }}</span>
                  <img
                    v-else
                    :src="avatar"
                    :alt="avatar"
                  >
                </v-avatar>
                <p style="margin-left: 65px; margin-top: -35px">Bagikan sesuatu di kelas anda...</p>
              </v-col>
            </v-row>
          </v-card>
          <v-card
            v-if="!beforePosting"
            id="show-posting"
          >
            <v-col cols="12">
              <v-textarea
                @keyup="checkTitle"
                v-model="title"
                filled
                label="Bagikan di kelas"
              ></v-textarea>
              <p style="color: red; margin-top: -30px">
                <strong id="title-error"></strong>
              </p>
            </v-col>
            <v-col cols="12" class="show-file">
              <v-card
                class="pa-2 list-file"
                outlined
                tile
                v-show="showImage"
                v-for="(item, index) in selectedFile"
                :key="index"
              >
                <p class="file-name">{{ item.name }}</p>
                <v-btn
                  icon
                  class="float-right close"
                  @click.prevent="deleteFile(index)"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card>
            </v-col>
            <v-col sm="12" style="margin-top: -30px">
              <v-btn
                outlined
                class="text-none"
                :color="getColor"
                depressed
                :loading="isSelecting"
                @click="onButtonClick"
              >
                <v-icon left>mdi-paperclip</v-icon>
                Tambahkan
              </v-btn>
              <input
                id="upload-file"
                ref="uploader"
                class="d-none"
                type="file"
                multiple
                @change="onFileChanged"
              >
              <div class="btn-right">
                <v-btn
                  @click.prevent="savePosting"
                  :disabled="disabledButton"
                  :color="getColor"
                  style="color: #fff"
                  class="float-right btn-posting"
                >
                  {{ changeTextPosting }}
                </v-btn>
                <v-btn
                  outlined
                  color="red"
                  class="float-right btn-cancel"
                  @click="cancelPosting"
                >
                  Batal
                </v-btn>
              </div>
              <div class="clearfix"></div>
            </v-col>
          </v-card>
          <div
            v-for="(item, index) in dataPosting"
            :key="index"
          >
            <v-card
              @click="commentPosting(item.id)"
              v-if="item.type_post == 1"
              class="pa-2"
              outlined
              style="margin-top: 30px; box-shadow: 0 6px 6px rgba(0,0,0,0.2)"
            >
              <v-row>
                <v-col
                  sm="12"
                  md="12"
                >
                  <v-avatar v-if="item.employee_id != null" :color="item.employee.color" class="avatar">
                    <span v-if="item.employee.photo == null"
                          class="white--text">{{ item.employee.name.substr(0, 2) }}</span>
                    <img
                      v-else
                      :src="'/storage/' + item.employee.photo"
                      alt=""
                    >
                  </v-avatar>
                  <v-avatar v-else :color="item.student.color" class="avatar">
                    <span v-if="item.student.photo == null"
                          class="white--text">{{ item.student.name.substr(0, 2) }}</span>
                    <img
                      v-else
                      :src="'/storage/' + item.student.photo"
                      alt=""
                    >
                  </v-avatar>
                  <p v-if="item.employee_id != null" style="margin-left: 65px; margin-top: -50px">{{
                      item.employee.name
                    }}</p>
                  <p v-else style="margin-left: 65px; margin-top: -50px">{{ item.student.name }}</p>
                  <p style="margin-left: 65px; margin-top: -20px; font-size: 11px; color: #797979">{{
                      item.date |
                        convertFormatDatetimeToTime
                    }}</p>
                  <v-menu offset-y>
                    <template v-slot:activator="{ on }">
                      <v-btn v-if="checkGuard === 'employee'" v-on="on" class="btn-action" icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                      <v-btn v-if="getUserId == item.student_id" v-on="on" class="btn-action" icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <div v-if="checkGuard === 'employee'">
                        <v-list-item v-if="getUserId == item.employee_id" @click="editPosting(item.id)">
                          <v-list-item-title>Edit</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deletePosting(item.id)">
                          <v-list-item-title>Hapus</v-list-item-title>
                        </v-list-item>
                      </div>
                      <div v-else>
                        <v-list-item @click="editPosting(item.id)">
                          <v-list-item-title>Edit</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deletePosting(item.id)">
                          <v-list-item-title>Hapus</v-list-item-title>
                        </v-list-item>
                      </div>
                    </v-list>
                  </v-menu>
                  <div class="clearfix"></div>
                </v-col>
              </v-row>
              <v-col cols="12" style="margin-top: -10px">
                <p style="text-align: justify">{{ item.title }}</p>
              </v-col>
              <v-col
                v-if="item.get_images !== null"
                cols="12" style="margin-top: -60px">
                <v-row>
                  <v-col
                    v-for="(image, i) in item.get_images"
                    :key="i"
                    cols="12"
                    md="6"
                    sm="12"
                  >
                    <v-card
                      class="pa-2 link-image"
                      outlined
                      tile
                      @click="openFile(image.file)"
                    >
                      <p class="file-name">{{ image.filename }}</p>
                    </v-card>
                  </v-col>
                </v-row>
              </v-col>
            </v-card>
            <div v-else>
              <div v-if="checkGuard === 'employee'">
                <v-card
                  @click="commentTask(item.task.id, item.id)"
                  class="pa-2"
                  outlined
                  height="70"
                  style="margin-top: 30px; box-shadow: 0 6px 6px rgba(0,0,0,0.2);"
                >
                  <v-row>
                    <v-col
                      sm="12"
                      md="12"
                    >
                      <v-avatar class="avatar" color="indigo">
                        <v-icon dark>mdi-calendar-text</v-icon>
                      </v-avatar>
                      <p style="margin-left: 65px; margin-top: -40px">{{ item.employee.name }} memposting tugas baru :
                        {{ splitTaskName(item.title, 30) }}</p>
                      <p style="margin-left: 65px; margin-top: -20px; font-size: 11px; color: #797979">
                        {{ item.date | convertFormatDatetimeToTime }}</p>
                    </v-col>
                  </v-row>
                </v-card>
              </div>
              <div v-else>
                <v-card
                  v-if="item.task.student_task != null"
                  @click="commentTask(item.task.id, item.id)"
                  class="pa-2"
                  outlined
                  height="70"
                  style="margin-top: 30px; box-shadow: 0 6px 6px rgba(0,0,0,0.2);"
                >
                  <v-row>
                    <v-col
                      sm="12"
                      md="12"
                    >
                      <v-avatar class="avatar" :color="item.employee.color">
                        <v-icon dark>mdi-calendar-text</v-icon>
                      </v-avatar>
                      <p style="margin-left: 65px; margin-top: -40px">{{ item.employee.name }} memposting tugas baru :
                        {{ splitTaskName(item.title, 30) }}</p>
                      <p style="margin-left: 65px; margin-top: -20px; font-size: 11px; color: #797979">
                        {{ item.date | convertFormatDatetimeToTime }}</p>
                    </v-col>
                  </v-row>
                </v-card>
              </div>
            </div>
          </div>
          <infinite-loading @infinite="infiniteHandler">
            <span slot="no-more"></span>
          </infinite-loading>
        </v-col>
      </v-row>
    </v-container>
    <div class="text-center">
      <v-dialog
        v-model="dialog"
        width="500"
      >
        <v-card>
          <v-card-title
            class="headline justify-center title-code lighten-2"
            primary-title
          >
            Kode Kelas
          </v-card-title>

          <v-card-text class="show-code">
            <span class="text-code">{{ code }}</span>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              text
              @click="dialog = false"
            >
              Close
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <div class="text-center">
      <v-dialog
        v-model="dialogUpdate"
        persistent
        max-width="700px"
      >
        <v-card>
          <v-card-title
            class="headline justify-center title-code lighten-2"
            primary-title
          >
            Edit Postingan
          </v-card-title>

          <v-card-text>
            <v-col cols="12">
              <input type="hidden" name="id" id="id" v-model="id">
              <v-textarea
                @keyup="checkTitleUpdate"
                v-model="titleUpdate"
                filled
                label="Bagikan di kelas"
              ></v-textarea>
              <p style="color: red; margin-top: -30px">
                <strong id="title_update-error"></strong>
              </p>
            </v-col>
            <v-col cols="12" class="show-file">
              <v-card
                class="pa-2 list-file"
                outlined
                tile
                v-show="showImage"
                v-for="(item, index) in selectedFile"
                :key="index"
              >
                <p class="file-name">{{ item.name }}</p>
                <v-btn
                  icon
                  class="float-right close"
                  @click.prevent="deleteFile(index)"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card>
            </v-col>
            <v-col sm="12" style="margin-top: -30px">
              <v-btn
                outlined
                class="text-none"
                :color="getColor"
                depressed
                :loading="isSelecting"
                @click="onButtonClick"
              >
                <v-icon left>mdi-paperclip</v-icon>
                Tambahkan
              </v-btn>
              <input
                ref="uploader"
                class="d-none"
                type="file"
                multiple
                @change="onFileChanged"
              >
              <div class="btn-right">
                <v-btn
                  @click.prevent="savePosting"
                  :disabled="disabledButtonUpdate"
                  :color="getColor"
                  style="color: #fff"
                  class="float-right btn-posting"
                >
                  {{ changeTextPosting }}
                </v-btn>
                <v-btn
                  outlined
                  color="red"
                  class="float-right btn-cancel"
                  @click="cancelPostingUpdate"
                >
                  Batal
                </v-btn>
              </div>
              <div class="clearfix"></div>
            </v-col>
          </v-card-text>
        </v-card>
      </v-dialog>
      <div class="text-center">
        <v-dialog
          v-model="dialogMeeting"
          max-width="800"
        >
          <v-card>
            <v-card-title
              class="headline justify-center title-code lighten-2"
              primary-title
            >
              Video Conference
            </v-card-title>

            <v-card-text>
              <table cellpadding="5" cellspacing="5" style="margin-top:10px; color: black">
                <tr>
                  <td>Link Meeting</td>
                  <td>:</td>
                  <td><a :href="urlMeeting" target="_blank">{{ urlMeeting }}</a></td>
                </tr>
                <tr>
                  <td>Meeting ID</td>
                  <td>:</td>
                  <td>{{ meetingId }}</td>
                </tr>
                <tr>
                  <td>Password</td>
                  <td>:</td>
                  <td>{{ passwordMeeting }}</td>
                </tr>
              </table>
              <div style="color: black" v-if="checkGuard === 'employee'">
                <p style="margin: 10px 10px; margin-left: 5px">Note :</p>
                <ol style="margin-left: -5px">
                  <li>Link hanya bisa digunakan satu kali dan berlaku selama satu jam</li>
                  <li>Setelah berhasil men-generate link, maka link otomatis akan tampil di halaman siswa</li>
                  <li>Pastikan guru sudah login di aplikasi zoom dengan akun email yang sama yang digunakan di aplikasi
                    classroom ini
                  </li>
                  <li>Apabila email berbeda dengan yang digunakan di aplikasi classroom ini maka tidak bisa memulai
                    meeting
                  </li>
                  <li>Apabila telah selesai melakukan meeting, maka harus menekan tombol tandai selesai</li>
                </ol>
              </div>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions v-if="checkGuard === 'employee'">
              <v-spacer></v-spacer>
              <v-btn
                color="red"
                text
                @click="dialogMeeting = false"
              >
                Close
              </v-btn>
              <v-btn
                :disabled="disabledButtonMeeting"
                :color="getColor"
                text
                @click.prevent="createMeeting"
              >
                {{ changeText }}
              </v-btn>
              <v-btn
                :disabled="disabledButtonDone"
                color="success"
                text
                @click.prevent="meetingDone"
              >
                Tandai Selesai
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
    </div>
    <vue-confirm-dialog></vue-confirm-dialog>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import moment from "moment";

export default {
  name: "Forum",
  data: function () {
    return {
      subject: '',
      className: '',
      schoolYear: '',
      image: '',
      code: '',
      id: '',
      title: '',
      titleUpdate: '',
      photoUser: '',
      name: '',
      urlMeeting: '',
      meetingId: '',
      passwordMeeting: '',
      page: 1,
      changeTextPosting: 'Posting',
      changeText: 'Generate',
      changeTextDone: 'Tandai Selesai',
      guard: false,
      photo: false,
      dialog: false,
      dialogMeeting: false,
      dialogUpdate: false,
      selectedFile: [],
      dataPosting: [],
      dataTask: [],
      beforePosting: true,
      disabledButtonDone: true,
      disabledButtonMeeting: false,
      isSelecting: false,
      showImage: false,
      showTask: false,
      disabledButton: true,
      disabledButtonUpdate: true
    }
  },
  mounted() {
    this.getClass();
    this.getClosestTask();
    let pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
      cluster: process.env.MIX_PUSHER_APP_CLUSTER
    });
    let channel = pusher.subscribe('my-channel');
    channel.bind('my-event', () => {
      this.getDataPosting();
    });
  },
  filters: {
    convertFormatDatetimeToTime: function (datetime) {
      return moment(datetime).format('DD-MM-YYYY HH:mm');
    }
  },
  computed: {
    ...mapGetters([
      'getUser',
      'getClassId',
      'getColor'
    ]),

    changeHeight: function () {
      if (this.dataTask.length === 2) {
        return 160;
      } else if (this.dataTask.length === 1) {
        return 92;
      } else {
        return 130;
      }
    },

    checkGuard: function () {
      const user = JSON.parse(this.getUser);
      return user.guard;
    },

    avatar: function () {
      const user = JSON.parse(this.getUser);
      if (user.photo == null) {
        return user.name.substr(0, 2);
      } else {
        this.photo = true;
        return '/storage/' + user.photo;
      }
    },

    color: function () {
      const user = JSON.parse(this.getUser);
      return user.color;
    },

    getName: function () {
      const user = JSON.parse(this.getUser);
      return user.name;
    },

    getUserId: function () {
      const user = JSON.parse(this.getUser);
      return user.user_id;
    }
  },
  methods: {
    ...mapActions({
      setAlert: 'setAlert'
    }),

    splitTaskName: function (name, length) {
      if (name.length > length) {
        return name.substr(0, length) + ' ...';
      } else {
        return name;
      }
    },

    getDataPosting: function () {
      const id = this.getClassId;
      axios.get('/posting/get', {
        params: {
          class_id: id
        }
      }).then(response => {
        this.dataPosting = response.data.data.data;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        })
    },

    getClass: function () {
      const id = this.getClassId;
      axios.get('/class/get', {
        params: {
          id: id
        }
      }).then(response => {
        if (response.data.status === 200) {
          const data = response.data.data;
          this.subject = data.subject.name;
          this.image = '/images/' + data.image;
          this.className = data.class_order;
          this.code = data.code;
          this.schoolYear = data.school_year.early_year + '/' + data.school_year.end_year;
        } else {
          this.setAlert({
            message: response.data.message,
            status: response.data.status
          });
        }
      })
        .catch(resp => {
          alert(resp.response.data.message);
        })
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
      for (let i = 0; i < selectedFiles.length; i++) {
        this.selectedFile.push(selectedFiles[i]);
      }
      this.showImage = true;
    },

    cancelPosting: function () {
      this.beforePosting = !this.beforePosting
      this.selectedFile.splice(0, this.selectedFile.length);
      this.$refs.uploader.value = null;
    },

    cancelPostingUpdate: function () {
      this.dialogUpdate = false;
      this.selectedFile.splice(0, this.selectedFile.length);
      this.$refs.uploader.value = null;
    },

    getClosestTask: function () {
      const classId = this.getClassId;
      axios.get('/posting/get/task', {
        params: {
          class_id: classId
        }
      }).then(response => {
        const data = response.data.data;
        this.dataTask = data;
        this.showTask = data.length === 0;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        })
    },

    mouseOver: function () {
      document.querySelector('.text-meeting').style.textDecoration = 'underline';
    },

    mouseOut: function () {
      document.querySelector('.text-meeting').style.textDecoration = 'none';
    },

    savePosting: function () {
      this.changeTextPosting = 'Loading...';
      this.disabledButton = true;
      const user = JSON.parse(this.getUser);
      const classId = this.getClassId;
      let title = null;
      let urlSave = null;
      let id = null;
      let formData = new FormData();

      for (let i = 0; i < this.selectedFile.length; i++) {
        let file = this.selectedFile[i];
        formData.append('file[]', file);
      }

      if (!this.dialogUpdate) {
        title = this.title;
        urlSave = '/posting/create';
      } else {
        title = this.titleUpdate;
        id = this.id;
        urlSave = '/posting/update';
      }

      formData.append('user_id', user.user_id);
      formData.append('class_id', classId);
      formData.append('title', title);
      formData.append('id', id);

      axios.post(urlSave, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(response => {
          this.changeTextPosting = 'Posting';
          this.disabledButton = false;
          this.setAlert({
            message: response.data.message,
            status: response.data.status,
          });
          if (response.data.status === 200) {
            this.dialogUpdate = false;
            setTimeout(function () {
              location.reload();
            }, 2000);
          }
        })
        .catch(resp => {
          this.changeTextPosting = 'Posting';
          this.disabledButton = false;
          if (_.has(resp.response.data, 'errors')) {
            _.map(resp.response.data.errors, function (val, key) {
              $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
            })
          }
          alert(resp.response.data.message)
        })
    },

    getMeeting: function () {
      this.dialogMeeting = true;
      const classId = this.getClassId;
      axios.post('/zoom/get/meeting', {
        class_id: classId
      })
        .then(response => {
          if (response.data.status === 200) {
            const data = response.data.data;
            if (data != null) {
              this.disabledButtonMeeting = true;
              this.disabledButtonDone = false;
              this.urlMeeting = data.url;
              this.meetingId = data.meeting_id;
              this.passwordMeeting = data.password;
            } else {
              this.disabledButtonDone = true;
              this.disabledButtonMeeting = false;
              this.urlMeeting = '';
              this.meetingId = '';
              this.passwordMeeting = '';
            }
          }
        })
        .catch(resp => {
          alert(resp.response.data.message)
        });
    },

    createMeeting: function () {
      this.$confirm({
        title: 'Apakah anda akan men-generate link ?',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            this.changeText = 'Loading...';
            this.disabledButtonMeeting = true;
            const classId = this.getClassId;
            axios.post('/zoom/create/meeting', {
              class_id: classId
            })
              .then(response => {
                this.disabledButtonMeeting = true;
                this.disabledButtonDone = false;
                this.changeText = 'Generate';
                if (response.data.status === 200) {
                  const data = response.data.data;
                  this.urlMeeting = data.url;
                  this.meetingId = data.meeting_id;
                  this.passwordMeeting = data.password;
                } else {
                  this.$toast.error(response.data.message, {timeout: false})
                }
              })
              .catch(resp => {
                this.disabledButtonMeeting = false;
                this.changeText = 'Generate';
                alert(resp.response.data.message)
              });
          }
        }
      });
    },

    meetingDone: function () {
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: 'Data yang sudah diubah tidak bisa dikembalikan',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            this.changeTextDone = 'Loading...';
            this.disabledButtonDone = true;
            const classId = this.getClassId;
            axios.delete('/zoom/delete/meeting', {
              params: {
                class_id: classId
              }
            })
              .then(response => {
                this.dialogMeeting = false;
                this.disabledButtonDone = false;
                this.changeTextDone = 'Tandai Selesai';
                this.setAlert({
                  message: response.data.message,
                  status: response.data.status
                });
                if (response.data.status === 200) {
                  setTimeout(function () {
                    location.reload();
                  }, 2000);
                }
              })
              .catch(resp => {
                this.disabledButtonDone = false;
                this.changeTextDone = 'Tandai Selesai';
                alert(resp.response.data.message)
              });
          }
        }
      });
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

    deleteFile: function (index) {
      this.selectedFile.splice(index, 1);
    },

    editPosting: function (id) {
      this.dialogUpdate = true;
      this.disabledButtonUpdate = false;
      axios.get('/posting/edit', {
        params: {
          id: id
        }
      }).then(resp => {
        const data = resp.data.data;
        const images = resp.data.images;
        this.titleUpdate = data.title;
        this.id = data.id;
        for (let i = 0; i < images.length; i++) {
          this.urlToFile(images[i].url, images[i].filename, images[i].mime)
            .then(file => {
              this.showImage = true;
              this.selectedFile.push(file);
            })
        }
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    deletePosting: function (id) {
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: 'Data yang dihapus tidak bisa dikembalikan.',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            axios.delete('/posting/delete', {
              params: {
                id: id
              }
            }).then(response => {
              this.setAlert({
                message: response.data.message,
                status: response.data.status,
              });
              if (response.data.status === 200) {
                setTimeout(function () {
                  location.reload();
                }, 2000);
              }
            })
              .catch(resp => {
                alert(resp.response.data.message);
              });
          }
        }
      });
    },

    checkTitle: function () {
      this.disabledButton = this.title === ''
    },

    openFile: function (file) {
      window.open('/storage/' + file, '_blank');
    },

    checkTitleUpdate: function () {
      this.disabledButtonUpdate = this.titleUpdate === ''
    },

    commentPosting: function (id) {
      this.$router.push(`/posting/comment/${id}`);
    },

    commentTask: function (id, postingId) {
      this.$router.push(`/task/detail/${id}/${postingId}`);
    },

    infiniteHandler($state) {
      const id = this.getClassId;
      axios.get('/posting/get', {
        params: {
          page: this.page,
          class_id: id
        },
      }).then(({data}) => {
        if (data.data.data.length) {
          this.page += 1;
          this.dataPosting.push(...data.data.data);
          $state.loaded();
        } else {
          $state.complete();
        }
      });
    },
  }
}
</script>

<style scoped>
.subject {
  color: white;
  font-weight: bold;
  margin-left: 20px;
  margin-top: 10px;
  font-size: 40px;
  float: left;
}

.image {
  height: 250px;
  border-radius: 10px;
  margin-top: -10px;
}

.class {
  margin-left: 22px;
  margin-top: -10px;
  float: left;
  font-size: 25px;
  color: white;
}

.school-year {
  margin-top: -20px;
  font-size: 15px;
}

.code {
  margin-top: -10px;
  font-size: 15px;
}

.show-code {
  font-size: 80px;
  margin-top: 30px;
  font-weight: bold;
  margin-bottom: 20px;
  text-align: center;
}

.text-code {
  color: black;
}

.icon {
  margin-left: -7px;
  margin-top: -3px;
}

.container {
  width: 85%;
}

.title-code {
  background-color: #3F51B5;
  color: white;
}

.clearfix {
  clear: both;
}

.row {
  margin-top: 20px;
}

.pa-2 {
  border-radius: 10px;
}

.title-task {
  margin-left: 15px;
}

.clearfix {
  clear: both;
}

.avatar {
  margin-top: -30px;
  margin-left: 5px;
}

.share:hover {
  color: #3F51B5;
}

.share {
  height: 75px;
  cursor: pointer;
  box-shadow: 0 6px 6px rgba(0, 0, 0, 0.2);
}

.text-share {
  margin-top: -15px;
}

.show-file {
  margin-top: -40px;
  margin-bottom: 20px;
}

.btn-right {
  margin-top: -35px;
}

.task {
  height: 200px;
  border-radius: 20px;
}

.close {
  margin-top: -42px;
}

.list-file {
  height: 40px;
  margin-top: 10px;
}

.btn-posting {
  color: white;
  margin-left: 11px;
}

.btn-cancel {
  margin-left: 100px;
}

.link-image:hover {
  color: #3F51B5;
}

#show-posting {
  box-shadow: 0 6px 6px rgba(0, 0, 0, 0.2);
}

.link-video {
  color: white;
  text-decoration: none
}

.text-meeting:hover {
  text-decoration: underline;
}

.btn-action {
  float: right;
  margin-top: -50px;
  position: relative;
}
</style>
