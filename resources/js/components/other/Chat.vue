<template>
  <v-btn @click="openLink" icon>
    <v-icon>mdi-message</v-icon>
  </v-btn>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "Chat",
  computed: {
    ...mapGetters([
      'getClassId'
    ])
  },
  methods: {
    openLink() {
      this.$router.push(`/chat/page`);
    }
  }
}
</script>

<style scoped>

</style>
