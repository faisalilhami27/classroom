<template>
  <v-app>
    <v-sheet>
      <v-card id="lateral" style="border-radius: 0px">
        <v-toolbar
          dark
          tabs
          flat
          :color="getColor"
          height="80px"
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
        </v-toolbar>
      </v-card>
      <!-- sidebar -->
      <sidebar v-model="openLeftNavigationDrawer"></sidebar>
      <v-row>
        <v-col sm="12" md="4" style="border-right: 1px solid #E0E0E0">
          <h4 style="margin-left: 15px">List Siswa</h4>
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
                        <span style="margin-left: 5px">{{ splitName(item.name, 25) }}</span>
                      </div>
                    </template>
                    <span>{{ item.sin_name }}</span>
                  </v-tooltip>
                </td>
                <td>
                  <v-row no-gutters>
                    <v-col>
                      <v-text-field
                        readonly
                        style="width: 30px"
                        maxLength="3"
                        v-model="item.score"
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
          <h4>Hasil Ujian</h4>
          <v-row>
            <v-col sm="12" md="2">
              <a class="status">
                <p class="amount">{{ pass }}</p>
                <p>Lulus</p>
              </a>
            </v-col>
            <v-divider vertical class="divider"></v-divider>
            <v-col sm="12" md="2">
              <a class="status">
                <p class="amount">{{ minimal }}</p>
                <p>KKM</p>
              </a>
            </v-col>
            <v-divider vertical class="divider"></v-divider>
            <v-col sm="12" md="2">
              <a class="status">
                <p class="amount">{{ notPass }}</p>
                <p>Tidak lulus</p>
              </a>
            </v-col>
            <v-divider vertical class="divider"></v-divider>
            <v-col sm="12" md="2">
              <a class="status">
                <p class="amount">{{ notYet }}</p>
                <p>Belum Ujian</p>
              </a>
            </v-col>
            <v-divider vertical class="divider"></v-divider>
            <v-col sm="12" md="2">
              <a class="status">
                <p class="amount">{{ notExam }}</p>
                <p>Tidak Ujian</p>
              </a>
            </v-col>
          </v-row>
          <div>
            <canvas id="exam-chart" height="120"></canvas>
          </div>
        </v-col>
      </v-row>
    </v-sheet>
  </v-app>
</template>

<script>
import Sidebar from '../layouts/Sidebar';
import Account from '../other/Account';
import Chat from '../chat/Chat';
import Announcement from "../other/Announcement";
import Chart from 'chart.js';
import {mapActions, mapGetters} from "vuex";

export default {
  name: "ExamDetail",
  data() {
    return {
      students: [],
      pass: 0,
      minimal: 0,
      notPass: 0,
      notYet: 0,
      notExam: 0,
      openLeftNavigationDrawer: false,
    }
  },
  components: {
    Announcement,
    Sidebar,
    Account,
    Chat
  },
  computed: {
    ...mapGetters([
      'getUrl',
      'getUser',
      'getColor',
      'getSubject',
      'getClassId'
    ]),

    pageTitle: function () {
      return this.getSubject;
    },
  },
  mounted() {
    this.getStudents();
    this.chartScoreExam();
  },
  methods: {
    ...mapActions({
      setAlert: "setAlert"
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

    getStudents() {
      const id = this.$route.params.id;
      axios.get('/exam/all/score', {
        params: {
          exam_id: id
        }
      }).then(response => {
        this.students = response.data.data;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    chartScoreExam() {
      const id = this.$route.params.id;
      axios.post('/exam/chart', {
        exam_id: id
      }).then(response => {
        if (response.data.status === 200) {
          const data = response.data.data;
          this.pass = data.pass;
          this.minimal = data.minimal;
          this.notPass = data.not_pass;
          this.notYet = data.not_yet;
          this.notExam = data.not_exam;
          this.createChart(data);
        } else {
          this.setAlert({
            message: response.data.message,
            status: response.data.status,
          });
        }
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    createChart(data) {
      const examCanvas = document.getElementById("exam-chart");

      Chart.defaults.global.defaultFontFamily = "Lato";
      Chart.defaults.global.defaultFontSize = 18;

      const examData = {
        labels: [
          "Lulus",
          "KKM",
          "Tidak Lulus",
          "Belum Ujian",
          "Tidak Ujian"
        ],
        datasets: [
          {
            data: [data.pass, data.minimal, data.not_pass, data.not_yet, data.not_exam],
            backgroundColor: [
              "#00de00",
              "#ffa330",
              "#F44336",
              "#7653ff",
              "#12cfe7"
            ]
          }]
      };

      new Chart(examCanvas, {
        type: 'pie',
        data: examData,
        options: {
          responsive: true,
          xAxes: [{
            gridLines: {
              display: false
            },
            ticks: {
              fontFamily: "Verdana",
            }
          }],
        }
      });
    },
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

.link:hover {
  text-decoration: underline;
}
</style>

