<template>
  <v-app id="inspire">
    <v-card>
      <v-toolbar
        dark
        tabs
        flat
        color="indigo"
        style="border-radius: 0; height: 90px"
      >
        <img :src="image" alt="" class="school-logo" width="70" height="70">
        <div>
          <p style="margin-left: 10px; margin-top: 30px; margin-bottom: -25px">{{ typeExam(exam.type) }}</p>
          <p style="margin-left: 10px; margin-top: 20px">{{ className }}</p>
        </div>
        <v-spacer></v-spacer>
        <div>
          <p style="margin-top: 30px; margin-bottom: -25px">Selamat datang,</p>
          <p style="margin-top: 20px">{{ getName }}</p>
        </div>
      </v-toolbar>
    </v-card>
    <v-container fluid>
      <v-row>
        <v-col md="9" sm="12">
          <v-card
            class="mx-auto"
          >
            <v-card-text>
              <span><v-btn small color="purple" class="text-timer" dark>Soal No :
              </v-btn><v-btn small class="timer" color="blue" dark>{{ currentPage }}</v-btn></span>
              <v-layout justify-center>
                <v-flex xs12 md2>
                  <v-btn small color="green" @click="dialog = true" class="btn-rules" dark>Peraturan
                  </v-btn>
                </v-flex>
              </v-layout>
              <span style="float: right"><v-btn small color="purple" class="text-timer remaining-time"
                                                dark>Sisa Waktu :</v-btn><v-btn
                small class="timer remaining-time" :color="color" dark>{{ displayTimer }}</v-btn></span>
              <div class="clearfix"></div>
            </v-card-text>
            <v-divider style="margin-top: -30px"></v-divider>
            <v-container v-if="showData">
              <div v-for="(item, index) in questionList" :key="index">
                <div v-if="item.accommodate_exam_question.question_bank.extension != null">
                  <div v-if="item.accommodate_exam_question.question_bank.extension === 'mp3'">
                    <audio controls>
                      <source :src="`/storage/${item.accommodate_exam_question.question_bank.document}`"
                              type="audio/mpeg">
                      Browser anda tidak mendukung file audio.
                    </audio>
                  </div>
                  <div v-else>
                    <video style="width: 100%; height: auto; z-index: 9999;" controls>
                      <source :src="`/storage/${item.accommodate_exam_question.question_bank.document}`"
                              type="video/mp4">
                      Browser anda tidak mendukung file video.
                    </video>
                  </div>
                </div>
                <p v-html="convertToHtml(item.accommodate_exam_question.question_bank.question_name)"></p>
                <v-radio-group v-model="answerSelected">
                  <v-radio
                    class="black--text"
                    v-for="(answer, i) in item.accommodate_exam_question.question_bank.answer_key"
                    :key="i"
                    :label="answer.answer_name"
                    :value="answer.id"
                    @change="setStudentAnswer(answer.question_id, answer.id)"
                  >
                  </v-radio>
                </v-radio-group>
                <v-divider></v-divider>
                <div class="previous">
                  <v-btn
                    v-if="prevPageUrl != null" @click.prevent="getQuestion(currentPage, 'previous')"
                    class="mt-2 btn-previous"
                    small
                    color="primary"
                  >
                    <v-icon left>mdi-arrow-left-circle</v-icon>
                    Sebelumnya
                  </v-btn>
                </div>
                <v-layout justify-center>
                  <v-flex xs12 md2>
                    <v-switch
                      inset
                      v-model="checkHesitate"
                      label="Ragu-ragu"
                      color="orange darken-3"
                      @change="setHesitateAnswer(item.accommodate_exam_question.question_bank.id)"
                    ></v-switch>
                  </v-flex>
                </v-layout>
                <div v-if="currentPage != lastPage" class="next">
                  <v-btn
                    @click.prevent="getQuestion(currentPage, 'next')"
                    class="mt-2 btn-next"
                    small
                    color="primary"
                  >
                    <v-icon left>mdi-arrow-right-circle</v-icon>
                    Selanjutnya
                  </v-btn>
                </div>
                <div v-else class="done">
                  <v-btn
                    @click.prevent="confirmExamDone"
                    class="mt-2 btn-done"
                    small
                    color="primary"
                  >
                    <v-icon left>mdi-content-save</v-icon>
                    Selesai
                  </v-btn>
                </div>
                <div style="clear: both"></div>
              </div>
            </v-container>
          </v-card>
        </v-col>
        <v-col md="3" sm="12">
          <v-card class="mx-auto">
            <p class="title-map"><b>Map Soal</b></p>
            <v-divider></v-divider>
            <v-card-text v-if="showData" style="margin-top: -25px">
              <v-row class="button-map">
                <v-col cols="12">
                  <v-row>
                    <div style="margin-left: 5px">
                      <div
                        style="display: inline-block"
                        v-for="(item, index) in mapQuestion"
                        :key="index"
                      >
                        <div v-if="item.accommodate_exam_question.question_bank.student_answer != null">
                          <v-btn
                            v-if="item.accommodate_exam_question.question_bank.student_answer.hesitate == null &&
                                  item.accommodate_exam_question.question_bank.student_answer.answer_id == null"
                            @click.prevent="getQuestion((index + 1), 'map')"
                            small
                            class="ma-2 pa-2"
                            color="red"
                            dark>
                            {{ (index + 1) }}
                          </v-btn>
                          <v-btn
                            v-else-if="item.accommodate_exam_question.question_bank.student_answer.hesitate == null"
                            @click.prevent="getQuestion((index + 1), 'map')"
                            small
                            class="ma-2 pa-2"
                            color="green"
                            dark>
                            {{ (index + 1) }}
                          </v-btn>
                          <v-btn
                            v-else
                            @click.prevent="getQuestion((index + 1), 'map')"
                            small
                            class="ma-2 pa-2"
                            color="orange"
                            dark>
                            {{ (index + 1) }}
                          </v-btn>
                        </div>
                        <div v-else>
                          <v-btn
                            @click.prevent="getQuestion((index + 1), 'map')"
                            small
                            class="ma-2 pa-2"
                            color="red"
                            dark>
                            {{ (index + 1) }}
                          </v-btn>
                        </div>
                      </div>
                    </div>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
            <div class="information">
              <v-chip
                class="ma-1"
                x-small
                text-color="white"
                color="green"
              >
                Sudah dijawab
              </v-chip>
              <v-chip
                class="ma-1"
                x-small
                text-color="white"
                color="orange"
              >
                Ragu-ragu
              </v-chip>
              <v-chip
                class="ma-1"
                x-small
                text-color="white"
                color="red"
              >
                Belum dijawab
              </v-chip>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="800">
        <v-card>
          <v-card-title
            class="headline header justify-center lighten-2"
            primary-title
          >
            Peraturan
          </v-card-title>
          <v-card-text class="rules" style="color: black;" v-html="exam.rules"></v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" small @click="fullscreen">Ya, saya setuju</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
    <div class="text-center">
      <v-dialog
        persistent
        v-model="warningDialog"
        width="500"
      >
        <v-card>
          <v-card-title class="headline header justify-center lighten-2" primary-title>
            Peringatan {{ countWarning }}
          </v-card-title>
          <v-card-text style="color: black; margin-top: 10px">
            Anda melakukan pelanggaran, silahkan kembali ke ujian atau ujian akan dianggap selesai dalam waktu <span
            style="font-weight: bold">{{ showTimerViolation }}</span> detik
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="red"
              small
              dark
              @click="backToExam"
            >
              Kembali Ujian
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <vue-confirm-dialog></vue-confirm-dialog>
  </v-app>
</template>

<script>
import {mapGetters, mapActions} from "vuex";

export default {
  name: "ExamPage",
  data() {
    return {
      page: localStorage.getItem('current_page'),
      type: localStorage.getItem('type'),
      timerViolation: '',
      prevPageUrl: null,
      lastPage: null,
      countWarning: 1,
      answerSelected: '',
      showTimerViolation: '',
      image: '',
      className: '',
      justify: 'center',
      color: 'blue',
      displayTimer: '00:00:00',
      questionList: [],
      mapQuestion: [],
      countQuestionAnswer: [],
      countQuestionHesitate: [],
      showData: false,
      checkHesitate: false,
      dialog: true,
      warningDialog: false,
      stopTimerViolation: false,
      stopTimerExam: false,
      showQuestion: false,
      currentPage: 1,
      exam: {
        duration: 0,
        question: 0,
        rules: '',
        type: ''
      }
    }
  },
  created() {
    document.title = this.getSubject;
    this.getSchoolConfig();
    this.getExamRules();
  },
  mounted() {
    this.notAllowed();
    this.getQuestion();
  },
  computed: {
    ...mapGetters([
      'getUser',
      'getClassId',
      'getSubject',
      'getColor'
    ]),

    getName() {
      const user = JSON.parse(this.getUser);
      return user.name;
    }
  },
  methods: {
    ...mapActions({
      setAlert: "setAlert"
    }),

    getSchoolConfig() {
      const id = this.$route.params.id;
      axios.get('/exam/school/config', {
        params: {
          exam_id: id
        }
      })
        .then(resp => {
          this.image = /storage/ + resp.data.config.school_logo;
          this.className = resp.data.class.class_name;
          this.exam.type = resp.data.exam.type_exam;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        })
    },

    getExamRules() {
      const id = this.$route.params.id;
      axios.get('/exam/rules/page', {
        params: {
          exam_id: id
        }
      })
        .then(resp => {
          this.exam.rules = this.convertToHtml(resp.data.text);
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    convertToHtml(params) {
      const e = document.createElement('div');
      e.innerHTML = params;
      return e.childNodes[0].nodeValue;
    },

    getQuestion(index = '', type = '') {
      const id = this.$route.params.id;
      const page = this.linkQuestion(index, type);
      axios.get('/exam/get/question', {
        params: {
          page: page,
          exam_id: id
        }
      })
        .then(resp => {
          const data = resp.data;
          this.successGetData(data);
        })
        .catch(resp => {
          alert(resp.response.data.message);
        })
    },

    typeExam(type) {
      let string = '';
      switch (type) {
        case 1:
          string = 'Ulangan Harian';
          break;
        case 2:
          string = 'Ujian Tengah Semester';
          break;
        case 3:
          string = 'Ujian Akhir Semester';
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

    linkQuestion(index, type) {
      let number = isNaN(index) ? 0 : parseInt(index);
      let page;
      switch (type) {
        case "map":
          page = index;
          break;
        case "next":
          page = (number + 1);
          break;
        case "previous":
          page = (number - 1);
          break;
        case "":
          page = this.page;
          break;
        default:
          page = 'undefined';
          break;
      }
      return page;
    },

    successGetData(data) {
      this.exam.duration = data.exam.duration;
      this.exam.question = data.exam.amount_question;
      this.mapQuestion = data.all;
      this.prevPageUrl = data.question.prev_page_url;
      this.lastPage = data.question.last_page;
      this.questionList = data.question.data;
      localStorage.setItem('current_page', data.question.current_page);
      this.currentPage = localStorage.getItem('current_page');
      data.question.data.forEach(item => {
        const data = item.accommodate_exam_question.question_bank.student_answer;
        if (data != null) {
          this.answerSelected = data.answer_id;
          this.checkHesitate = data.hesitate != null;
        } else {
          this.answerSelected = '';
          this.checkHesitate = false;
        }
      })
    },

    setStudentAnswer(questionId, answerId) {
      const examId = this.$route.params.id;
      axios.post('/exam/student/answer', {
        exam_id: examId,
        question_id: questionId,
        answer_id: answerId,
        type: this.type
      })
        .then(resp => {
          console.log(resp.data.count);
          this.mapQuestion = resp.data.all;
          localStorage.setItem('answer', resp.data.count);
          this.setAlert({
            message: resp.data.message,
            status: resp.data.status,
          });
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    setHesitateAnswer(questionId) {
      const examId = this.$route.params.id;
      this.checkHesitate = this.checkHesitate !== false;
      if (this.checkHesitate) {
        this.countQuestionHesitate.push(questionId);
        localStorage.setItem('hesitate', this.countQuestionHesitate.length);
      } else {
        this.countQuestionHesitate.pop(questionId);
        localStorage.setItem('hesitate', this.countQuestionHesitate.length);
      }
      axios.post('/exam/student/hesitate/answer', {
        exam_id: examId,
        question_id: questionId,
        hesitate: this.checkHesitate,
        type: this.type
      })
        .then(resp => {
          const data = resp.data;
          this.mapQuestion = data.all;
        })
    },

    countdown() {
      let duration = this.exam.duration;
      const timerFromStorage = localStorage.getItem('timer');
      let timer = (timerFromStorage == null) ? duration * 60 : timerFromStorage;
      this.stopTimerExam = setInterval(() => {
        if (Number(timer) > 0) {
          let hours = Math.floor(timer / 3600),
            minutes = Math.floor((timer - (hours * 3600)) / 60),
            seconds = timer - (hours * 3600) - (minutes * 60);

          hours = hours < 10 ? "0" + hours : hours;
          minutes = minutes < 10 ? "0" + minutes : minutes;
          seconds = seconds < 10 ? "0" + seconds : seconds;
          timer -= 1;
          localStorage.setItem('timer', timer);
          this.displayTimer = hours + ":" + minutes + ":" + seconds;
          if (Number(timer) < 9 * 60) {
            this.color = 'red';
          }
        } else {
          this.displayTimer = "00" + ":" + "00" + ":" + "00";
          if (Number(timer) === 0) {
            clearInterval(this.stopTimerExam);
            this.removeStorage();
            this.submitAnswer();
          }
        }
      }, 1000);
    },

    backToExam() {
      this.fullscreen();
      this.warningDialog = false;
      clearInterval(this.stopTimerViolation);
      this.showTimerViolation = this.timerViolation;
      localStorage.removeItem('time_violation');
    },

    countViolation() {
      const id = this.$route.params.id;
      axios.get('/exam/count/violation', {
        params: {
          exam_id: id
        }
      })
        .then(resp => {
          const data = resp.data.data;
          this.countWarning = data.violation;
          if (this.countWarning === 3) {
            this.removeStorage();
            this.submitAnswer();
          } else {
            clearInterval(this.stopTimerViolation);
            const timer = localStorage.getItem('time_violation');
            this.showTimerViolation = (timer == null) ? data.exam.time_violation : timer;
            this.countTimeViolation(data.exam.time_violation);
          }
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    countTimeViolation(duration) {
      const timerFromStorage = localStorage.getItem('time_violation');
      let timer = (timerFromStorage == null) ? duration : timerFromStorage;
      this.stopTimerViolation = setInterval(() => {
        timer--;
        localStorage.setItem('time_violation', timer);
        if (timer === 0) {
          clearInterval(this.stopTimerViolation);
          this.warningDialog = false;
          this.removeStorage();
          this.submitAnswer();
        }
        this.showTimerViolation = timer;
      }, 1000);
    },

    removeStorage() {
      localStorage.removeItem('timer');
      localStorage.removeItem('time_violation');
      localStorage.removeItem('current_page');
      localStorage.removeItem('answer');
      localStorage.removeItem('hesitate');
    },

    submitAnswer() {
      const id = this.$route.params.id;
      axios.post('/exam/insert/answer', {
        exam_id: id,
        type: this.type
      })
        .then(resp => {
          const data = resp.data;
          this.setAlert({
            message: data.message,
            status: data.status
          });
          location.href = data.link;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    confirmExamDone() {
      const message = this.checkQuestion();
      this.$confirm({
        title: 'Apakah anda yakin ?',
        message: message,
        button: {
          yes: 'Ya',
          no: 'Batal'
        },
        callback: confirm => {
          if (confirm) {
            clearInterval(this.stopTimerExam);
            this.removeStorage();
            clearInterval(this.stopTimerExam);
            this.submitAnswer();
          }
        }
      });
    },

    checkQuestion() {
      const storageAnswer = localStorage.getItem('answer');
      const storageHesitate = localStorage.getItem('hesitate');
      const answer = (storageAnswer == null) ? 0 : parseInt(storageAnswer);
      const hesitate = (storageHesitate == null) ? 0 : parseInt(storageHesitate);
      const notAnswer = (this.exam.question - answer);
      let message = '';
      if (notAnswer !== 0 && hesitate !== 0) {
        message = `Terdapat ${hesitate} soal ragu-ragu dan ${notAnswer} belum diisi`;
      } else if (notAnswer !== 0) {
        message = `Terdapat ${notAnswer} soal belum diisi`;
      } else if (hesitate !== 0) {
        message = `Terdapat ${hesitate} soal ragu-ragu`;
      } else {
        message = `Setelah selesai tidak bisa masuk kembali ke ujian`;
      }
      return message;
    },

    notAllowed() {
      window.history.pushState(null, "", window.location.href);
      document.oncontextmenu = function (e) {
        return false
      }
      window.addEventListener('blur', (e) => {
        const name = "Melakukan pelanggaran seperti menekan shortcut keyboard alt+tab, inspect element, atau yang lainnya";
        this.warningDialog = true;
        this.countViolation();
        this.addViolation(name);
      });
      document.addEventListener('keydown', (e) => {
        // press ctrl + T
        if (e.ctrlKey && e.keyCode == 84) {
          const name = "Mencoba membuka tab baru";
          this.warningDialog = true;
          this.countViolation();
          this.addViolation(name);
        }

        // press ctrl + N
        if (e.ctrlKey && e.keyCode == 78) {
          const name = "Mencoba membuka di browser baru";
          this.warningDialog = true;
          this.countViolation();
          this.addViolation(name);
        }
        return e.preventDefault();
      })
      document.addEventListener('fullscreenchange', this.exitHandler);
      document.addEventListener('webkitfullscreenchange', this.exitHandler);
      document.addEventListener('mozfullscreenchange', this.exitHandler);
      document.addEventListener('MSFullscreenChange', this.exitHandler);
    },

    exitHandler() {
      if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        const name = "Menekan tombol ESC/Keluar Fullscreen";
        this.warningDialog = true;
        this.countViolation();
        if (document.exitFullscreen) {
          this.addViolation(name);
        } else if (document.mozCancelFullScreen) { /* Firefox */
          this.addViolation(name);
        } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
          this.addViolation(name);
        } else if (document.msExitFullscreen) { /* IE/Edge */
          this.addViolation(name);
        }
      }
    },

    fullscreen() {
      this.dialog = false;
      this.showData = true;
      this.countdown();
      const elem = document.documentElement;
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.mozRequestFullScreen) { /* Firefox */
        elem.mozRequestFullScreen();
      } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) { /* IE/Edge */
        elem.msRequestFullscreen();
      }
    },

    addViolation(name) {
      const id = this.$route.params.id;
      axios.post('/exam/add/violation', {
        exam_id: id,
        violation_name: name,
        type: this.type
      })
        .then(() => {
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    }
  }
}
</script>

<style scoped>
.school-logo {
  margin-top: 25px;
  margin-left: -5px;
}

.title-map {
  text-align: center;
  padding-top: 5px;
  margin-bottom: 3px;
}

.button-map {
  left: 6px;
  position: relative;
}

.information {
  margin-top: -20px;
  margin-left: 7px;
  padding-bottom: 5px;
}

.text-timer {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.timer {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.clearfix {
  clear: both;
}

.link:hover {
  text-decoration: underline;
}

.black--text /deep/ label {
  color: black;
  font-size: 14px;
}

.next {
  position: relative;
  float: right;
  left: -15.5%;
}

.btn-next, .btn-done {
  top: -55px;
  position: absolute;
}

.done {
  position: relative;
  float: right;
  left: -11%;
}

.previous {
  position: relative;
}

.btn-previous {
  position: absolute;
  top: 13px;
}

.header {
  background-color: #3F51B5;
  color: white;
}

.rules {
  margin-left: -20px;
  margin-top: 10px;
}

.btn-rules {
  margin-top: -50px;
}

.remaining-time {
  margin-top: -97px;
}
</style>
