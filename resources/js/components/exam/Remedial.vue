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
                <th class="text-left">Jenis Ujian</th>
                <th class="text-left">{{ subjectName }}</th>
                <th class="text-left">{{ level }}</th>
                <th class="text-left">Tersedia</th>
                <th class="text-left">Durasi</th>
                <th class="text-left">Jumlah Soal</th>
                <th class="text-left">Remedial Ke</th>
                <th class="text-left">Aksi</th>
              </tr>
              </thead>
              <tbody>
              <tr v-if="remedialList.length === 0">
                <td colspan="9" class="text-center">Tidak ada data remedial</td>
              </tr>
              <tr v-else v-for="(item, index) in remedialList" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ typeExam(item.assign_student.exam.type_exam) }}</td>
                <td>{{ item.assign_student.exam.subject.name }}</td>
                <td>{{ checkLevel(item.assign_student.exam) }}</td>
                <td width="270px">{{ convertFormatDatetimeToTime(item.start_date, item.end_date) }}</td>
                <td>{{ item.assign_student.exam.duration }} Menit</td>
                <td>{{ item.assign_student.exam.amount_question }} Soal</td>
                <td>{{ item.exam_to }}</td>
                <td>
                  <center>
                    <div>
                      <span v-if="today < convertFormatDate(item.start_date)">
                        <v-chip class="ma-2" color="red" text-color="white" x-small>Remedial belum dimulai</v-chip>
                      </span>
                      <span v-else-if="today > convertFormatDate(item.end_date)">
                        <span v-if="item.assign_student.exam.show_value === 2">
                          <v-chip class="ma-2" color="green" text-color="white" x-small>Remedial sudah selesai</v-chip>
                        </span>
                        <span v-else>
                          <v-btn @click="getScore(item.assign_student.exam.id)" color="success" title="Hasil Ujian" fab small dark>
                            <v-icon>mdi-chart-bar</v-icon>
                          </v-btn>
                        </span>
                      </span>
                      <span v-else>
                        <span v-if="item.status === 1">
                          <span v-if="item.assign_student.exam.show_value === 2">
                            <v-chip class="ma-2" color="green" text-color="white" x-small>Remedial sudah selesai</v-chip>
                          </span>
                          <span v-else>
                            <v-btn @click="getScore(item.assign_student.exam.id, item.id)" color="success" title="Hasil Ujian" fab small dark>
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
                          @click="examPage(item.assign_student.exam.id)"
                         >
                          <v-icon>mdi-clock-start</v-icon>
                        </v-btn>
                      </span>
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
                  <td :class="`text-center ${changeScoreColor(score)}`" style="font-size: 30px; font-weight: bold">
                    {{ score }}
                  </td>
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
  name: "Remedial",
  data: function () {
    return {
      dialog: false,
      today: new Date().getTime(),
      remedialList: [],
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
        message: 'Setelah remedial tidak bisa kembali lagi',
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            axios.post('/exam/start', {
              exam_id: id
            }).then(resp => {
              localStorage.setItem('type', 'remedial');
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
      axios.get('/exam/remedial', {
        params: {
          page: this.pagination.current,
          class_id: id
        },
      }).then(resp => {
        this.remedialList = resp.data.data.data;
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

    getScore(id, remedialId) {
      this.dialog = true;
      axios.get('/exam/score/student/remedial', {
        params: {
          exam_id: id,
          remedial_id: remedialId
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

