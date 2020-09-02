<template>
  <v-app>
    <v-sheet>
      <v-row>
        <v-skeleton-loader
          v-if="firstLoad"
          :loading="loading"
          class="mx-auto"
          max-width="820"
          height="460"
        ></v-skeleton-loader>
        <v-col v-show="!firstLoad" col="12" md="8" sm="12">
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
          <v-card v-if="materials.length > 0" max-width="820">
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
          <v-skeleton-loader
            v-if="firstLoad"
            :loading="loading"
            class="mx-auto"
            max-width="400"
            height="460"
            type="card"
          ></v-skeleton-loader>
          <v-card
            v-show="!firstLoad"
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
                  <playlist :materials="allMaterial" @select-playlist="selectPlaylist"></playlist>
                </div>
              </v-list-item-group>
            </v-list>
          </v-card>
          <br>
          <v-skeleton-loader
            v-if="firstLoad"
            :loading="loading"
            class="mx-auto"
            max-width="400"
            type="card"
          ></v-skeleton-loader>
          <v-card
            v-show="!firstLoad"
            class="mx-auto"
            max-width="400"
          >
            <v-card-text>
              <v-icon>mdi-text-box-multiple</v-icon>
              <div style="color: black; font-size: 15px; display: inline-block"><b>File Pendukung</b></div>
            </v-card-text>
            <v-divider></v-divider>
            <support-file :materials="materials"></support-file>
          </v-card>
        </v-col>
      </v-row>
    </v-sheet>
  </v-app>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Discussion from "./Discussion";
import Playlist from "./Playlist";
import SupportFile from './SupportFile';

export default {
  name: "Learn",
  components: {
    Discussion,
    Playlist,
    SupportFile
  },
  data() {
    return {
      materials: [],
      allMaterial: [],
      discussionList: [],
      loading: true,
      firstLoad: true,
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
        setTimeout(() => {
          if (this.firstLoad) this.firstLoad = false
          this.loading = false;
        }, 2000);
      })
    },

    selectPlaylist(index) {
      this.$emit('select-playlist', index);
      this.getMaterial(index);
    }
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
</style>
