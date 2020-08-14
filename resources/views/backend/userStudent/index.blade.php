@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
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
                    <th>Nama {{ optional(configuration())->type_school == 1 ? 'Mahasiswa' : 'Siswa' }}</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Email Verifikasi</th>
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
            <div class="form-group">
              <label for="username">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" id="username" autocomplete="off" class="form-control" minlength="3" maxlength="30" placeholder="Masukan username">
              <span class="text-danger">
                <strong id="username-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="email_verified">Verifikasi Email <span class="text-danger">*</span></label>
              <select name="email_verified" id="email_verified" class="form-control">
                <option value="">-- Pilih Status --</option>
                <option value="0">Belum diverifikasi</option>
                <option value="1">Sudah diverifikasi</option>
              </select>
              <span class="text-danger">
                <strong id="email_verified-error"></strong>
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
    let table, url, type;

    // load data in datatable
    table = $('#user_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('user.student.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'student.name'},
        {data: 'username'},
        {data: 'student.email'},
        {data: 'email_verified', sClass: 'text-center'},
        {data: 'status', sClass: 'text-center'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2],
          orderable: false
        },
      ],
    });

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_user').modal('show');
      $('.modal-title').text('Edit Data User');
      $('.student_id').hide();
      $('.password').hide();
      url = '{{ route('user.student.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('user.student.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#username').val(data.username);
            $('#status').val(data.status);
            $('#email_verified').val(data.email_verified);
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
        content: 'Data yang dihapus tidak akan dapat dikembalikan.',
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
                url: "{{ route('user.student.delete') }}",
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
                url: "{{ route('user.student.reset') }}",
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
      $('#username').val('');
      $('#status').val('');
      $('#email_verified').val('');
    }
  </script>
@endpush
