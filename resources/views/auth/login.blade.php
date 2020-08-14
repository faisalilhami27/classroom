@extends('auth.layouts.app')
@section('title', 'Login')
@push('styles')
  <style>
    @font-face {
      font-family: "Beyond";
      src: url("{{ asset('fonts/Quite.ttf') }}");
    }

    .text-quote {
      font-family: Beyond, serif;
      font-size: 24px;
      color: #353535;
      text-shadow: 0 0 6px #616161;
    }

    .quote_second {
      display: none;
      width: 40%;
      position: absolute;
      top: 30%;
      left: 59%;
      text-align: center
    }

    .quote {
      width: 60%;
      position: absolute;
      top: 30%;
      left: 36%;
      text-align: center
    }
  </style>
@endpush
@section('content')
  <div class="container-fluid register">
    <div class="row">
      <div class="col-md-3 register-left">
        @if (is_null(optional(configuration())->school_logo))
          <img src="https://image.ibb.co/n7oTvU/logo_white.png" alt=""/>
        @else
          <img src="{{ asset('storage/' . optional(configuration())->school_logo) }}" alt=""/>
        @endif
        <h3>Classroom</h3>
        <p>Media belajar online terbaik untuk siswa dan guru</p>
      </div>
      <div class="col-md-9 register-right">
        <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login"
               aria-selected="true"><span style="top: -8px; position: relative">Login</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="newuser-tab" data-toggle="tab" href="#newuser" role="tab" aria-controls="newuser"
               aria-selected="false"><span style="top: -8px; position: relative">Buat Akun Siswa</span></a>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <h3 class="register-heading">Login</h3>
            <div class="row" style="margin-top: 20px">
              <div class="col-md-12 profile_card">
                <form id="form-login" method="post">
                  <div class="form-group">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                      </div>
                      <input type="text" v-model="username" class="form-control" autofocus autocomplete="off"
                             placeholder="Username" id="username" name="username">
                    </div>
                    <span class="text-danger" style="top: -20px; position: relative">
                      <strong id="username-error" style="font-size: 13px"></strong>
                    </span>
                  </div>
                  <div class="form-group">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                      </div>
                      <input type="password" v-model="password" class="form-control" placeholder="Password"
                             id="password" name="password">
                    </div>
                    <span class="text-danger" style="top: -20px; position: relative">
                      <strong id="password-error" style="font-size: 13px"></strong>
                    </span>
                  </div>
                  <div class="form-group">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" @change="checkUser" id="check">
                      <label class="form-check-label" for="check">
                        <span style="top: -2.5px; position: relative">Login Guru/Admin</span>
                      </label>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary btn-block" :disabled="isDisabledButtonLogin"
                          v-html="loadingButtonLogin" @click.prevent="loginProcess()"></button>
                </form>
              </div>
            </div>
          </div>
          <div class="tab-pane fade show" id="newuser" role="tabpanel" aria-labelledby="newuser-tab">
            <h3 class="register-heading">Buat Akun Siswa</h3>
            <div class="row register-form">
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" max="20" v-model="student_identity_number" required class="form-control"
                         placeholder="Masukan {{ optional(configuration())->type_school == 1 ? 'NPM' : 'NIPD' }} *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="student_identity_number-error" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" autocomplete="off" max="70" v-model="name" required
                         placeholder="Masukan nama lengkap *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="name-error" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <input type="text" max="20" autocomplete="off" v-model="username_register" required
                         class="form-control" placeholder="Masukan username *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="username_register-error" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <input type="email" maxlength="70" autocomplete="off" v-model="email" required class="form-control"
                         placeholder="Masukan email *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="email-error" style="font-size: 13px"></strong>
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" autocomplete="off" maxlength="15" minlength="10" v-model="phone_number" required
                         class="form-control" placeholder="Masukan nomor hp *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="phone_number-error" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <input type="password" minlength="8" v-model="password_register" required class="form-control"
                         placeholder="Masukan password *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="password_register-error" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <input type="password" minlength="8" v-model="password_confirmation" required class="form-control"
                         @keyup="checkMatchingPassword()" placeholder="Konfirmasi Password *"/>
                  <span class="text-danger" style="position: relative">
                    <strong id="password_confirmation-error" v-text="render_text" style="font-size: 13px"></strong>
                  </span>
                </div>
                <div class="float-right">
                  <button type="submit" @click.prevent="registerProcess()" :disabled="isDisabledButtonRegister"
                          class="btn btn-primary btn-register" v-html="loadingButtonRegister">Register
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="quote">
          <p class="text-quote">"Ilmu yang sejati, seperti barang berharga lainnya, tidak bisa diperoleh dengan mudah.
            Ia harus diusahakan, dipelajari, dipikirkan, dan lebih dari itu, harus selalu disertai doa."</p>
        </div>
        <div class="quote_second">
          <p class="text-quote">"Bukan ilmu yang seharusnya mendatangimu, tapi kamu yang seharusnya mendatangi
            ilmu."</p>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  <script>
    @if ($message = Session::get('error'))
    notification(500, '{{ $message }}')
    @elseif ($message = Session::get('success'))
    notification(200, '{{ $message }}')
    @endif
    new Vue({
      el: '#app',
      data: {
        student_identity_number: '',
        username: '',
        password: '',
        username_register: '',
        password_register: '',
        name: '',
        email: '',
        phone_number: '',
        password_confirmation: '',
        render_text: '',
        checkGuard: 'student',
        isChecked: false,
        isDisabledRegister: false,
        isDisabledLogin: false,
        loading: false
      },
      computed: {
        loadingButtonRegister: function () {
          if (this.loading) {
            return '<i class="fa fa-spinner fa-spin"></i> Loading...';
          } else {
            return 'Register';
          }
        },

        loadingButtonLogin: function () {
          if (this.loading) {
            return '<i class="fa fa-spinner fa-spin"></i> Loading...';
          } else {
            return 'Login';
          }
        },

        isDisabledButtonRegister: function () {
          if (this.isDisabledRegister) {
            return this.isDisabledRegister = true;
          } else {
            return this.isDisabledRegister = false;
          }
        },

        isDisabledButtonLogin: function () {
          if (this.isDisabledLogin) {
            return this.isDisabledLogin = true;
          } else {
            return this.isDisabledLogin = false;
          }
        },
      },
      methods: {
        loginProcess: function () {
          this.loading = true;
          this.isDisabledLogin = true;
          axios({
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            method: 'post',
            url: "{{ route('login') }}",
            data: "username=" + this.username + "&password=" + this.password + '&loginAs=' + this.checkGuard,
            dataType: 'json'
          }).then(resp => {
            this.isDisabledLogin = false;
            this.loading = false;
            notification(resp.data.status, resp.data.message);
            document.getElementById('check').checked = false;
            this.username = '';
            this.password = '';
            this.checkGuard = 'student';
            if (resp.data.status === 200) {
              this.loading = false;
              setTimeout(function () {
                if (resp.loginAs === 'student') {
                  window.location.href = "{{ route('home') }}";
                } else {
                  window.location.href = "{{ route('role.pickList') }}";
                }
              }, 3000);
            }
          }).catch(resp => {
            this.isDisabledLogin = false;
            this.loading = false;
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(3000);
              })
            }
            alert(resp.response.data.message)
          })
        },

        registerProcess: function () {
          this.isDisabledRegister = true;
          this.loading = true;
          const url = '{{ route('register.create') }}';
          axios({
            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            method: 'post',
            url: url,
            data: "student_identity_number=" + this.student_identity_number +
              "&name=" + this.name +
              "&email=" + this.email +
              "&username_register=" + this.username_register +
              "&phone_number=" + this.phone_number +
              "&password_register=" + this.password_register +
              "&password_confirmation=" + this.password_confirmation,
            dataType: 'json'
          }).then(resp => {
            this.isDisabledRegister = false;
            this.loading = false;
            notification(resp.data.status, resp.data.message);
            if (resp.data.status === 200) {
              this.loading = false;
              setTimeout(function () {
                window.location.href = "{{ route('login') }}";
              }, 3000);
            }
          }).catch(resp => {
            this.isDisabledRegister = false;
            this.loading = false;
            if (_.has(resp.response.data, 'errors')) {
              _.map(resp.response.data.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.response.data.message)
          })
        },

        checkUser: function (e) {
          if (e.target.checked) {
            this.checkGuard = 'employee';
            this.isChecked = true;
          } else {
            this.isChecked = false;
            this.checkGuard = 'student';
          }
        },

        resetFormLogin: function () {
          this.username = '';
          this.password = '';
          this.checkGuard = '';
        },

        checkMatchingPassword: function () {
          const password = this.password_register;
          const passwordConfirmation = this.password_confirmation;
          const button = document.querySelector('.btn-register');
          if (password !== passwordConfirmation) {
            this.render_text = 'Password tidak sama';
            button.disabled = true;
          } else {
            this.render_text = '';
            button.disabled = false;
          }
        }
      }
    });

    $('#newuser').hide();

    $("#newuser-tab").click(function () {
      $('.quote').hide();
      $('#newuser').show();
      $('.quote_second').fadeIn(1000);
    });

    $("#login-tab").click(function () {
      $('#newuser').hide();
      $('.quote').fadeIn(2500);
      $('.quote_second').fadeOut(200);
    });
  </script>
@endpush
