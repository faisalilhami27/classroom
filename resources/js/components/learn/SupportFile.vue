<template>
  <div>
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
  </div>
</template>

<script>
export default {
  name: "SupportFile",
  props: ['materials'],
  methods: {
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
  }
}
</script>

<style scoped>

</style>
