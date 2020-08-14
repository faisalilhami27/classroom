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
                <table id="student_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>{{ optional(configuration())->type_school == 1 ? 'NPM' : 'NIPD'}}</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No HP</th>
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

  {{-- modal student --}}
  <div id="modal_student" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_student" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="student_identity_number">NIP <span class="text-danger">*</span></label>
              <input type="text" name="student_identity_number" autocomplete="off" id="student_identity_number" class="form-control"
                     placeholder="Masukan NIP" maxlength="16">
              <span class="text-danger">
                <strong id="student_identity_number-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" autocomplete="off" class="form-control" placeholder="Masukan Nama Lengkap"
                     maxlength="70">
              <span class="text-danger">
                <strong id="name-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="email">Email <span class="text-danger">*</span></label>
              <input type="text" name="email" id="email" autocomplete="off" class="form-control"
                     placeholder="Masukan Email" maxlength="70">
              <span class="text-danger">
                <strong id="email-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="phone_number">Nomor Handphone <span class="text-danger">*</span></label>
              <input type="text" name="phone_number" id="phone_number" class="form-control"
                     placeholder="Masukan Nomor Handphone" autocomplete="off" maxlength="70">
              <span class="text-danger">
                <strong id="phone_number-error"></strong>
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
    table = $('#student_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('student.json') }}',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'student_identity_number'},
        {data: 'name'},
        {data: 'email'},
        {data: 'phone_number', sClass: 'text-center'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1],
          orderable: false
        },
      ],
    });

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_student').modal('show');
      $('.modal-title').text('Edit Data Siswa');
      url = '{{ route('student.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('student.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#student_identity_number').val(data.student_identity_number);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone_number').val(data.phone_number);
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
                url: "{{ route('student.delete') }}",
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

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: $('#form_student').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_student').modal('hide');
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
      $('#student_identity_number').val('');
      $('#name').val('');
      $('#email').val('');
      $('#phone_number').val('');
    }

    // check email whether it has been used or not
    $('#email').on('keyup', function () {
      clearTimeout(timer);
      const email = $('#email').val().trim();
      $('#email-error').html('').addClass('text-danger');
      if (email) {
        if (email !== '') {
          timer = setTimeout(function () {
            $.ajax({
              url: '{{ route('student.email') }}',
              type: 'get',
              data: {email: email},
              dataType: 'json',
              success: function (resp) {
                if (resp.status === 200) {
                  $('#email-error').html('<i class="icon icon-check"></i> ' + resp.message)
                    .removeClass('text-danger')
                    .addClass('text-success');
                  $('.btn-submit').attr('disabled', false);
                } else {
                  $('#email-error').html('<i class="icon icon-close"></i> ' + resp.message)
                    .removeClass('text-success')
                    .addClass('text-danger');
                  $('.btn-submit').attr('disabled', true);
                }
              }
            });
          }, timeout);
        } else {
          $('#email-error').html('');
        }
      }
    });

    // check phone number whether it has been used or not
    $('#phone_number').on('keyup', function () {
      clearTimeout(timer);
      const phoneNumber = $('#phone_number').val().trim();
      $('#phone_number-error').html('').addClass('text-danger');
      if (phoneNumber) {
        if (phoneNumber !== '') {
          timer = setTimeout(function () {
            $.ajax({
              url: '{{ route('student.phone') }}',
              type: 'get',
              data: {phone_number: phoneNumber},
              dataType: 'json',
              success: function (resp) {
                if (resp.status === 200) {
                  $('#phone_number-error').html('<i class="icon icon-check"></i> ' + resp.message)
                    .removeClass('text-danger')
                    .addClass('text-success');
                  $('.btn-submit').attr('disabled', false);
                } else {
                  $('#phone_number-error').html('<i class="icon icon-close"></i> ' + resp.message)
                    .removeClass('text-success')
                    .addClass('text-danger');
                  $('.btn-submit').attr('disabled', true);
                }
              }
            });
          }, timeout);
        } else {
          $('#phone_number-error').html('');
        }
      }
    })
  </script>
@endpush
