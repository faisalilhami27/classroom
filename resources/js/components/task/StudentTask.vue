<template>
  <v-app>
    <v-row>
      <v-col sm="12" md="4" style="border-right: 1px solid #E0E0E0">
        <h4>List Siswa</h4>
        <v-switch color="success" @change="changeShowScore" v-model="showScore"
                  label="Tampilkan nilai ke siswa ?"></v-switch>
        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left">Siswa</th>
              <th class="text-left">Nilai</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in students" :key="index">
              <td>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <div v-bind="attrs" v-on="on">
                      <v-avatar :color="item.color" size="36">
                        <span v-if="item.photo == null" class="white--text">{{ item.name.substr(0, 2) }}</span>
                        <img v-else :alt="item.name.substr(0, 2)" :src="item.photo">
                      </v-avatar>
                      {{ splitName(item.name, 25) }}
                    </div>
                  </template>
                  <span>{{ item.sin_name }}</span>
                </v-tooltip>
              </td>
              <td>
                <v-row no-gutters>
                  <v-col>
                    <v-text-field
                      @keyup="fillStudentScore(item.id)"
                      style="width: 30px"
                      maxLength="3"
                      v-model="scoreValue[item.id]"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col>
                    <v-text-field
                      v-model="score"
                      readonly
                      class="score"
                      required
                    ></v-text-field>
                  </v-col>
                </v-row>
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-col>
      <v-col sm="12" md="8">
        <h4>Tugas Siswa</h4>
        <v-row>
          <v-col sm="12" md="2">
            <a class="status all" @click="getAllData">
              <p class="amount">{{ all }}</p>
              <p>Semua</p>
            </a>
          </v-col>
          <v-col sm="12" md="2">
            <a class="status submit" @click="getSubmitData">
              <p class="amount">{{ submit }}</p>
              <p>Diserahkan</p>
            </a>
          </v-col>
          <v-divider vertical class="divider"></v-divider>
          <v-col sm="12" md="2">
            <a class="status late" @click="getLateData">
              <p class="amount">{{ late }}</p>
              <p>Terlambat</p>
            </a>
          </v-col>
          <v-divider vertical class="divider"></v-divider>
          <v-col sm="12" md="3">
            <a class="status unSubmit" @click="getUnSubmitData">
              <p class="amount">{{ unSubmit }}</p>
              <p>Belum Menyerahkan</p>
            </a>
          </v-col>
        </v-row>
        <v-btn :disabled="disabledButton" @click="downloadAllFiles" color="info">
          <v-icon left>mdi-download-circle</v-icon>
          {{ changeText }}
        </v-btn>
        <br><br>
        <v-row>
          <v-col cols="12" md="4" v-for="(item, index) in tasks" :key="index">
            <v-card
              color="#F5F5F5"
              dark
            >
              <v-card-title class="student-name">{{ splitName(item.student.name, 26) }}</v-card-title>
              <v-tooltip v-if="item.task_file != null" bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-chip
                    v-bind="attrs"
                    v-on="on"
                    @click="openFile(item.task_file)"
                    class="ma-2 file-task"
                    color="purple"
                    text-color="white"
                  >
                    <v-avatar left>
                      <v-icon>mdi-download-circle</v-icon>
                    </v-avatar>
                    {{ splitName(item.filename, 20) }}
                  </v-chip>
                </template>
                <span>Klik untuk membuka</span>
              </v-tooltip>
              <v-chip
                v-else
                class="ma-2 file-task"
                color="red"
                text-color="white"
              >
                <v-avatar left>
                  <v-icon>mdi-close-circle</v-icon>
                </v-avatar>
                Belum menyerahkan
              </v-chip>
              <p :class="convertClassName(item.status)">{{ convertStatus(item.status) }}</p>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

export default {
  name: "StudentTask",
  data: function () {
    return {
      students: [],
      tasks: [],
      score: 0,
      all: 0,
      submit: 0,
      late: 0,
      unSubmit: 0,
      status: 'all',
      scoreValue: [],
      disabledButton: false,
      showScore: false,
      typingTimer: false,
      changeText: 'Download Semua Tugas'
    }
  },
  computed: {
    ...mapGetters([
      'getUser',
      'getClassId'
    ])
  },
  mounted() {
    this.getStudents();
    this.getDataByStatus(this.status);
  },
  methods: {
    ...mapActions({
      setAlert: 'setAlert'
    }),
    splitName(name, number) {
      if (name != null) {
        const length = name.length;
        if (length > number) {
          return name.substr(0, number) + ' ...';
        } else {
          return name
        }
      }
    },

    convertStatus(status) {
      if (status === 3) {
        return 'Belum Menyerahkan';
      } else if (status === 1) {
        return 'Terlambat';
      } else {
        return 'Diserahkan';
      }
    },

    convertClassName(status) {
      if (status === 3) {
        return 'unSubmit-task';
      } else if (status === 1) {
        return 'late-task';
      } else {
        return 'submit-task';
      }
    },

    getStudents() {
      const id = this.$route.params.id;
      axios.post('/task/all/student/task', {
        task_id: id
      }).then(response => {
        const data = response.data.data;
        const task = response.data.task;
        this.score = '/' + task.max_score;
        this.showScore = (task.show_score !== 1);
        if (data != null) {
          data.forEach((item) => {
            this.students.push(item);
            this.scoreValue[item.id] = item.score
          });
        }
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    getAllData() {
      const status = this.status = 'all';
      this.getDataByStatus(status);
    },

    getSubmitData() {
      const status = this.status = 2;
      this.getDataByStatus(status);
    },

    getLateData() {
      const status = this.status = 1;
      this.getDataByStatus(status);
    },

    getUnSubmitData() {
      const status = this.status = 3;
      this.getDataByStatus(status);
    },

    getDataByStatus(status = '') {
      this.tasks = [];
      const id = this.$route.params.id;
      axios.post('/task/status', {
        task_id: id,
        status: status
      }).then(response => {
        const data = response.data.data;
        this.submit = data.count_submit;
        this.late = data.count_late;
        this.unSubmit = data.count_unSubmit;
        this.all = data.count_all;
        if (data.student_tasks != null) {
          data.student_tasks.forEach(item => {
            this.tasks.push(item);
          });
        }
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    changeShowScore() {
      const id = this.$route.params.id;
      axios.post('/task/change/show/score', {
        task_id: id,
        status: this.showScore
      }).then(response => {
        this.setAlert({
          message: response.data.message,
          status: response.data.status,
        });
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    openFile(url) {
      window.open('/storage/' + url, '_blank');
    },

    fillStudentScore(params) {
      if (this.typingTimer) {
        clearTimeout(this.typingTimer);
      }

      this.typingTimer = setTimeout(() => {
        const id = this.$route.params.id;
        axios.post('/task/score', {
          task_id: id,
          score: this.scoreValue[params],
          student_id: params
        }).then(response => {
          this.setAlert({
            message: response.data.message,
            status: response.data.status,
          });
        })
          .catch(resp => {
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, (val, key) => {
                this.setAlert({
                  message: val[0],
                  status: 500,
                });
              })
            }
          });
      }, 1000);
    },

    downloadAllFiles() {
      this.$confirm({
        title: 'Apakah akan mendownload semua tugas ?',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            const id = this.$route.params.id;
            window.open('/task/file/download/' + id);
          }
        }
      });
    }
  }
}
</script>

<style scoped>
.amount {
  font-size: 40px;
  margin-bottom: -10px;
}

.divider {
  height: 50px;
  margin-top: 30px;
  margin-right: 30px;
}

.status {
  color: black;
  text-decoration: none;
}

.submit:hover {
  color: #00a000;
}

.all:hover {
  color: #0000cc;
}

.late:hover {
  color: #FB8C00;
}

.unSubmit:hover {
  color: #F44336;
}

.file-task {
  position: relative;
  left: 8px;
  top: -20px;
}

.submit-task {
  margin-top: -20px;
  margin-left: 15px;
  padding-bottom: 10px;
  color: #00a000;
}

.late-task {
  margin-top: -20px;
  margin-left: 15px;
  padding-bottom: 10px;
  color: #FB8C00;
}

.unSubmit-task {
  margin-top: -20px;
  margin-left: 15px;
  padding-bottom: 10px;
  color: #F44336;
}

.student-name {
  color: black;
  font-size: 15px;
}

.score {
  width: 35px;
  position: relative;
  right: 8px;
}
</style>
