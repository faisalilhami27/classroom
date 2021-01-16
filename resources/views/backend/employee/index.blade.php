@extends('backend.layouts.app')
@section('title', $title)
@push('styles')
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
@endpush
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
              <button class="btn btn-primary btn-sm" style="float: right;" data-toggle="modal" data-target="#modal_employee_import"><i class="icon icon-cloud-upload"></i> Import Data</button>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="employee_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
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

  {{-- modal employee --}}
  <div id="modal_employee" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_employee" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="first_name">First Name <span class="text-danger">*</span></label>
              <input type="text" name="first_name" id="first_name" autocomplete="off" class="form-control" placeholder="Masukan first name"
                     maxlength="70">
              <span class="text-danger">
                <strong id="first_name-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="last_name">Last Name</label>
              <input type="text" name="last_name" id="last_name" autocomplete="off" class="form-control" placeholder="Masukan last name"
                     maxlength="70">
              <span class="text-danger">
                <strong id="last_name-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="email">Email <span class="text-danger">*</span></label>
              <input type="text" name="email" id="email" autocomplete="off" class="form-control"
                     placeholder="Masukan Email" maxlength="100">
              <span class="text-danger">
                <strong id="email-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="phone_number">Nomor Handphone <span class="text-danger">*</span></label>
              <input type="text" name="phone_number" id="phone_number" class="form-control"
                     placeholder="Masukan Nomor Handphone" autocomplete="off" maxlength="100">
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

  {{-- modal import --}}
  <div id="modal_employee_import" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title-import">Import Data</h4>
        </div>
        <form action="" id="form_import" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group pull-right">
              <a href="{{ asset('excel/form_pengisian_data_karyawan.xlsx') }}" class="btn btn-info btn-sm"><i class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
            </div>
            <div class="clearfix"></div>
            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
              <div class="form-control" data-trigger="fileinput">
                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                <span class="fileinput-filename"></span>
              </div>
              <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">Select file</span>
                <span class="fileinput-exists">Change</span>
                <input type="file" name="employee_import"/>
              </span>
            </div>
            <span class="text-danger">
              <strong id="major_import-error"></strong>
            </span>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            <button class="btn btn-primary btn-import" type="submit">Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
  <script>
    let table, url, type, timer, timeout = 1000;

    // load data in datatable
    table = $('#employee_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('employee.json') }}',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
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

    // show modal add data
    const addData = function () {
      $('#modal_employee').modal('show');
      $('.modal-title').text('Tambah Data Karyawan');
      url = '{{ route('employee.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_employee').modal('show');
      $('.modal-title').text('Edit Data Karyawan');
      url = '{{ route('employee.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('employee.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#first_name').val(data.first_name);
            $('#last_name').val(data.last_name);
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
                url: "{{ route('employee.delete') }}",
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
        data: $('#form_employee').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_employee').modal('hide');
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
      $('#employee_identity_number').val('');
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
              url: '{{ route('employee.email') }}',
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
              url: '{{ route('employee.phone') }}',
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

    // check whether the extension file is suitable or not
    $('input[type=file]').change(function () {
      var val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(xlsx|xls)$");

      if (!(regex.test(val))) {
        $(this).val('');
        alert('Format yang diizinkan xlsx atau xls');
      }
    });

    // import data
    $('.btn-import').click(function () {
      const form = $('#form_import')[0];
      const formData = new FormData(form);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('employee.import') }}',
        type: 'post',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeImport();
        },
        success: function (data) {
          notification(data.status, data.message);
          if (data.status === 200) {
            $('#modal_major_import').modal('hide');
            loadingAfterImport();
            setTimeout(function () {
              location.reload();
            }, 1000);
          }
        },
        error: function (resp) {
          loadingAfterImport();
          if (_.has(resp.responseJSON, 'errors')) {
            _.map(resp.responseJSON.errors, function (val, key) {
              $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
            })
          }
          alert(resp.responseJSON.message)
        }
      });
    });

    // method for handle before import data
    const loadingBeforeImport = function () {
      $(".btn-import").attr('disabled', 'disabled');
      $(".btn-import").html('<i class="fa fa-spinner fa-spin"></i> Import data....');
    }

    // method for handle after import data
    const loadingAfterImport = function () {
      $(".btn-import").removeAttr('disabled');
      $(".btn-import").html('Submit');
    }
  </script>
@endpush
