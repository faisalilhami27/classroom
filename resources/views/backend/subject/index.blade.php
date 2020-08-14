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
              <button class="btn btn-primary btn-sm" style="float: right;" data-toggle="modal"
                      data-target="#modal_import"><i class="icon icon-cloud-upload"></i> Import Data
              </button>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="subject_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    @if (optional(configuration())->type_school == 1)
                      <th>Semester</th>
                    @endif
                    @if (optional(configuration())->type_school == 1 || optional(configuration())->type_school == 2)
                      <th>Jurusan</th>
                    @endif
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

  <div id="modal_subject" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_subject" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="code">Kode <span class="text-danger">*</span></label>
              <input type="text" name="code" autocomplete="off" id="code" class="form-control"
                     placeholder="Contoh : PAI" maxlength="10">
              <span class="text-danger">
                <strong id="code-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="name">Nama <span class="text-danger">*</span></label>
              <input type="text" name="name" autocomplete="off" id="name" class="form-control"
                     placeholder="Contoh : Pendidikan Agama Islam" maxlength="100">
              <span class="text-danger">
                <strong id="name-error"></strong>
              </span>
            </div>
            @if (optional(configuration())->type_school == 1)
              <div class="form-group">
                <label for="semester_id">Semester <span class="text-danger">*</span></label>
                <select name="semester_id" id="semester_id" class="form-control">
                  <option value="">-- Pilih Semester --</option>
                  @if (!empty($semesters))
                    @foreach($semesters as $semester)
                      <option value="{{ $semester->id }}">Semester {{ $semester->number }}</option>
                    @endforeach
                  @else
                    <option value="" disabled>Data tidak tersedia</option>
                  @endif
                </select>
                <span class="text-danger">
                  <strong id="semester_id-error"></strong>
                </span>
              </div>
            @endif
            <div class="form-group">
              <label for="major_id">Jurusan <span class="text-danger">*</span></label>
              <select name="major_id" id="major_id" class="form-control">
                <option value="">-- Pilih Jurusan --</option>
                <option value="0">Semua Jurusan</option>
                @if ($majors->isNotEmpty())
                  @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->code }} - {{ $major->name }}</option>
                  @endforeach
                @else
                  <option value="" disabled>Data belum tersedia</option>
                @endif
              </select>
              <span class="text-danger">
                <strong id="major_id-error"></strong>
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
  <div id="modal_import" role="dialog" class="modal fade">
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
              @if ((optional(configuration())->type_school) == 1)
                <a href="{{ asset('excel/form_pengisian_data_matkul.xlsx') }}" class="btn btn-info btn-sm"><i
                    class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
              @elseif ((optional(configuration())->type_school) == 2)
                <a href="{{ asset('excel/form_pengisian_data_mapel.xlsx') }}" class="btn btn-info btn-sm"><i
                    class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
              @else
                <a href="{{ asset('excel/form_pengisian_data_mapel_smp_sd.xlsx') }}" class="btn btn-info btn-sm"><i
                    class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
              @endif
            </div>
            <div class="clearfix"></div>
            @if (optional(configuration())->type_school == 1 || optional(configuration())->type_school == 2)
              <div class="form-group">
                <label for="major_id_import">Jurusan <span class="text-danger">*</span></label>
                <select name="major_id_import" id="major_id_import" class="form-control">
                  <option value="">-- Pilih Jurusan --</option>
                  @if ($majors->isNotEmpty())
                    @foreach($majors as $major)
                      <option value="{{ $major->id }}">{{ $major->code }} - {{ $major->name }}</option>
                    @endforeach
                  @else
                    <option value="" disabled>Data belum tersedia</option>
                  @endif
                </select>
                <span class="text-danger">
                <strong id="major_id_import-error"></strong>
              </span>
              </div>
            @endif
            <div class="form-group">
              <label for="file_import">File <span class="text-danger">*</span></label>
              <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                <div class="form-control" data-trigger="fileinput">
                  <i class="glyphicon glyphicon-file fileinput-exists"></i>
                  <span class="fileinput-filename"></span>
                </div>
                <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">Select file</span>
                <span class="fileinput-exists">Change</span>
                <input type="file" name="file_import"/>
              </span>
              </div>
              <span class="text-danger">
                <strong id="file_import-error"></strong>
              </span>
            </div>
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
    table = $('#subject_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('subject.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'code'},
        {data: 'name'},
        @if(optional(configuration())->type_school == 1)
          {
            data: 'semester.number'
          },
        @endif
        @if(optional(configuration())->type_school == 1 || optional(configuration())->type_school == 2)
          {
            data: 'major'
          },
        @endif
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1],
          orderable: false
        },
      ],
    });

    $('#semester_id, #major_id, #semester_id_import, #major_id_import').select2({width: '100%'});

    // show modal add data
    const addData = function () {
      $('#modal_subject').modal('show');
      $('.modal-title').text('Tambah Data Mata Pelajaran');
      url = '{{ route('subject.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_subject').modal('show');
      $('.modal-title').text('Edit Data Mata Pelajaran');
      url = '{{ route('subject.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('subject.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#code').val(data.code);
            $('#name').val(data.name);
            $('#semester_id').select2({width: '100%'}).val(data.semester_id).trigger('change');
            if (data.major_id == null) {
              $('#major_id').select2({width: '100%'}).val(0).trigger('change');
            } else {
              $('#major_id').select2({width: '100%'}).val(data.major_id).trigger('change');
            }
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
                url: "{{ route('subject.delete') }}",
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
        data: $('#form_subject').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_subject').modal('hide');
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
        url: '{{ route('subject.import') }}',
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
            $('#modal_import').modal('hide');
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
      $('#semester_id').select2({width: '100%'}).val('').trigger('change');
      $('#major_id').select2({width: '100%'}).val('').trigger('change');
      $('#code').val('');
      $('#name').val('');
    }
  </script>
@endpush
