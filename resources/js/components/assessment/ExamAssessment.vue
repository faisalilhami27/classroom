<template>
  <div>
    <v-container>
      <v-col cols="12" sm="12">
        <v-simple-table fixed-header>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left">No</th>
              <th class="text-left">Nama Ujian</th>
              <th class="text-left">Jenis Ujian</th>
              <th class="text-left">{{ subjectName }}</th>
              <th class="text-left">{{ level }}</th>
              <th class="text-left">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in exams" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ item.name }}</td>
              <td>{{ typeExam(item.type_exam) }}</td>
              <td>{{ item.subject.name }}</td>
              <td>{{ checkLevel(item) }}</td>
              <td>
                <center>
                  <v-btn
                    color="primary"
                    title="Progress Ujian"
                    fab
                    small
                    dark
                    @click="examDetail(item.id)"
                  >
                    <v-icon>mdi-chart-bar</v-icon>
                  </v-btn>
                  <v-btn
                    color="green"
                    title="Export Nilai"
                    fab
                    small
                    dark
                    @click="exportScore(item.id)"
                  >
                    <v-icon>mdi-file-excel</v-icon>
                  </v-btn>
                </center>
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
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
  </div>
</template>


<script>
export default {
  name: "ExamAssessment",
  data() {
    return {
      exams: [],
      subjectName: '',
      level: '',
      tasks: [],
      pagination: {
        current: 1,
        total: 0
      },
    }
  },
  mounted() {
    this.getExam();
  },
  methods: {
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

    checkLevel(item) {
      if (item.semester != null) {
        return item.semester.number;
      } else {
        return item.grade_level.name;
      }
    },

    getExam() {
      axios.get('/assessment/exam', {
        params: {
          page: this.pagination.current,
          class_id: this.$route.params.id
        },
      }).then(resp => {
        this.exams = resp.data.data.data;
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

    examDetail(id) {
      this.$router.push(`/exam/detail/${id}`);
    },

    exportScore(id) {
      window.open('/progress/export/' + id, '_blank');
    },
  }
}
</script>

<style scoped>

</style>
