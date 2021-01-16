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
                <table id="class_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Kode</th>
                    <th>Mata Pelajaran</th>
                    <th>Tingkat Kelas</th>
                    <th>Jurusan</th>
                    <th>Urutan Kelas</th>
                    <th>Tahun Ajar</th>
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

  <div id="modal_class" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_class" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="subject_id">Mata Pelajaran <span class="text-danger">*</span></label>
              <select name="subject_id" id="subject_id" class="form-control">
                <option value="">-- Pilih Mata Pelajaran --</option>
                @if ($subjects->isNotEmpty())
                  @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                  @endforeach
                @else
                  <option value="" disabled>Data belum tersedia</option>
                @endif
              </select>
              <span class="text-danger">
                <strong id="subject_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="grade_level_id">Tingkat Kelas <span class="text-danger">*</span></label>
              <select name="grade_level_id" id="grade_level_id" class="form-control">
                <option value="">-- Pilih Tingkat Kelas --</option>
                @if ($gradeLevels->isNotEmpty())
                  @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->id }}">{{ $gradeLevel->name }}</option>
                  @endforeach
                @else
                  <option value="" disabled>Data belum tersedia</option>
                @endif
              </select>
              <span class="text-danger">
                <strong id="grade_level_id-error"></strong>
              </span>
            </div>
            @if (optional(configuration())->type_school == 2)
              <div class="form-group">
                <label for="major_id">Jurusan <span class="text-danger">*</span></label>
                <select name="major_id" id="major_id" class="form-control">
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
                  <strong id="major_id-error"></strong>
                </span>
              </div>
            @endif
            <div class="form-group">
              <label for="class_order">Urutan Kelas <span class="text-danger">*</span></label>
              <input type="text" name="class_order" autocomplete="off" id="class_order" class="form-control"
                     placeholder="Contoh : A atau 1" maxlength="5">
              <span class="text-danger">
                <strong id="class_order-error"></strong>
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

  <div id="modal_student" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Daftar Siswa</h4>
        </div>
        <div class="modal-body">
          <button class="btn btn-danger btn-sm btn-delete-student pull-right"><i class="icon icon-trash-o"></i> Hapus
            Siswa
          </button>
          <div class="clearfix"></div>
          <div class="table-responsive" style="margin-top: 20px">
            <table id="student_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">
                  <label class="custom-control custom-control-danger custom-checkbox">
                    <input class="custom-control-input checkbox_all_student" type="checkbox" id="checkbox_all_student"
                           name="checkbox_all_student">
                    <span class="custom-control-indicator"></span>
                  </label>
                </th>
                <th width="20px">No</th>
                <th>NIPD</th>
                <th>Nama Siswa</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
    let table, url, type, tableStudent;

    // load data in datatable
    table = $('#class_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('class.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'code', orderable: false},
        {data: 'subject.name'},
        {data: 'major.name'},
        {data: 'level'},
        {data: 'class_order'},
        {data: 'school_year'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2],
          orderable: false
        },
      ],
    });

    $('#grade_level_id, #major_id, #subject_id').select2({
      width: '100%',
    });

    // show modal add data
    const addData = function () {
      $('#modal_class').modal('show');
      $('.modal-title').text('Tambah Data Kelas');
      url = '{{ route('class.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    table.on('click', '.btn-edit', function () {
      const id = $(this).attr('id');
      $('#modal_class').modal('show');
      $('.modal-title').text('Edit Data Kelas');
      url = '{{ route('class.update') }}';
      type = 'put';
      $.ajax({
        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        url: '{{ route('class.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#class_order').val(data.class_order);
            $('#grade_level_id').select2({width: '100%'}).val(data.grade_level_id).trigger('change');
            $('#major_id').select2({width: '100%'}).val(data.major_id).trigger('change');
            $('#subject_id').select2({width: '100%'}).val(data.subject_id).trigger('change');
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
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
        data: $('#form_class').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          if (data.status === 200) {
            $('#modal_class').modal('hide');
            resetForm();
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

    // get data from database for show student data
    table.on('click', '.btn-view', function (e) {
      e.preventDefault();
      $('#modal_student').modal('show');
      const id = $(this).attr('id');
      tableStudent = $('#student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('class.json.student') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.class_id = id
          }
        },

        columns: [
          {data: 'checkbox', searchable: false, orderable: false},
          {data: 'DT_RowIndex', searchable: false, orderable: false},
          {data: 'student.student_identity_number'},
          {data: 'student.name'},
        ],

        columnDefs: [
          {
            targets: [0],
            orderable: false
          },
        ],
      });
    });

    // delete data
    table.on('click', '.btn-delete', function () {
      $.confirm({
        content: 'Data yang dihapus belum akan dapat dikembalikan.',
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
                url: "{{ route('class.delete') }}",
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
    });


    // delete student
    $('.btn-delete-student').click(function () {
      const student = [];
      $(".checkbox_student:checked").each(function () {
        student.push(this.value);
      });

      $.confirm({
        content: `Akan menghapus ${student.length} siswa yang ada`,
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
            text: '<i class="icon icon-trash-o"></i> Hapus',
            btnClass: 'btn-warning',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('class.delete.student') }}",
                type: "delete",
                data: {
                  student_list: student
                },
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  if (data.status === 200) {
                    tableTemporary.ajax.reload();
                    $('#delete_all_question').prop('checked', false);
                    $("#school_year_id").select2('destroy').val('').select2({width: '100%'});
                  }
                },
                error: function (xhr, status, error) {
                  alert(status + " : " + error);
                }
              });
            }
          }
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
      $('#grade_level_id').select2({width: '100%'}).val('').trigger('change');
      $('#major_id').select2({width: '100%'}).val('').trigger('change');
      $('#subject_id').select2({width: '100%'}).val('').trigger('change');
      $('#class_order').val('');
    }
  </script>
@endpush
