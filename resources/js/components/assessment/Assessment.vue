<template>
  <v-app>
    <v-container>
      <v-col cols="12" sm="12">
        <v-card>
          <v-tabs
            v-model="tabs"
            background-color="indigo accent-4"
            centered
            dark
            icons-and-text
          >
            <v-tabs-slider></v-tabs-slider>
            <v-tab href="#tab-1">
              Daftar Tugas
            </v-tab>
            <v-tab href="#tab-2">
              Daftar Ujian
            </v-tab>
          </v-tabs>

          <v-tabs-items v-model="tabs">
            <v-tab-item
              v-for="(item, index) in items"
              :key="index"
              :value="'tab-' + (index + 1)"
            >
              <v-card flat>
                <v-card-text>
                  <component :is="item.content"></component>
                </v-card-text>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-container>
  </v-app>
</template>

<script>
import TaskAssessment from './TaskAssessment';
import ExamAssessment from './ExamAssessment';

export default {
  name: "Assessment",
  components: {
    TaskAssessment,
    ExamAssessment
  },
  data() {
    return {
      tabs: null,
      items: [
        {content: 'TaskAssessment'},
        {content: 'ExamAssessment'}
      ]
    }
  }
}
</script>

<style scoped>

</style>
