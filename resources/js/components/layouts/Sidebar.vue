<template>
  <v-navigation-drawer
    v-model="drawerOpen"
    temporary
    absolute>
    <v-img :aspect-ratio="16/9" :src="setImage">
      <v-row align="end" class="lightbox white--text pa-2 fill-height">
        <v-col>
          <div class="subheading"><span class="name">{{ setName }}</span></div>
          <div class="body-1"><span class="email">{{ setEmail }}</span></div>
        </v-col>
      </v-row>
    </v-img>
    <v-divider></v-divider>
    <div v-if="checkGuard === 'employee'">
      <v-list dense>
        <v-list-item
          class="list-class"
          :href="backToDashboard"
          link
        >
          <v-avatar color="orange" size="36">
            <span class="white--text">Da</span>
          </v-avatar>

          <v-list-item-content>
            <v-list-item-title class="title-class">Dashboard</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </div>
    <v-divider></v-divider>
    <v-list dense>
      <h4 class="title-class">Daftar Kelas</h4>
      <v-list-item
        class="list-class"
        :href="'/home'"
        link
      >
        <v-avatar color="green" size="36">
          <span class="white--text">Se</span>
        </v-avatar>

        <v-list-item-content>
          <v-list-item-title class="title-class">Semua Kelas</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-list-item
        class="list-class"
        v-for="(item ,index) in dataClass"
        :key="index"
        @click="url(item.id, item.subject.split(' ').join('-'), item.subject_id)"
        link
      >
        <v-avatar :color="item.color" size="36">
          <span class="white--text">{{ item.subject.substr(0, 2) }}</span>
        </v-avatar>

        <v-list-item-content>
          <v-list-item-title class="title-class">{{ item.subject }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <v-divider></v-divider>
  </v-navigation-drawer>
</template>

<script>
  import {mapActions, mapGetters} from "vuex";

  export default {
    name: "Sidebar",
    props: {
      value: {
        type: Boolean
      }
    },
    data: function () {
      return {
        dataClass: [],
        name: '',
        email: '',
        image: '',
        drawerOpen: this.value
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
        'getUser'
      ]),

      setImage: function() {
        return '/images/random28.jpg';
      },

      setName: function () {
        const user = JSON.parse(this.getUser);
        return this.name = user.name;
      },

      setEmail: function () {
        const user = JSON.parse(this.getUser);
        return this.email = user.email;
      },

      backToDashboard: function () {
        return '/';
      },

      checkGuard: function() {
        const user = JSON.parse(this.getUser);
        return user.guard;
      }
    },
    watch: {
      value: function () {
        this.drawerOpen = this.value
      },
      drawerOpen: function () {
        this.$emit('input', this.drawerOpen)
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
    }
  }
</script>

<style scoped>
  .title-class {
    margin-left: 20px;
  }

  .list-class {
    margin-top: 15px;
  }

  .title-class {
    margin-left: 20px;
  }

  .name, .email {
    color: white;
    font-weight: bold;
  }
</style>
