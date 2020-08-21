<template>
  <v-app>
    <v-sheet>
      <v-row>
        <v-col col="12" md="8" sm="12">
          <div v-if="materials.length > 0">
            <div v-for="(item, index) in materials" :key="index">
              <div v-if="item.video_link != null">
                <vue-plyr ref="plyr">
                  <div class="plyr__video-embed" id="player">
                    <iframe
                      :src="item.video_link"
                      allowfullscreen allowtransparency>
                    </iframe>
                  </div>
                </vue-plyr>
              </div>
              <div v-else>
                <img :src="`/backend/img/video_not_available.png`" width="820" height="460" alt="not available">
                <div class="centered">Tidak ada materi video</div>
              </div>
            </div>
          </div>
          <div v-else>
            <h2 class="text-center">Belum ada materi untuk kelas ini</h2>
          </div>
          <br>
          <v-card max-width="820">
            <v-card-text>
              <v-icon>mdi-forum</v-icon>
              <div style="color: black; font-size: 15px; display: inline-block"><b>Diskusi Materi</b></div>
            </v-card-text>
            <v-divider></v-divider>
            <div v-for="(item, index) in materials" :key="index">
              <discussion :material-id="item.id"></discussion>
            </div>
          </v-card>
        </v-col>
        <v-col col="12" md="4" sm="12">
          <v-card
            class="mx-auto"
            max-width="400"
            height="460"
          >
            <v-card-text>
              <v-icon>mdi-playlist-play</v-icon>
              <div style="color: black; font-size: 15px; display: inline-block"><b>Playlist Materi</b></div>
            </v-card-text>
            <v-divider></v-divider>
            <v-list dense>
              <v-list-item-group v-model="item" color="primary">
                <div style="overflow: auto">
                  <v-list-item
                    v-for="(item, index) in allMaterial"
                    :key="index"
                    @click.prevent="getMaterial((index + 1))"
                  >
                    <v-list-item-content>
                      <v-list-item-title>
                        {{ item.position }}. {{ item.title }}
                      </v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </div>
              </v-list-item-group>
            </v-list>
          </v-card>
          <br>
          <v-card
            class="mx-auto"
            max-width="400"
          >
            <v-card-text>
              <v-icon>mdi-text-box-multiple</v-icon>
              <div style="color: black; font-size: 15px; display: inline-block"><b>File Pendukung</b></div>
            </v-card-text>
            <v-divider></v-divider>
            <v-list dense>
              <v-list-item
                v-for="(item, index) in materials"
                :key="index"
                @click=""
              >
                <v-list-item-content>
                  <div v-if="item.module == null && item.archive == null">
                    <v-list-item-title>
                      <v-chip
                        class="ma-2"
                        color="red"
                        text-color="white"
                      >
                        <v-avatar left>
                          <v-icon>mdi-close-circle</v-icon>
                        </v-avatar>
                        File tidak tersedia
                      </v-chip>
                    </v-list-item-title>
                  </div>
                  <div v-else>
                    <v-list-item-title v-if="item.module != null">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-chip
                            v-bind="attrs"
                            v-on="on"
                            class="ma-2"
                            color="primary"
                            text-color="white"
                            @click.prevent="download(item.module)"
                          >
                            <v-avatar left>
                              <v-icon>mdi-download-circle</v-icon>
                            </v-avatar>
                            {{ splitLengthFilename(splitFilename('module', item.module)) }}
                          </v-chip>
                        </template>
                        <span>Klik untuk download modul</span>
                      </v-tooltip>
                    </v-list-item-title>
                    <v-list-item-title v-if="item.archive != null">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-chip
                            v-bind="attrs"
                            v-on="on"
                            class="ma-2"
                            color="purple"
                            text-color="white"
                            @click.prevent="download(item.archive)"
                          >
                            <v-avatar left>
                              <v-icon>mdi-download-circle</v-icon>
                            </v-avatar>
                            {{ splitLengthFilename(splitFilename('archive', item.archive)) }}
                          </v-chip>
                        </template>
                        <span>Klik untuk download file</span>
                      </v-tooltip>
                    </v-list-item-title>
                  </div>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>
    </v-sheet>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Discussion from "./Discussion";

export default {
  name: "Learn",
  components: {Discussion},
  data() {
    return {
      materials: [],
      allMaterial: [],
      discussionList: [],
      page: 1,
      paginate: 1,
      item: 0,
      isShow: false,
      disableTextAnswer: false,
      clickBtnAnswer: null,
      message: '',
      messageAnswer: '',
      isShowAnswerList: false,
    }
  },
  mounted() {
    this.getMaterial();
  },
  computed: {
    ...mapGetters([
      'getUrl',
      'getUser',
      'getColor',
      'getSubject',
      'getClassId',
      'getSubjectId',
    ]),

    pageTitle: function () {
      return this.getSubject;
    },
  },
  methods: {
    ...mapActions({
      setAlert: "setAlert"
    }),

    splitFilename(type, filename) {
      let result;
      switch (type) {
        case "module":
          result = filename.split('e-learning/module/').join('');
          break;
        case "archive":
          result = filename.split('e-learning/archive/').join('');
          break;
        default:
          result = 'Undefined';
          break;
      }
      return result;
    },

    splitLengthFilename(filename) {
      const length = filename.length;
      if (length > 30) {
        return filename.substr(0, 30) + ' ...';
      } else {
        return filename;
      }
    },

    download(url) {
      window.open("/storage/" + url, '_blank');
    },

    getMaterial(index = '') {
      this.materials = [];
      const page = (index === '') ? this.page : index;
      axios.get('/e-learning/get/material', {
        params: {
          page: page,
          subject_id: this.getSubjectId,
          class_id: this.getClassId
        }
      }).then(response => {
        this.materials = response.data.material.data;
        this.allMaterial = response.data.all;
      })
    },
  },
}
</script>

<style scoped>
.centered {
  position: absolute;
  top: 52.6%;
  left: 26%;
  font-weight: bold;
  font-size: 18px;
}

.slide-enter-active {
  -moz-transition-duration: 0.3s;
  -webkit-transition-duration: 0.3s;
  -o-transition-duration: 0.3s;
  transition-duration: 0.3s;
  -moz-transition-timing-function: ease-in;
  -webkit-transition-timing-function: ease-in;
  -o-transition-timing-function: ease-in;
  transition-timing-function: ease-in;
}

.slide-leave-active {
  -moz-transition-duration: 0.3s;
  -webkit-transition-duration: 0.3s;
  -o-transition-duration: 0.3s;
  transition-duration: 0.3s;
  -moz-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  -webkit-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  -o-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
}

.slide-enter-to, .slide-leave {
  max-height: 100px;
  overflow: hidden;
}

.slide-enter, .slide-leave-to {
  overflow: hidden;
  max-height: 0;
}

.clearfix {
  clear: both;
}

.name {
  display: inline-block;
  margin-left: 5px;
}

.action {
  float: right;
  position: relative;
  left: 10px;
}
</style>
