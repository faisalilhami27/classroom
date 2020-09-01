<template>
  <v-app id="inspire">
    <v-container class="container">
      <v-row justify="center">
        <div v-if="checkGuard === 'employee'">
          <v-btn
            rounded
            :color="getColor"
            @click="openModal"
            dark>
            <v-icon>mdi-plus</v-icon>
            Buat Tugas
          </v-btn>
          <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
            <v-card>
              <v-toolbar dark color="indigo" style="border-radius: 0px">
                <v-btn icon dark @click="cancelSave">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Tugas</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                  <v-btn
                    dark
                    text
                    :disabled="showHideButton"
                    @click="saveTask">{{ changeText }}
                  </v-btn>
                </v-toolbar-items>
              </v-toolbar>
              <v-list three-line subheader>
                <v-subheader>Pengaturan</v-subheader>
                <v-row align="center">
                  <v-col class="d-flex" cols="12" sm="6">
                    <v-row>
                      <v-col cols="6" sm="6">
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title>Pilih Siswa</v-list-item-title>
                            <v-autocomplete
                              v-model="selectedStudents"
                              :items="studentLists"
                              item-text="name"
                              item-value="id"
                              outlined
                              dense
                              chips
                              small-chips
                              label="Pilih Siswa"
                              multiple
                              @change="checkStudent"
                            >
                              <template v-slot:selection="{ item, index }">
                          <span
                            v-if="index === 0"
                            class="grey--text caption"
                          >{{ selectedStudents.length }} Siswa dipilih</span>
                              </template>
                              <template v-slot:prepend-item>
                                <v-list-item
                                  ripple
                                  @click="toggle"
                                >
                                  <v-list-item-action>
                                    <v-icon :color="selectedStudents.length > 0 ? 'indigo darken-4' : ''">{{ icon }}
                                    </v-icon>
                                  </v-list-item-action>
                                  <v-list-item-content>
                                    <v-list-item-title>Semua Siswa</v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                                <v-divider class="mt-2"></v-divider>
                              </template>
                            </v-autocomplete>
                            <span style="color: red; margin-top: -25px">
                              <strong id="student-error"></strong>
                            </span>
                          </v-list-item-content>
                        </v-list-item>
                      </v-col>
                      <v-col cols="6" sm="6">
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title>Point</v-list-item-title>
                            <v-text-field
                              label="Contoh : 100"
                              required
                              outlined
                              type="number"
                              min="1"
                              dense
                              v-model="point"
                            ></v-text-field>
                            <span style="color: red; margin-top: -25px">
                              <strong id="point-error"></strong>
                            </span>
                          </v-list-item-content>
                        </v-list-item>
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col class="d-flex" cols="12" sm="6">
                    <v-row>
                      <v-col cols="12" sm="6">
                        <v-menu
                          v-model="menu2"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-list-item>
                              <v-list-item-content>
                                <v-list-item-title>Tanggal</v-list-item-title>
                                <v-text-field
                                  v-model="date"
                                  :label="currentDate"
                                  prepend-icon="event"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                  required
                                  outlined
                                  dense
                                ></v-text-field>
                                <span style="color: red; margin-top: -25px">
                                  <strong id="date-error"></strong>
                                </span>
                              </v-list-item-content>
                            </v-list-item>
                          </template>
                          <v-date-picker v-model="date" @input="menu2 = false" :min="nowDate"></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6">
                        <v-menu
                          ref="menu"
                          v-model="menu3"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          :return-value.sync="time"
                          transition="scale-transition"
                          offset-y
                          max-width="290px"
                          min-width="290px"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-list-item>
                              <v-list-item-content>
                                <v-list-item-title>Jam</v-list-item-title>
                                <v-text-field
                                  v-model="time"
                                  label="00:00"
                                  prepend-icon="access_time"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                  required
                                  outlined
                                  dense
                                ></v-text-field>
                                <span style="color: red; margin-top: -25px">
                                  <strong id="time-error"></strong>
                                </span>
                              </v-list-item-content>
                            </v-list-item>
                          </template>
                          <v-time-picker
                            v-if="menu3"
                            v-model="time"
                            format="24hr"
                            full-width
                            @click:minute="$refs.menu.save(time)"
                          ></v-time-picker>
                        </v-menu>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-list>
              <v-divider></v-divider>
              <v-list three-line subheader>
                <v-subheader>General</v-subheader>
                <v-col cols="12">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-list-item>
                        <v-list-item-content>
                          <v-list-item-title>Judul</v-list-item-title>
                          <v-list-item-subtitle>
                            <v-textarea
                              v-model="title"
                              name="input-7-1"
                              filled
                              label="Judul"
                              auto-grow
                            ></v-textarea>
                            <span style="color: red; top: -25px; position: relative">
                              <strong id="title-error"></strong>
                            </span>
                          </v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-list-item>
                        <v-list-item-content>
                          <v-list-item-title>Petunjuk (Optional)</v-list-item-title>
                          <v-list-item-subtitle>
                            <v-textarea
                              v-model="instruction"
                              name="input-7-1"
                              filled
                              label="Petunjuk (Optional)"
                              auto-grow
                            ></v-textarea>
                          </v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-col>
                    <v-col
                      cols="12" v-show="showImage"
                      v-for="(item, index) in selectedFile"
                      :key="index"
                      sm="6"
                      class="show-file"
                    >
                      <v-list-item>
                        <v-list-item-content>
                          <v-list-item-subtitle>
                            <v-col cols="12">
                              <v-card
                                class="pa-2 list-file"
                                outlined
                                style="border-radius: 10px"
                              >
                                <p class="file-name">{{ splitFilename(item.name) }}</p>
                                <v-btn
                                  icon
                                  class="float-right close"
                                  @click.prevent="deleteFile(index)"
                                >
                                  <v-icon>mdi-close</v-icon>
                                </v-btn>
                              </v-card>
                              <span style="color: red; margin-top: -25px">
                                <strong :id="`file_${index}-error`"></strong>
                              </span>
                            </v-col>
                          </v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12">
                  <v-col cols="12" sm="6" style="margin-top: -100px; margin-left: -12px">
                    <v-list-item>
                      <v-list-item-content>
                        <v-list-item-subtitle>
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
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                  </v-col>
                </v-col>
              </v-list>
            </v-card>
          </v-dialog>
        </div>
      </v-row>
      <v-col cols="12" sm="12">
        <div style="margin-top: 20px">
          <v-card
            v-for="(task, index) in taskList"
            :key="index"
            style="margin-top: 10px; border-radius: 10px"
            class="mx-auto"
            max-width="700"
            @click="detailTask(task.id, task.posting_id)"
          >
            <v-card-text>
              <v-avatar class="avatar" color="indigo" size="35" left>
                <v-icon dark>mdi-calendar-text</v-icon>
              </v-avatar>
              <span style="margin-left: 10px; font-weight: bold; font-size: 15px">{{ task.title }}</span>
              <v-menu offset-y>
                <template v-slot:activator="{ on }">
                  <v-btn v-if="checkGuard === 'employee'" v-on="on" style="float:right;" icon>
                    <v-icon small>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item @click="openModalEdit(task.id)">
                    <v-list-item-title>Edit</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteTask(task.id)">
                    <v-list-item-title>Hapus</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
              <span style="float: right; font-size: 12px; margin-top: 7px">Tenggat : {{ convertDateTime(task.deadline_date, task.time) }}</span>
            </v-card-text>
          </v-card>
          <infinite-loading @infinite="infiniteHandler">
            <span slot="no-more"></span>
          </infinite-loading>
        </div>
      </v-col>
    </v-container>
    <vue-confirm-dialog></vue-confirm-dialog>
  </v-app>
</template>

<script>
  import {mapActions, mapGetters} from "vuex";
  import moment from 'moment';

  export default {
    name: "Task",
    data() {
      return {
        nowDate: new Date().toISOString().slice(0, 10),
        id: '',
        date: '',
        time: '',
        title: '',
        point: '',
        instruction: '',
        url: '',
        page: 1,
        changeText: 'Simpan',
        menu2: false,
        menu3: false,
        modal: false,
        dialog: false,
        notifications: false,
        sound: true,
        widgets: false,
        isSelecting: false,
        showImage: false,
        disabledButton: false,
        studentLists: [],
        taskList: [],
        selectedStudents: [],
        selectedFile: [],
      }
    },
    mounted() {
      let pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
        cluster: process.env.MIX_PUSHER_APP_CLUSTER
      });
      let channel = pusher.subscribe('my-channel');
      channel.bind('my-event', () => {
        this.getTask();
      });
    },
    computed: {
      ...mapGetters([
        'getUser',
        'getClassId',
        'getColor'
      ]),

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      },

      icon() {
        const student = this.selectedStudents.length === this.studentLists.length;
        const studentLength = this.selectedStudents.length > 0 && !student;

        if (student) return 'mdi-close-box'
        if (studentLength) return 'mdi-minus-box'
        return 'mdi-checkbox-blank-outline'
      },

      currentDate() {
        return new Date().toISOString().substr(0, 10);
      },

      showHideButton() {
        if ((this.selectedStudents.length > 0) && (this.title !== '') && (this.point !== '') && (this.date !== '') && (this.time !== '')) {
          return this.disabledButton !== false;
        } else {
          return this.disabledButton !== true;
        }
      },
    },

    methods: {
      ...mapActions({
        setAlert: "setAlert"
      }),

      convertDateTime(date, time) {
        return moment(date).format('DD-MM-YYYY') + ' ' + moment(time, 'HH:mm').format('HH:mm');
      },

      splitFilename(filename) {
        const length = filename.length;
        if (length > 70) {
          return filename.substr(0, 70) + ' ...';
        } else {
          return filename
        }
      },

      getTask() {
        const id = this.getClassId;
        axios.get('/task/get', {
          params: {
            class_id: id
          }
        }).then(response => {
          if (response.data.status === 200) {
            this.taskList = response.data.data.data;
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

      checkStudent: function (params) {
        this.selectedStudents = params;
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

      deleteFile: function (index) {
        this.selectedFile.splice(index, 1);
      },

      toggle() {
        this.$nextTick(() => {
          const student = this.selectedStudents.length === this.studentLists.length;
          if (student) {
            this.selectedStudents = []
          } else {
            this.studentLists.map(item => {
              this.selectedStudents.push(item.id);
            })
          }
        })
      },

      getStudent() {
        const id = this.getClassId;
        axios.get('/task/student/get', {
          params: {
            class_id: id
          }
        }).then(response => {
          if (response.data.status === 200) {
            this.studentLists = response.data.data;
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

      openModal() {
        this.resetForm();
        this.dialog = true;
        this.url = '/task/create';
        const id = this.getClassId;
        axios.get('/task/student/get', {
          params: {
            class_id: id
          }
        }).then(response => {
          if (response.data.status === 200) {
            this.studentLists = response.data.data;
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

      openModalEdit(id) {
        this.dialog = true;
        this.url = '/task/update';
        this.getStudent();
        this.resetForm();
        axios.get('/task/edit', {
          params: {
            id: id
          }
        }).then(resp => {
          const data = resp.data.data;
          console.log(data.description);
          const images = resp.data.images;
          const students = resp.data.data.student_tasks;
          this.title = data.title;
          this.point = data.max_score;
          this.date = data.deadline_date;
          this.time = data.time;
          this.instruction = data.description;
          this.id = data.id;

          for (let i = 0; i < images.length; i++) {
            this.urlToFile(images[i].url, images[i].filename, images[i].mime)
              .then(file => {
                this.showImage = true;
                this.selectedFile.push(file);
              })
          }

          students.map(item => {
            this.selectedStudents.push(item.student_id);
          })
        })
          .catch(resp => {
            alert(resp.response.data.message)
          })
      },

      saveTask: function () {
        this.disabledButton = true;
        this.changeText = 'Loading...';
        const classId = this.getClassId;
        const formData = new FormData();

        for (let i = 0; i < this.selectedFile.length; i++) {
          let file = this.selectedFile[i];
          formData.append('file[]', file);
        }

        formData.append('students', JSON.stringify(this.selectedStudents));
        formData.append('point', this.point);
        formData.append('instruction', this.instruction);
        formData.append('date', this.date);
        formData.append('time', this.time);
        formData.append('title', this.title);
        formData.append('id', this.id);
        formData.append('class_id', classId);

        axios.post(this.url, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
          .then(response => {
            this.disabledButton = false;
            this.changeText = 'Simpan';
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
            this.changeText = 'Simpan';
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, function (val, key) {
                const replace = key.split('.').join('_');
                $('#' + replace + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.response.data.message)
          })
      },

      deleteTask: function (id) {
        this.$confirm({
          title: 'Apakah anda yakin ?',
          message: 'Data yang dihapus tidak bisa dikembalikan.',
          button: {
            yes: 'Ya',
            no: 'Batal'
          },
          callback: confirm => {
            if (confirm) {
              axios.delete('/task/delete', {
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

      cancelSave: function () {
        this.dialog = !this.dialog
        this.selectedFile.splice(0, this.selectedFile.length);
        this.$refs.uploader.value = null;
      },

      detailTask: function (id, postingId) {
        this.$router.push(`/task/detail/${id}/${postingId}`);
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

      openFile: function (file) {
        window.open('/storage/' + file, '_blank');
      },

      resetForm: function () {
        this.date = '';
        this.time = '';
        this.title = '';
        this.point = '';
        this.id = '';
        this.instruction = '';
        this.showImage = false;
        this.studentLists = [];
        this.selectedStudents = [];
        this.selectedFile = [];
      },

      infiniteHandler($state) {
        const id = this.getClassId;
        axios.get('/task/get', {
          params: {
            page: this.page,
            class_id: id
          },
        }).then(({data}) => {
          if (data.data.data.length) {
            this.page += 1;
            this.taskList.push(...data.data.data);
            $state.loaded();
          } else {
            $state.complete();
          }
        });
      },
    },
  }
</script>

<style scoped>
  .container {
    width: 65%;
  }

  .show-file {
    margin-top: -70px;
    margin-bottom: 15px;
  }

  .close {
    margin-top: -40px;
  }

  .file-name {
    margin-top: 10px;
  }

  .list-file {
    margin-top: 10px;
  }
</style>
