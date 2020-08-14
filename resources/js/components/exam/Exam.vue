<template>
  <v-app>
    <v-container>
      <h2 class="text-center">Daftar Ujian</h2>
      <br>
      <v-card v-if="checkGuard === 'student'">
        <v-tabs
          v-model="tabs"
          background-color="indigo accent-4"
          centered
          dark
          icons-and-text
        >
          <v-tabs-slider></v-tabs-slider>
          <v-tab href="#tab-1">
            Ujian
          </v-tab>
          <v-tab href="#tab-2">
            Remedial
          </v-tab>
          <v-tab href="#tab-3">
            Ujian Susulan
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
      <exam-class v-else></exam-class>
    </v-container>
  </v-app>
</template>

<script>
  import ExamClass from "./ExamClass";
  import Remedial from './Remedial';
  import Supplementary from './Supplementary';
  import {mapGetters} from "vuex";

  export default {
    name: "Exam",
    components: {
      Supplementary,
      ExamClass,
      Remedial
    },
    data() {
      return {
        tabs: null,
        items: [
          {content: 'ExamClass'},
          {content: 'Remedial'},
          {content: 'Supplementary'}
        ]
      }
    },
    computed: {
      ...mapGetters([
        'getUser'
      ]),

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      },
    }
  }
</script>

<style scoped>
  .name {
    margin-left: 10px;
    font-weight: bold;
    font-size: 15px;
  }

  .date {
    float: right;
  }
</style>
