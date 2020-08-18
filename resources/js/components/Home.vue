<template>
  <v-app id="inspire">
    <v-sheet
      height="100%"
      class="overflow-hidden"
      style="position: relative; border-radius: 0px"
    >
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
          <v-toolbar-title><b>Classroom</b></v-toolbar-title>
          <v-spacer></v-spacer>
          <div style="margin-right: 10px">
            <chat></chat>
          </div>
          <div v-if="checkGuard === 'employee'" style="margin-right: 20px">
            <announcement></announcement>
          </div>
          <div v-else>
            <announcement></announcement>
          </div>
          <v-btn v-if="checkGuard === 'student'" @click.stop="dialog = true" icon>
            <v-icon>mdi-plus</v-icon>
          </v-btn>
          <account></account>
        </v-toolbar>
      </v-card>
      <!-- sidebar -->
      <sidebar v-model="openLeftNavigationDrawer"></sidebar>
      <v-container class="lighten-5" fluid>
        <v-row no-gutters>
          <v-col
            v-if="!showHide"
            cols="12"
            sm="12"
            v-html="showClass"
            class="list-class null"
          >
          </v-col>
          <v-col
            v-else
            v-for="(item, index) in dataClass"
            :key="index"
            cols="12"
            sm="4"
            class="list-class"
          >
            <v-card
              class="mx-auto"
              max-width="350"
            >
              <v-img
                class="white--text align-end"
                height="200px"
                :src="item.image"
              >
                <v-card-title>{{ item.subject }}</v-card-title>
              </v-img>
              <v-card-text class="text--primary">
                <div>{{ item.class }}</div>

                <div>{{ item.school_year }}</div>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  color="blue"
                  text
                  @click="url(item.id, item.subject.split(' ').join('-'), item.subject_id)"
                >
                  Mulai Belajar
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <v-row justify="center">
        <v-dialog
          v-model="dialog"
          max-width="290"
        >
          <v-card>
            <v-card-title class="headline">Gabung ke kelas</v-card-title>

            <v-card-text>
              Mintalah kode kelas kepada pengajar untuk bisa bergabung ke kelas.
            </v-card-text>
            <v-container class="code">
              <v-col
                cols="12"
                sm="12"
              >
                <v-text-field
                  v-model="code"
                  :rules="codeRules"
                  label="Kode Kelas"
                  maxLength="7"
                  autocomplete="off"
                  @keyup="countCode"
                  required
                ></v-text-field>
              </v-col>
            </v-container>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="red darken-1"
                text
                @click="dialog = false"
              >
                Batal
              </v-btn>

              <v-btn
                color="blue darken-1"
                text
                @click.prevent="joinClass"
                :disabled="disabledButton !== true"
              >
                Gabung
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-sheet>
  </v-app>
</template>

<script>
  import Sidebar from "./layouts/Sidebar";
  import Account from "./other/Account";
  import Chat from "./chat/Chat";
  import axios from 'axios';
  import {mapActions, mapGetters} from 'vuex';
  import Announcement from "./other/Announcement";

  export default {
    name: 'home',
    components: {
      Announcement,
      Sidebar,
      Account,
      Chat
    },
    data: function() {
      return {
        showClass: '',
        showHide: false,
        dataClass: [],
        disabledButton: false,
        totalLength: 0,
        code: '',
        dialog: false,
        openLeftNavigationDrawer: false,
        codeRules: [
          v => !!v || 'Kode harus diisi',
          v => v.length >= 7 || 'Kode harus 7 karakter',
        ],
      }
    },
    mounted: function () {
      const user = JSON.parse(this.getUser);
      axios.get('/class/get/user/class', {
        params: {
          user_id: user.user_id
        }
      }).then(response => {
        if (response.data.status === 200) {
          const data = response.data.list;
          if (data != "") {
            this.showHide = true;
            this.dataClass = data;
          } else {
            this.showHide = false;
            this.showClass = `<div>
              <p class="text-center result-text"><b>Belum ada kelas yang anda ikuti</b></p>
            </div>`;
          }
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
    computed: {
      ...mapGetters([
        'getUrl',
        'getUser',
        'getColor'
      ]),

      checkGuard: function () {
        const user = JSON.parse(this.getUser);
        return user.guard;
      }
    },
    methods: {
      ...mapActions({
        setAlert: 'setAlert',
        setSubject: 'setSubject',
        setClassId: 'setClassId',
        setSubjectId: 'setSubjectId',
      }),

      url: function(id, subject, subjectId) {
        this.setSubject(subject);
        this.setClassId(id);
        this.setSubjectId(subjectId);
        location.href = `/detail/${id}/${subject}`;
      },

      countCode: function () {
        this.totalLength = this.code.length;
        this.disabledButton = this.totalLength === 7;
      },

      joinClass: function () {
        const user = JSON.parse(this.getUser);
        axios.post('/class/join/class', {
          code: this.code,
          user_id: user.user_id
        })
          .then(response => {
            this.code = '';
            this.disabledButton = false;
            this.setAlert({
              message: response.data.message,
              status: response.data.status,
            });
            if (response.data.status === 200) {
              this.dialog = false;
              setTimeout(function() {
                location.reload();
              }, 2000);
            }
          })
          .catch(resp => {
            alert(resp.response.data.message);
          })
      }
    }
  }
</script>

<style>
  #lateral .v-btn--example {
    bottom: 0;
    position: absolute;
    margin: 0 0 16px 16px;
  }

  .list-class {
    margin-top: 17px;
  }

  .code {
    margin-top: -20px;
  }

  .result-text {
    font-size: 30px;
  }
</style>
