@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      @if (checkPermission()->create)
        <button class="btn btn-info btn-sm" type="button" onclick="addData()" style="margin-bottom: 10px">
          <i class="icon icon-plus-circle"></i> Tambah
        </button>
      @endif
      <div class="row gutter-xs">
        <div class="col-xs-12">
          <div class="card">
            <div class="card-header">
              <strong>{{ $title }}</strong>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="user_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Nama Karyawan</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Hak Akses</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_user" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_user" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group employee_id">
              <label for="employee_id">Karyawan <span class="text-danger">*</span></label>
              <select name="employee_id" id="employee_id" class="form-control"></select>
              <span class="text-danger">
                <strong id="employee_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="username">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" id="username" autocomplete="off" class="form-control" minlength="3" maxlength="30" placeholder="Masukan username">
              <span class="text-danger">
                <strong id="username-error"></strong>
              </span>
            </div>
            <div class="form-group password">
              <label for="password">Password</label>
              <div class="input-with-icon">
                <div class="input-group">
                  <input class="form-control form-password" id="password" name="password" maxlength="30" minlength="8" type="password" placeholder="Password">
                  <span class="input-group-addon">
                      <label class="custom-control custom-control-primary custom-checkbox">
                        <input class="custom-control-input form-checkbox" type="checkbox">
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-label">Show</span>
                      </label>
                    </span>
                </div>
                <span class="text-danger">
                  <strong id="password-error"></strong>
                </span>
                <span class="icon icon-lock input-icon"></span>
              </div>
            </div>
            <div class="form-group">
              <label for="role_id">Role <span class="text-danger">*</span></label>
              <select name="role_id[]" id="role_id" class="form-control" multiple>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
              <span class="text-danger">
                <strong id="role_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="status">Status <span class="text-danger">*</span></label>
              <select name="status" id="status" class="form-control">
                <option value="">-- Pilih Status --</option>
                <option value="0">Tidak Aktif</option>
                <option value="1">Aktif</option>
              </select>
              <span class="text-danger">
                <strong id="status-error"></strong>
              </span>
            </div>
            <p>Note: *) harus diisi</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            <button class="btn btn-primary btn-submit" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
    let table, url, type, timer, timeout = 1000;

    // load data in datatable
    table = $('#user_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('user.employee.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'employee.name'},
        {data: 'username'},
        {data: 'employee.email'},
        {data: 'permission', sClass: 'text-center'},
        {data: 'status', sClass: 'text-center'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2, -3],
          orderable: false
        },
      ],
    });

    $("#employee_id").select2({
      placeholder: "Pilih Karyawan",
      width: "100%",
      dropdownParent: $("#modal_user")
    });

    $("#role_id").select2({
      width: '100%',
      placeholder: "Pilih Role",
    });

    // get data employee unregistered
    const getUnregisteredEmployees = function () {
      $.ajax({
        url: "{{ route('user.employee.get') }}",
        type: "post",
        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        success: function (response) {
          $(response).each(function (index, value) {
            $("#employee_id").append(`<option value="${value.id}">${value.employee_identity_number} - ${value.name}</option>`)
          })
          $("#employee_id").trigger("change")
        },
        error: function (xhr, status, error) {
          alert(status + ": " + error)
        }
      })
    }

    // show hide password
    $('.form-checkbox').click(function () {
      if ($(this).is(':checked')) {
        $('.form-password').attr('type', 'text');
      } else {
        $('.form-password').attr('type', 'password');
      }
    });

    // show modal add data
    const addData = function () {
      $('#modal_user').modal('show');
      $('.modal-title').text('Tambah Data User');
      url = '{{ route('user.employee.create') }}';
      type = 'post';
      getUnregisteredEmployees();
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_user').modal('show');
      $('.modal-title').text('Edit Data User');
      $('.employee_id').hide();
      $('.password').hide();
      url = '{{ route('user.employee.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('user.employee.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            const roles = resp.roles;
            console.log(roles);
            $('#id').val(data.id);
            $('#username').val(data.username);
            $('#password').val(data.password);
            $('#status').val(data.status);
            $('#role_id').select2({width: "100%"}).val(roles).trigger('change');
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    }

    // delete data
    const deleteData = function (id) {
      $.confirm({
        content: 'Data karyawan dari akun ini juga akan dihapus.',
        title: 'Apakah yakin ingin menghapus ?',
        type: 'red',
        typeAnimated: true,
        buttons: {
          cancel: {
            text: 'Batal',
            btnClass: 'btn-danger',
            keys: ['esc'],
            action: function () {
            }
          },
          ok: {
            text: '<i class="icon icon-trash"></i> Hapus',
            btnClass: 'btn-warning',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('user.employee.delete') }}",
                type: "delete",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  setTimeout(function () {
                    location.reload();
                  }, 1000)
                },
                error: function (xhr, status, error) {
                  alert(status + " : " + error);
                }
              });
            }
          }
        }
      });
    }

    // reset password
    const resetPassword = function (id) {
      $.confirm({
        content: 'Password akan direset sesuai konfigurasi reset password.',
        title: 'Apakah anda yakin ?',
        type: 'red',
        typeAnimated: true,
        buttons: {
          cancel: {
            text: 'Batal',
            btnClass: 'btn-danger',
            keys: ['esc'],
            action: function () {
            }
          },
          ok: {
            text: '<i class="icon icon-refresh"></i> Reset',
            btnClass: 'btn-primary',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('user.employee.reset') }}",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  setTimeout(function () {
                    location.reload();
                  }, 1000)
                },
                error: function (xhr, status, error) {
                  alert(status + " : " + error);
                }
              });
            }
          }
        }
      });
    }

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: $('#form_user').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_user').modal('hide');
          loadingAfterSend();
          resetForm();
          setTimeout(function () {
            location.reload();
          }, 1000);
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

    // method for handle before send data
    const loadingBeforeSend = function () {
      $(".btn-submit").attr('disabled', 'disabled');
      $(".btn-submit").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-submit").removeAttr('disabled');
      $(".btn-submit").html('Submit');
    }

    // reset form
    const resetForm = function () {
      $('#id').val('');
      $('#name').val('');
    }

    // check username whether it has been used or not
    $('#username').on('keyup', function () {
      clearTimeout(timer);
      const username = $('#username').val().trim();
      $('#username-error').html('').addClass('text-danger');
      if (username) {
        if (username !== '') {
          timer = setTimeout(function () {
            $.ajax({
              url: '{{ route('user.employee.username') }}',
              type: 'get',
              data: {username: username},
              dataType: 'json',
              success: function (resp) {
                if (resp.status === 200) {
                  $('#username-error').html('<i class="icon icon-check"></i> ' + resp.message)
                    .removeClass('text-danger')
                    .addClass('text-success');
                  $('.btn-submit').attr('disabled', false);
                } else {
                  $('#username-error').html('<i class="icon icon-close"></i> ' + resp.message)
                    .removeClass('text-success')
                    .addClass('text-danger');
                  $('.btn-submit').attr('disabled', true);
                }
              }
            });
          }, timeout);
        } else {
          $('#username-error').html('');
        }
      }
    });
  </script>
@endpush
