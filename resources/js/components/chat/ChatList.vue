<template>
  <div>
    <div v-for="(item, index) in chats" :key="index">
      <div v-if="checkGuard === 'employee'">
        <v-card
          max-width="400"
          class="chat_guest"
          v-if="user !== item.employee_id"
        >
          <v-card-text>
            <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
            <div style="clear: both"></div>
            <p class="message">{{ item.message }}</p>
          </v-card-text>
        </v-card>
        <v-card
          v-else
          max-width="400"
          class="my_chat"
        >
          <v-card-text>
            <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
            <div style="clear: both"></div>
            <p class="message">{{ item.message }}</p>
          </v-card-text>
        </v-card>
        <div style="clear: both"></div>
        <br>
      </div>
      <div v-else>
        <v-card
          max-width="400"
          class="chat_guest"
          v-if="user !== item.student_id"
        >
          <v-card-text>
            <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
            <div style="clear: both"></div>
            <p class="message">{{ item.message }}</p>
          </v-card-text>
        </v-card>
        <v-card
          v-else
          max-width="400"
          class="my_chat"
        >
          <v-card-text>
            <p class="time">{{ item.created_at | convertFormatDatetimeToTime }}</p>
            <div style="clear: both"></div>
            <p class="message">{{ item.message }}</p>
          </v-card-text>
        </v-card>
        <div style="clear: both"></div>
        <br>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import moment from 'moment';

export default {
  name: "ChatList",
  props: ['chats'],
  computed: {
    ...mapGetters([
      'getClassId',
      'getSubject',
      'getUser',
      'getColor'
    ]),

    user: function () {
      const user = JSON.parse(this.getUser);
      return user.user_id;
    },

    checkGuard: function () {
      const user = JSON.parse(this.getUser);
      return user.guard;
    }
  },
  filters: {
    convertFormatDatetimeToTime: function (datetime) {
      return moment(datetime).format('HH:mm');
    }
  },
}
</script>

<style scoped>
.time {
  float: right;
  font-size: 11px;
  margin-top: -13px;
  margin-bottom: -5px;
}

.chat_guest {
  float: left;
  margin: 5px;
}

.my_chat {
  float: right;
  margin: 5px;
}

.message {
  margin-bottom: -5px;
  color: black;
}
</style>
