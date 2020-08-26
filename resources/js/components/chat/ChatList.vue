<template>
  <div>
    <div v-for="(item, index) in chats" :key="index">
      <div v-if="checkGuard === 'employee'">
        <div v-if="item.type == 'text'">
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
              <v-icon v-if="item.status_read === 0" class="read" size="14">mdi-check-all</v-icon>
              <v-icon v-else class="read" :color="newColor" size="14">mdi-check-all</v-icon>
            </v-card-text>
          </v-card>
        </div>
        <div v-else>
          <div v-for="(file, idx) in item.files" :key="idx">
            <div class="chat_guest" v-if="user !== item.employee_id">
              <v-card
                v-if="item.type == 'image'"
                class="d-inline-block"
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-container>
                  <p class="time-image">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <div style="clear: both"></div>
                  <v-row>
                    <v-col cols="auto">
                      <v-img
                        style="border-radius: 10px"
                        height="150"
                        width="150"
                        :src="`/storage/${file.file}`"
                      ></v-img>
                    </v-col>
                  </v-row>
                  <p style="margin-bottom: -10px; margin-top: -10px;"><span
                    style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </p>
                </v-container>
              </v-card>
              <v-card
                v-else
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-card-text>
                  <p class="time-image">
                    {{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <v-chip
                    class="ma-2"
                    color="indigo"
                    text-color="white"
                  >
                    <v-avatar left>
                      <v-icon>mdi-account-circle</v-icon>
                    </v-avatar>
                    <span style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </v-chip>
                </v-card-text>
              </v-card>
            </div>
            <div class="my_chat" v-else>
              <v-card
                v-if="item.type == 'image'"
                class="d-inline-block"
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-container>
                  <p class="time-image">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <div style="clear: both"></div>
                  <v-row>
                    <v-col cols="auto">
                      <v-img
                        style="border-radius: 10px"
                        height="150"
                        width="150"
                        :src="`/storage/${file.file}`"
                      ></v-img>
                    </v-col>
                  </v-row>
                  <p style="margin-bottom: -10px; margin-top: -10px;"><span
                    style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </p>
                  <v-icon v-if="item.status_read === 0" class="read" size="14">mdi-check-all</v-icon>
                  <v-icon v-else class="read" :color="newColor" size="14">mdi-check-all</v-icon>
                </v-container>
              </v-card>
              <v-card
                v-else
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-card-text>
                  <div>
                    <p class="time-image">
                      {{ item.created_at | convertFormatDatetimeToTime }}</p>
                    <v-chip
                      class="ma-2"
                      color="indigo"
                      text-color="white"
                    >
                      <v-avatar left>
                        <v-icon>mdi-account-circle</v-icon>
                      </v-avatar>
                      <span style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                    </v-chip>
                  </div>
                  <v-icon v-if="item.status_read === 0" class="read-file" size="14">mdi-check-all</v-icon>
                  <v-icon v-else class="read-file" :color="newColor" size="14">mdi-check-all</v-icon>
                </v-card-text>
              </v-card>
            </div>
            <div style="clear: both"></div>
          </div>
        </div>
        <div style="clear: both"></div>
        <br>
      </div>
      <div v-else>
        <div v-if="item.type === 'text'">
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
              <v-icon v-if="item.status_read === 0" class="read" size="14">mdi-check-all</v-icon>
              <v-icon v-else class="read" :color="newColor" size="14">mdi-check-all</v-icon>
            </v-card-text>
          </v-card>
        </div>
        <div v-else>
          <div v-for="(file, idx) in item.files" :key="idx">
            <div class="chat_guest" v-if="user !== item.student_id">
              <v-card
                v-if="item.type == 'image'"
                class="d-inline-block"
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-container>
                  <p class="time-image">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <div style="clear: both"></div>
                  <v-row>
                    <v-col cols="auto">
                      <v-img
                        style="border-radius: 10px"
                        height="150"
                        width="150"
                        :src="`/storage/${file.file}`"
                      ></v-img>
                    </v-col>
                  </v-row>
                  <p style="margin-bottom: -10px; margin-top: -10px;"><span
                    style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </p>
                </v-container>
              </v-card>
              <v-card
                v-else
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-card-text>
                  <p class="time-image">
                    {{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <v-chip
                    class="ma-2"
                    color="indigo"
                    text-color="white"
                  >
                    <v-avatar left>
                      <v-icon>mdi-account-circle</v-icon>
                    </v-avatar>
                    <span style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </v-chip>
                </v-card-text>
              </v-card>
            </div>
            <div class="my_chat" v-else>
              <v-card
                v-if="item.type == 'image'"
                class="d-inline-block"
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-container>
                  <p class="time-image">{{ item.created_at | convertFormatDatetimeToTime }}</p>
                  <div style="clear: both"></div>
                  <v-row>
                    <v-col cols="auto">
                      <v-img
                        style="border-radius: 10px"
                        height="150"
                        width="150"
                        :src="`/storage/${file.file}`"
                      ></v-img>
                    </v-col>
                  </v-row>
                  <p style="margin-bottom: -10px; margin-top: -10px;"><span
                    style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                  </p>
                  <v-icon v-if="item.status_read === 0" class="read" size="14">mdi-check-all</v-icon>
                  <v-icon v-else class="read" :color="newColor" size="14">mdi-check-all</v-icon>
                </v-container>
              </v-card>
              <v-card
                v-else
                max-width="300"
                @click="openFile(file.file)"
              >
                <v-card-text>
                  <div>
                    <p class="time-image">
                      {{ item.created_at | convertFormatDatetimeToTime }}</p>
                    <v-chip
                      class="ma-2"
                      color="indigo"
                      text-color="white"
                    >
                      <v-avatar left>
                        <v-icon>mdi-account-circle</v-icon>
                      </v-avatar>
                      <span style="font-size: 14px">{{ splitFilename(file.filename) }}</span>
                    </v-chip>
                  </div>
                  <v-icon v-if="item.status_read === 0" class="read-file" size="14">mdi-check-all</v-icon>
                  <v-icon v-else class="read-file" :color="newColor" size="14">mdi-check-all</v-icon>
                </v-card-text>
              </v-card>
            </div>
            <div style="clear: both"></div>
          </div>
        </div>
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
  props: ['chats', 'color'],
  data() {
    return {
      newColor: (this.color === '') ? 'blue' : this.color
    }
  },
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
  methods: {
    splitFilename(filename) {
      const length = filename.length;
      if (length > 15) {
        return filename.substr(0, 15) + ' ...';
      } else {
        return filename
      }
    },

    openFile(url) {
      window.open('/storage/' + url, '_blank');
    }
  }
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

.time-image {
  margin-bottom: -10px;
  margin-top: -10px;
  float: right;
  font-size: 11px;
}

.read {
  float: right;
  margin-top: 3px;
  margin-bottom: 3px;
}

.read-file {
  float: right;
  margin-bottom: 50px;
}
</style>
