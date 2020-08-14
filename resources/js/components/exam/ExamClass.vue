<template>
  <div>
    <v-container>
      <v-col cols="12" sm="12">
        <div style="margin-top: 20px">
          <v-simple-table fixed-header>
            <template v-slot:default>
              <thead>
              <tr>
                <th class="text-left">No</th>
                <th class="text-left">Nama Ujian</th>
                <th class="text-left">Jenis Ujian</th>
                <th class="text-left">{{ subjectName }}</th>
                <th class="text-left">{{ level }}</th>
                <th class="text-left">Tersedia</th>
                <th class="text-left">Durasi</th>
                <th class="text-left">Jumlah Soal</th>
                <th class="text-left">Aksi</th>
              </tr>
              </thead>
              <tbody>
              <tr v-if="examList.length === 0">
                <td colspan="9" class="text-center">Tidak ada data ujian</td>
              </tr>
              <tr v-else v-for="(item, index) in examList" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.name }}</td>
                <td>{{ typeExam(item.type_exam) }}</td>
                <td>{{ item.subject.name }}</td>
                <td>{{ checkLevel(item) }}</td>
                <td width="270px">{{ convertFormatDatetimeToTime(item.start_date, item.end_date) }}</td>
                <td>{{ item.duration }} Menit</td>
                <td>{{ item.amount_question }} Soal</td>
                <td>
                  <center>
                    <div v-if="checkGuard === 'employee'">
                    <span v-if="item.status === 0">
                     <v-chip class="ma-2" color="warning" text-color="white" x-small>Ujian belum diaktifkan</v-chip>
                    </span>
                      <v-btn @click="examDetail(item.id)" v-else color="primary" title="Progress Ujian" fab small dark>
                        <v-icon>mdi-chart-bar</v-icon>
                      </v-btn>
                    </div>
                    <div v-else>
                       <span v-if="item.status === 0">
                         <v-chip class="ma-2" color="warning" text-color="white" x-small>Ujian belum diaktifkan</v-chip>
                       </span>
                      <div v-else>
                        <span v-if="today < convertFormatDate(item.start_date)">
                          <v-chip class="ma-2" color="red" text-color="white" x-small>Ujian belum dimulai</v-chip>
                        </span>
                        <span v-else-if="today > convertFormatDate(item.end_date)">
                          <span v-if="item.show_value === 2">
                            <v-chip class="ma-2" color="green" text-color="white" x-small>Ujian sudah selesai</v-chip>
                          </span>
                          <span v-else>
                            <v-btn @click="getScore(item.id)" color="success" title="Hasil Ujian" fab small dark>
                              <v-icon>mdi-chart-bar</v-icon>
                            </v-btn>
                          </span>
                        </span>
                        <span v-else>
                          <span v-if="item.single_assign_student.status === 1">
                            <span v-if="item.show_value === 2">
                               <v-chip class="ma-2" color="green" text-color="white" x-small>Ujian sudah selesai</v-chip>
                            </span>
                            <span v-else>
                              <v-btn @click="getScore(item.id)" color="success" title="Hasil Ujian" fab small dark>
                                <v-icon>mdi-chart-bar</v-icon>
                              </v-btn>
                            </span>
                          </span>
                         <v-btn
                           v-else
                           color="primary"
                           title="Mulai ujian"
                           fab
                           small
                           dark
                           @click="examPage(item.id)"
                         >
                           <v-icon>mdi-clock-start</v-icon>
                         </v-btn>
                        </span>
                      </div>
                    </div>
                  </center>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </div>
      </v-col>
      <v-row justify="center">
        <v-col cols="8">
          <v-pagination
            v-model="pagination.current"
            :length="pagination.total"
            @input="onPageChange"
          ></v-pagination>
        </v-col>
      </v-row>
    </v-container>
    <vue-confirm-dialog></vue-confirm-dialog>
    <div class="text-center">
      <v-dialog
        v-model="dialog"
        width="500"
      >
        <v-card>
          <v-card-title class="headline header justify-center blue lighten-2">
            <span style="color: white">Nilai Ujian</span>
          </v-card-title>

          <v-card-text>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th class="text-center" style="font-size: 17px">KKM</th>
                  <th class="text-center" style="font-size: 17px">Nilai Ujian</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td class="text-center">
                    <span v-if="minimal == null" style="font-size: 17px">Belum ditentukan</span>
                    <span v-else style="font-size: 30px; font-weight: bold">{{ minimal }}</span>
                  </td>
                  <td :class="`text-center ${changeScoreColor(score)}`" style="font-size: 30px; font-weight: bold">{{ score }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
            <div class="text-center" style="margin-top: 10px; margin-bottom: -10px">
              <v-chip
                class="ma-2"
                color="#00a000"
                text-color="white"
                x-small
              >
                Di atas KKM
              </v-chip>
              <v-chip
                class="ma-2"
                color="#FB8C00"
                text-color="white"
                x-small
              >
                Pas KKM
              </v-chip>
              <v-chip
                class="ma-2"
                color="#F44336"
                text-color="white"
                x-small
              >
                Di bawah KKM
              </v-chip>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              text
              @click="dialog = false"
            >
              Tutup
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import moment from 'moment';

export default {
  name: "ExamClass",
  data: function () {
    return {
      dialog: false,
      today: new Date().getTime(),
      examList: [],
      score: 0,
      minimal: 0,
      pagination: {
        current: 1,
        total: 0
      },
      subjectName: '',
      level: '',
    }
  },
  mounted() {
    this.getExam();
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
  },
  methods: {
    ...mapActions({
      setAlert: "setAlert"
    }),

    examDetail(id) {
      this.$router.push(`/exam/detail/${id}`);
    },

    changeScoreColor(score) {
      if (this.minimal == null) {
        return 'undetermined';
      } else if (score > this.minimal) {
        return 'perfect-score';
      } else if (score === this.minimal) {
        return 'medium-score';
      } else {
        return 'low-score';
      }
    },

    convertFormatDatetimeToTime: function (startDate, endDate) {
      return moment(startDate).format('DD-MM-YYYY HH:mm') + ' - ' + moment(endDate).format('DD-MM-YYYY HH:mm');
    },

    convertFormatDate: function (date) {
      return new Date(date).getTime();
    },

    checkLevel(item) {
      if (item.semester != null) {
        return item.semester.number;
      } else {
        return item.grade_level.name;
      }
    },

    splitName(name) {
      const length = name.length;
      if (length > 50) {
        return name.substr(0, 50) + ' ...';
      } else {
        return name;
      }
    },

    typeExam(type) {
      let string = '';
      switch (type) {
        case 1:
          string = 'Kuis';
          break;
        case 2:
          string = 'UTS';
          break;
        case 3:
          string = 'UAS';
          break;
        case 4:
          string = 'Try Out';
          break;
        default:
          string = 'Undefined';
          break;
      }
      return string;
    },

    examPage: function (id) {
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: 'Setelah mulai ujian tidak bisa kembali lagi',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            axios.post('/exam/start', {
              exam_id: id
            }).then(resp => {
              localStorage.setItem('type', 'exam');
              window.location.assign(resp.data.exam_page);
            })
              .catch(resp => {
                alert(resp.response.data.message);
              });
          }
        }
      });
    },

    getExam() {
      const id = this.getClassId;
      axios.get('/exam/get', {
        params: {
          page: this.pagination.current,
          class_id: id
        },
      }).then(resp => {
        this.examList = resp.data.data.data;
        this.pagination.current = resp.data.data.current_page;
        this.pagination.total = resp.data.data.last_page;
        this.subjectName = resp.data.subject_name;
        this.level = resp.data.level;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    onPageChange() {
      this.getExam();
    },

    getScore(id) {
      this.dialog = true;
      axios.get('/exam/score/student', {
        params: {
          exam_id: id
        },
      }).then(resp => {
        this.score = resp.data.score;
        this.minimal = resp.data.minimal;
      })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    }
  }
}
</script>

<style scoped>
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

.undetermined {
  text-align: center;
  font-size: 50px;
  font-weight: bold;
  color: #000;
}
</style>
