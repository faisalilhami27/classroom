@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      <div class="row">
        <div class="col-md-12">
          <div class="panel m-b-lg">
            <ul class="nav nav-tabs nav-justified">
              <li class="active tab1"><a href="#home-11" data-toggle="tab"><h3><span class="icon icon-gear"></span> Data
                    Konfigurasi <span class="icon icon-gear"></span></h3></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active in" id="home-11">
                <div class="demo-form-wrapper">
                  <form class="form form-horizontal" id="form_configuration" enctype="multipart/form-data">
                    <div class="form-group">
                      <label class="col-sm-4" for="school_name">Nama Sekolah</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <input id="school_name" autocomplete="off" maxlength="50" name="school_name"
                                 value="{{ optional($config)->school_name }}" class="form-control" type="text"
                                 placeholder="Nama Sekolah">
                          <span class="icon icon-building input-icon"></span>
                          <span class="text-danger">
                            <strong id="school_name-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="type_school">Jenis Sekolah</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <select name="type_school" id="type_school" class="form-control">
                            <option value="">-- Pilih Jenis Sekolah --</option>
                            @if (!empty($typeSchools))
                              @foreach($typeSchools as $typeSchool)
                                @if (optional($config)->type_school == $typeSchool->id)
                                  <option value="{{ $typeSchool->id }}" selected>{{ $typeSchool->name }}</option>
                                @else
                                  <option value="{{ $typeSchool->id }}">{{ $typeSchool->name }}</option>
                                @endif
                              @endforeach
                            @else
                              <option value="" disabled>Data belum tersedia</option>
                            @endif
                          </select>
                          <span class="icon icon-university input-icon"></span>
                          <span class="text-danger">
                            <strong id="type_school-error"></strong>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="reset_password_employee">Reset Password Karyawan</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <div class="input-group">
                            <input class="form-control form-password-employee" id="reset_password_employee"
                                   value="{{ optional($config)->reset_password_employee }}" name="reset_password_employee" maxlength="30"
                                   minlength="8" type="password" placeholder="Password">
                            <span class="input-group-addon">
                              <label class="custom-control custom-control-primary custom-checkbox">
                                <input class="custom-control-input form-checkbox employee" type="checkbox">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-label">Show</span>
                              </label>
                            </span>
                          </div>
                          <span class="text-danger">
                            <strong id="reset_password_employee-error"></strong>
                          </span>
                          <span class="icon icon-lock input-icon"></span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="reset_password_student">Reset Password Siswa</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <div class="input-group">
                            <input class="form-control form-password-student" id="reset_password_student"
                                   value="{{ optional($config)->reset_password_student }}" name="reset_password_student" maxlength="30"
                                   minlength="8" type="password" placeholder="Password">
                            <span class="input-group-addon">
                              <label class="custom-control custom-control-primary custom-checkbox">
                                <input class="custom-control-input form-checkbox student" type="checkbox">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-label">Show</span>
                              </label>
                            </span>
                          </div>
                          <span class="text-danger">
                            <strong id="reset_password_student-error"></strong>
                          </span>
                          <span class="icon icon-lock input-icon"></span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-4" for="form-control-5">Logo Sekolah</label>
                      <div class="col-sm-8">
                        <div class="input-with-icon">
                          <div class="input-group input-file">
                            <input class="form-control" disabled type="text" id="image" placeholder="No file chosen"
                                   style="background-color: rgba(0,0,0, 0.1)">
                            <span class="icon icon-paperclip input-icon"></span>
                            <span class="input-group-btn">
                              <label class="btn btn-primary file-upload-btn">
                                <input id="school_logo" accept="image/*" class="file-upload-input" type="file"
                                       name="school_logo">
                                <span class="icon icon-paperclip icon-lg"></span>
                              </label>
                            </span>
                          </div>
                          <p class="help-block">
                            <small>Click the button next to the input field.</small>
                          </p>
                        </div>
                      </div>
                    </div>
                    <button style="float: right" type="submit" class="btn btn-primary" id="btn-submit">Submit
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  <script>
    // submit data configuration
    $('#btn-submit').click(function (e) {
      e.preventDefault();
      const form = $('#form_configuration')[0];
      const formData = new FormData(form);
      $.ajax({
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        url: '{{ route('configuration.store') }}',
        type: 'post',
        contentType: false,
        cache: false,
        processData: false,
        data: formData,
        dataType: 'json',
        beforeSend: function () {
          $("#btn-submit").attr('disabled', 'disabled');
          $("#btn-submit").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
        },
        success: function (resp) {
          $("#btn-submit").removeAttr('disabled');
          $("#btn-submit").html('Submit');
          notification(resp.status, resp.message);
          setTimeout(function () {
            location.reload();
          }, 1000);
        },
        error: function (resp) {
          $("#btn-submit").removeAttr('disabled');
          $("#btn-submit").html('Submit');
          if (_.has(resp.responseJSON, 'errors')) {
            _.map(resp.responseJSON.errors, function (val, key) {
              $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
            })
          }
          alert(resp.responseJSON.message)
        }
      });
    });

    // show hide password
    $('.employee').click(function () {
      if ($(this).is(':checked')) {
        $('.form-password-employee').attr('type', 'text');
      } else {
        $('.form-password-employee').attr('type', 'password');
      }
    });

    // show hide password
    $('.student').click(function () {
      if ($(this).is(':checked')) {
        $('.form-password-student').attr('type', 'text');
      } else {
        $('.form-password-student').attr('type', 'password');
      }
    });


    // check whether the extension file is suitable or not
    $('input[type=file]').change(function () {
      var val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(png|jpg|jpeg)$");

      if (!(regex.test(val))) {
        $(this).val('');
        alert('Format yang diizinkan png, jpeg dan jpg');
      }
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
  </script>
@endpush
