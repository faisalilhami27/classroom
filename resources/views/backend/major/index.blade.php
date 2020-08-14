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
              <button class="btn btn-primary btn-sm" style="float: right;" data-toggle="modal" data-target="#modal_major_import"><i class="icon icon-cloud-upload"></i> Import Data</button>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="role_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
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

  <div id="modal_major" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_major" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="code">Kode <span class="text-danger">*</span></label>
              <input type="text" name="code" autocomplete="off" id="code" class="form-control" placeholder="Contoh : IPA" maxlength="10">
              <span class="text-danger">
                <strong id="code-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="name">Nama Jurusan <span class="text-danger">*</span></label>
              <input type="text" name="name" autocomplete="off" id="name" class="form-control" placeholder="Contoh : Ilmu Pengetahuan Alam" maxlength="100">
              <span class="text-danger">
                <strong id="name-error"></strong>
              </span>
            </div>
            <p>Note: </p>
            <ol style="margin-left: -25px">
              <li>*) harus diisi.</li>
              <li>Kode maksimal 10 huruf</li>
            </ol>
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
  <div id="modal_major_import" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title-import">Import Data</h4>
        </div>
        <form action="" id="form_major_import" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group pull-right">
              <a href="{{ asset('excel/form_pengisian_data_jurusan.xlsx') }}" class="btn btn-info btn-sm"><i class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
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
                <input type="file" name="major_import" placeholder="ocsa"/>
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
    let table, url, type;

    // load data in datatable
    table = $('#role_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('major.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'code'},
        {data: 'name'},
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
      $('#modal_major').modal('show');
      $('.modal-title').text('Tambah Data Jurusan');
      url = '{{ route('major.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_major').modal('show');
      $('.modal-title').text('Edit Data Jurusan');
      url = '{{ route('major.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('major.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#code').val(data.code);
            $('#name').val(data.name);
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
                url: "{{ route('major.delete') }}",
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

    // check whether the extension file is suitable or not
    $('input[type=file]').change(function () {
      var val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(xlsx|xls)$");

      if (!(regex.test(val))) {
        $(this).val('');
        alert('Format yang diizinkan xlsx atau xls');
      }
    });

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: $('#form_major').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_major').modal('hide');
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

    // import data
    $('.btn-import').click(function () {
      const form = $('#form_major_import')[0];
      const formData = new FormData(form);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('major.import') }}',
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

    // reset form
    const resetForm = function () {
      $('#id').val('');
      $('#code').val('');
      $('#name').val('');
    }
  </script>
@endpush
