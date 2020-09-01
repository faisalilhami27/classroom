@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      <div class="row">
        <div class="col-md-6">
          <div class="panel m-b-lg">
            <ul class="nav nav-tabs nav-justified">
              <li class="active tab1"><a href="#home-11" data-toggle="tab"><span style="color: #FFFFFF">Data User</span></a></li>
              <li><a href="#password-11" data-toggle="tab"><span style="color: #FFFFFF">Ganti Password</span></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active in" id="home-11">
                <div class="demo-form-wrapper">
                  <form class="form form-horizontal" id="frm-website" enctype="multipart/form-data">
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-1">Nama Lengkap</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <input id="name" autocomplete="off" name="name" value="{{ $user->employee->name }}" class="form-control" type="text" placeholder="Nama Lengkap" maxlength="60">
                          <span class="icon icon-user-secret input-icon"></span>
                          <span class="text-danger">
                            <strong id="name-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-1">Username</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <input id="username" autocomplete="off" name="text" value="{{ $user->username }}" class="form-control" type="text" placeholder="Username" maxlength="20">
                          <span class="icon icon-user input-icon"></span>
                          <span class="text-danger">
                            <strong id="username-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-1">Email</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <input id="email" autocomplete="off" maxlength="60" name="email" value="{{ $user->employee->email }}" class="form-control" type="email" placeholder="Email">
                          <span class="icon icon-envelope input-icon"></span>
                          <span class="text-danger">
                            <strong id="email-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-1">No Handphone</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <input id="phone_number" autocomplete="off" maxlength="15" name="phone_number" value="{{ $user->employee->phone_number }}" class="form-control" type="text" placeholder="Email">
                          <span class="icon icon-phone input-icon"></span>
                          <span class="text-danger">
                            <strong id="phone_number-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-5">Foto Profil</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <div class="input-group input-file">
                            <input class="form-control" disabled type="text" placeholder="No file chosen" style="background-color: rgba(0,0,0, 0.1)">
                            <span class="icon icon-paperclip input-icon"></span>
                            <span class="input-group-btn">
                              <label class="btn btn-primary file-upload-btn">
                                <input id="photo" accept="image/*" class="file-upload-input" type="file" name="file">
                                <span class="icon icon-paperclip icon-lg"></span>
                              </label>
                            </span>
                          </div>
                          <strong id="photo-error"></strong>
                          <p class="help-block">
                            <small>Click the button next to the input field.</small>
                          </p>
                        </div>
                      </div>
                    </div>
                    <button class="btn btn-primary" style="margin-left: 36%" id="btn-update-data" type="submit">Submit</button>
                  </form>
                </div>
              </div>
              <div class="tab-pane fade" id="password-11">
                <form class="form form-horizontal" id="form-reset" role="form">
                  <div class="form-group">
                    <label class="col-sm-4" for="form-control-1">Password</label>
                    <div class="col-sm-8">
                      <div class="input-with-icon">
                        <div class="input-group">
                          <input class="form-control form-password" id="password" maxlength="12" type="password" placeholder="Password">
                          <span class="input-group-addon">
                            <label class="custom-control custom-control-primary custom-checkbox">
                              <input class="custom-control-input form-checkbox" type="checkbox">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-label">Show</span>
                            </label>
                          </span>
                        </div>
                        <span class="icon icon-lock input-icon"></span>
                        <span class="text-danger">
                          <strong id="password-error"></strong>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-4" for="form-control-1">Konfirmasi Password</label>
                    <div class="col-sm-8">
                      <div class="input-with-icon">
                        <div class="input-group">
                          <input class="form-control form-password1" id="confirm_password" maxlength="12" type="password" placeholder="Konfirmasi Password">
                          <span class="input-group-addon">
                            <label
                              class="custom-control custom-control-primary custom-checkbox">
                              <input class="custom-control-input form-checkbox1" type="checkbox">
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-label">Show</span>
                            </label>
                          </span>
                        </div>
                        <span class="text-danger">
                          <strong id="password_confirmation-error"></strong>
                        </span>
                        <span class="icon icon-lock input-icon"></span>
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-primary" style="margin-left: 36%; margin-top: 5%" id="btn-reset-pass" type="submit">Submit
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel m-b-lg">
            <ul class="nav nav-tabs nav-justified">
              <li class="active tab1"><a href="#home-11" data-toggle="tab"><span
                    style="font-size: 27px; font-weight: bold">Review Data User</span></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active in" id="home-11">
                <div class="col-md-12">
                  @if(is_null($user->employee->photo))
                    <img class="img-circle center-block"
                         src="{{ Avatar::create(Auth::user()->username)->toBase64() }}"
                         width="128px" height="128px"
                         style="margin-bottom: 5%; border: 2px solid #FFFFFF" alt="Profile">
                  @else
                    <img class="img-circle center-block"
                         src="{{ asset('storage/' . $user->employee->photo) }}"
                         width="128px" height="128px"
                         style="margin-bottom: 5%; border: 3px solid #FFFFFF" alt="Profile">
                  @endif
                </div>
                <div class="card-body">
                  <h3 class="card-title text-center">{{ $user->employee->name }}</h3>
                  <p class="card-text text-center">
                    <small>{{ $user->employee->phone_number }}</small>
                  </p>
                  <p class="card-text text-center">
                    <small>{{ $user->employee->email }}</small>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
      $('.form-checkbox').click(function () {
        if ($(this).is(':checked')) {
          $('.form-password').attr('type', 'text');
        } else {
          $('.form-password').attr('type', 'password');
        }
      });

      $('.form-checkbox1').click(function () {
        if ($(this).is(':checked')) {
          $('.form-password1').attr('type', 'text');
        } else {
          $('.form-password1').attr('type', 'password');
        }
      });

      $('input[type=file]').change(function () {
        const val = $(this).val().toLowerCase(),
          regex = new RegExp("(.*?)\.(png|jpg|jpeg)$");
        if (!(regex.test(val))) {
          $(this).val('');
          alert('Format yang diizinkan png atau jpg');
        } else if (this.files[0].size > 1000024) {
          $(this).val('');
          $("#images-error").html("Maximum file size of 1 MB").fadeIn(1000).fadeOut(5000);
          $("#images-error").css("color", "red");
        }
      });

      $("#btn-update-data").click(function (event) {
        event.preventDefault();

        const name = $("#name").val();
        const username = $("#username").val();
        const email = $("#email").val();
        const phoneNumber = $("#phone_number").val();
        const photo = $('#photo').prop('files')[0];
        const formData = new FormData();

        formData.append('user_id', '{{ Auth::user()->employee_id }}');
        formData.append('name', name);
        formData.append('email', email);
        formData.append('username', username);
        formData.append('phone_number', phoneNumber);
        formData.append('photo', photo);

        $.ajax({
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
          },
          type: "POST",
          dataType: "json",
          cache: false,
          contentType: false,
          processData: false,
          url: "{{ route('profile.update.user') }}",
          data: formData,
          beforeSend: function () {
            loadingBeforeSend();
          },
          success: function (data) {
            notification(data.status, data.message);
            loadingAfterSend();
            setTimeout(function () {
              location.reload();
            }, 1000)
          },
          error: function (resp) {
            loadingAfterSend();
            if (_.has(resp.responseJSON, 'errors')) {
              _.map(resp.responseJSON.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.responseJSON.message)
          }
        });
      });

      $("#form-reset").submit(function (e) {
        e.preventDefault();
        const password = $("#password").val();
        const confirm_password = $("#confirm_password").val();

        $.ajax({
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
          },
          url: "{{ route('profile.reset') }}",
          type: "PUT",
          data: "password=" + password + "&password_confirmation=" + confirm_password,
          dataType: "json",
          success: function (data) {
            notification(data.status, data.message);
            setTimeout(function () {
              location.reload();
            }, 1000)
          },
          error: function (resp) {
            if (_.has(resp.responseJSON, 'errors')) {
              _.map(resp.responseJSON.errors, function (val, key) {
                $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
              })
            }
            alert(resp.responseJSON.message)
          }
        });
      });

      $(function () {

        // We can attach the `fileselect` event to all file inputs on the page
        $(document).on('change', ':file', function () {
          const input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [numFiles, label]);
        });

        // We can watch for our custom `fileselect` event like this
        $(document).ready(function () {
          $(':file').on('fileselect', function (event, numFiles, label) {

            const input = $(this).parents('.input-file').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

            if (input.length) {
              input.val(log);
            } else {
              if (log) alert(log);
            }

          });
        });
      });
    });

    function loadingBeforeSend() {
      $("#btn-update-data").attr('disabled', 'disabled');
      $("#btn-update-data").text('Menyimpan data....');
    }

    function loadingAfterSend() {
      $("#btn-update-data").removeAttr('disabled');
      $("#btn-update-data").text('Submit');
    }
  </script>
@endpush

