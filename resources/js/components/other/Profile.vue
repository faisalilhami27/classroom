<template>
  <v-row justify="center">
    <v-dialog v-model="show" max-width="600px">
      <v-card>
        <v-card-title
          class="headline title-profile lighten-2"
          primary-title
        >
          Ubah Profil
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="name"
                  :rules="nameRules"
                  label="Nama*" required
                >
                </v-text-field>
                <span class="text-danger">
                  <strong id="name-error"></strong>
                </span>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="username"
                  :rules="usernameRules"
                  label="Username*"
                  required
                >
                </v-text-field>
                <span class="text-danger">
                  <strong id="username-error"></strong>
                </span>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="email"
                  :rules="emailRules"
                  label="Email*"
                  required
                >
                </v-text-field>
                <span class="text-danger">
                  <strong id="email-error"></strong>
                </span>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="phone"
                  :rules="phoneRules"
                  label="Nomor Handphone*"
                  type="number"
                  maxLength="15"
                  required
                >
                </v-text-field>
                <span class="text-danger">
                  <strong id="phone_number-error"></strong>
                </span>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="password"
                  label="Password"
                  type="password"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-file-input
                  v-model="photo"
                  label="Foto"
                >
                </v-file-input>
              </v-col>
            </v-row>
          </v-container>
          <small>*harus diisi</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" text @click="show = false">Close</v-btn>
          <v-btn color="blue darken-1" text @click.prevent="submitForm">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>
ge
<script>
  import axios from 'axios';
  import {mapActions, mapGetters} from "vuex";

  export default {
    name: "Profile",
    data: function () {
      return {
        valid: true,
        photo: [],
        password: '',
        name: '',
        nameRules: [
          v => !!v || 'Nama tidak boleh kosong',
          v => v.length > 3 || 'Nama minimal 3 huruf',
        ],
        username: '',
        usernameRules: [
          v => !!v || 'Username tidak boleh kosong',
          v => v.length > 5 || 'Username minimal 5 huruf',
        ],
        phone: '',
        phoneRules: [
          v => !!v || 'Nomor handphone tidak boleh kosong',
          v => v.length > 10 || 'Nomor handphone minimal 10 angka',
        ],
        email: '',
        emailRules: [
          v => !!v || 'E-mail tidak boleh kosong',
          v => /.+@.+/.test(v) || 'Format email salah',
        ],
      }
    },
    props: {
      value: Boolean,
    },
    computed: {
      ...mapGetters([
        'getUser'
      ]),
      show: {
        get() {
          return this.value
        },
        set(value) {
          this.$emit('input', value)
        }
      }
    },
    mounted() {
      const user = JSON.parse(this.getUser);
      axios.get('/profile/get/user', {
        params: {
          user_id: user.user_id
        }
      }).then(response => {
        if (response.data.status === 200) {
          const data = response.data.list;
          this.name = data.name;
          this.username = data.username;
          this.email = data.email;
          this.phone = data.phone_number;
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
    methods: {
      ...mapActions({
        setAlert: 'setAlert'
      }),

      submitForm: function () {
        const user = JSON.parse(this.getUser);
        let formData = new FormData();
        formData.append('name', this.name);
        formData.append('username', this.username);
        formData.append('email', this.email);
        formData.append('phone_number', this.phone);
        formData.append('password', this.password);
        formData.append('photo', this.photo);
        formData.append('user_id', user.user_id);

        axios.post('/profile/update/user', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
          .then(response => {
            this.setAlert({
              message: response.data.message,
              status: response.data.status
            });
            if (response.data.status === 200) {
              this.show = false;
              const updateItem = response.data.user;
              localStorage.setItem('user', JSON.stringify(updateItem));
              setTimeout(function() {
                location.reload();
              }, 2000);
            }
          })
          .catch(resp => {
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.response.data.message)
          })
      }
    }
  }
</script>

<style scoped>
 .title-profile {
   background-color: #3F51B5;
   color: white;
 }
</style>
