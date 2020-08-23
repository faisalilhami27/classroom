<template>
  <div class="text-center">
    <v-menu offset-y>
      <template v-slot:activator="{ on }">
        <v-btn
          v-on="on"
          icon
        >
          <v-avatar :color="color" size="45">
            <span v-if="!photo" class="white--text">{{ avatar }}</span>
            <img
              v-else
              :src="avatar"
              :alt="avatar"
            >
          </v-avatar>
        </v-btn>
      </template>
      <v-list>
        <v-list-item @click.stop="openModal = true">
          <v-list-item-icon>
            <v-icon>person</v-icon>
          </v-list-item-icon>

          <v-list-item-title>Profile</v-list-item-title>
        </v-list-item>

        <v-list-item @click.prevent="logout">
          <v-list-item-icon>
            <v-icon>logout</v-icon>
          </v-list-item-icon>

          <v-list-item-title>Sign Out</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <profile v-model="openModal"></profile>
  </div>
</template>

<script>
  import Profile from "./Profile";
  import {mapGetters} from "vuex";
  export default {
    name: "Account",
    components: {Profile},
    data: function () {
      return {
        studentPhoto: '',
        student: [],
        openModal: false,
        photo: false,
      }
    },
    computed: {
      ...mapGetters([
        'getUser'
      ]),

      token() {
        let token = document.head.querySelector('meta[name="csrf_token"]');
        return token.content
      },

      avatar: function() {
        const user = JSON.parse(this.getUser);
        if (user.photo == null) {
          return user.name.substr(0, 2);
        } else {
          this.photo = true;
          return '/storage/' + user.photo;
        }
      },

      color: function() {
        const user = JSON.parse(this.getUser);
        return user.color;
      }
    },
    methods: {
      logout: function () {
        axios.post('/logout').then(() => {
          this.removeStorage();
          location.reload();
        });
      },

      removeStorage() {
        localStorage.clear();
      },
    }
  }
</script>

<style scoped>

</style>
