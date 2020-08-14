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
                <table id="school_year_table" class="table table-striped table-hover table-nowrap dataTable"
                       width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Tahun Awal</th>
                    <th>Tahun Akhir</th>
                    <th>Semester</th>
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

  <div id="modal_school_year" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_school_year" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="early_year">Tahun Awal <span class="text-danger">*</span></label>
              <input type="text" min="1" name="early_year" autocomplete="off" id="early_year" class="form-control"
                     placeholder="{{ date('Y') }}" maxlength="4">
              <span class="text-danger">
                <strong id="early_year-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="end_year">Tahun Akhir <span class="text-danger">*</span></label>
              <input type="text" readonly name="end_year" autocomplete="off" id="end_year" class="form-control"
                     placeholder="{{ (date('Y') + 1) }}" maxlength="4" style="background-color: rgba(0,0,0, 0.1)">
              <span class="text-danger">
                <strong id="end_year-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="semester">Semester <span class="text-danger">*</span></label>
              <select name="semester" id="semester" class="form-control">
                <option value="">-- Pilih Semester --</option>
                <option value="1">Ganjil</option>
                <option value="2">Genap</option>
              </select>
              <span class="text-danger">
                <strong id="semester-error"></strong>
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

    $('#early_year').datepicker({
      autoclose: true,
      format: "yyyy",
      viewMode: "years",
      minViewMode: "years"
    });

    $('#early_year').change(function () {
      $('#end_year').val(parseInt(this.value) + 1);
    })

    // load data in datatable
    table = $('#school_year_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('school.year.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'early_year'},
        {data: 'end_year'},
        {data: 'semester'},
        {data: 'status_active'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2, -3],
          orderable: false
        },
      ],
    });

    // show modal add data
    const addData = function () {
      $('#modal_school_year').modal('show');
      $('.modal-title').text('Tambah Data Tahun Ajar');
      url = '{{ route('school.year.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_school_year').modal('show');
      $('.modal-title').text('Edit Data Tahun Ajar');
      url = '{{ route('school.year.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('school.year.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#early_year').val(data.early_year);
            $('#end_year').val(data.end_year);
            $('#semester').val(data.semester);
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
                url: "{{ route('school.year.delete') }}",
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

    // switch status school year
    const changeStatus = function (id) {
      $.confirm({
        content: 'Tahun ajar yang sudah diubah tidak bisa dikembalikan ke sebelumnya.',
        title: 'Apakah yakin ingin mengubah tahun ajar ?',
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
            text: '<i class="icon icon-power-off"></i> Ubah',
            btnClass: 'btn-success',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('school.year.change') }}",
                type: "put",
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
        data: $('#form_school_year').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
         if (data.status === 200) {
           resetForm();
           $('#modal_school_year').modal('hide');
           setTimeout(function () {
             location.reload();
           }, 1000);
         }
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
      $('#early_year').val('');
      $('#end_year').val('');
      $('#semester').val('');
    }
  </script>
@endpush
