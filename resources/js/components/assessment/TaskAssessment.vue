<template>
  <div>
    <v-container>
      <v-col cols="12" sm="12">
        <v-simple-table fixed-header>
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left">No</th>
              <th class="text-left">Nama Tugas</th>
              <th class="text-left">Tanggal Pengumpulan</th>
              <th class="text-left">Waktu Pengumpulan</th>
              <th class="text-left">Maksimal Nilai</th>
              <th class="text-left">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="tasks.length === 0">
              <td colspan="6" class="text-center">Tidak ada data tugas</td>
            </tr>
            <tr v-else v-for="(item, index) in tasks" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ item.title }}</td>
              <td>{{ convertToDate(item.deadline_date) }}</td>
              <td>{{ convertToTime(item.time) }}</td>
              <td>{{ item.max_score }}</td>
              <td>
                <center>
                  <v-btn
                    color="primary"
                    title="Ubah Nilai"
                    fab
                    small
                    dark
                    @click="detailTask(item.id, item.posting_id)"
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
import moment from "moment";

export default {
  name: "TaskAssessment",
  data() {
    return {
      tasks: [],
      page: 1,
      pagination: {
        current: 1,
        total: 0
      },
    }
  },
  mounted() {
    this.getTask();
  },
  methods: {
    convertToDate: function (date) {
      return moment(date).format('DD-MM-YYYY');
    },

    convertToTime: function (time) {
      return moment(time, 'HH:mm').format('HH:mm');
    },

    getTask() {
      axios.get('/assessment/task', {
        params: {
          page: this.pagination.current,
          class_id: this.$route.params.id
        }
      })
        .then(response => {
          this.tasks = response.data.task.data;
          this.pagination.current = response.data.task.current_page;
          this.pagination.total = response.data.task.last_page;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        });
    },

    onPageChange() {
      this.getTask();
    },

    detailTask: function (id, postingId) {
      this.$router.push(`/task/detail/${id}/${postingId}`);
    },

    exportScore(id) {
      window.open('/assessment/export/task/' + id, '_blank');
    },
  }
}
</script>

<style scoped>

</style>
