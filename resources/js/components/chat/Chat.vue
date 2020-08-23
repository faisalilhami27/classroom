<template>
  <v-btn @click="openLink" icon>
    <v-icon v-if="count === 0">mdi-message-text</v-icon>
    <v-badge
      v-else
      color="green"
      :content="count"
      overlap
    >
      <v-icon>mdi-message-text</v-icon>
    </v-badge>
  </v-btn>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "Chat",
  data() {
    return {
      count: 0,
    }
  },
  computed: {
    ...mapGetters([
      'getClassId'
    ])
  },
  created() {
    const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
      cluster: process.env.MIX_PUSHER_APP_CLUSTER
    });
    const channel = pusher.subscribe('my-channel');
    channel.bind('my-event', () => {
      this.getChat();
    });
  },
  mounted() {
    this.getChat();
  },
  methods: {
    openLink() {
      location.href = '/chat/page';
    },

    getChat() {
      axios.post('/chat/get/all')
        .then(response => {
          this.count = response.data.count;
        })
        .catch(resp => {
          alert(resp.response.data.message);
        })
    },
  }
}
</script>

<style scoped>

</style>
