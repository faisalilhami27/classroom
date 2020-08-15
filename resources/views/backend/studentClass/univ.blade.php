@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      @if (checkPermission()->create)
        <button class="btn btn-info btn-sm btn-add" data-toggle="modal" data-target="#modal_class" type="button"
                style="margin-bottom: 10px">
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
                    <th>Jurusan</th>
                    <th>Mata Kuliah</th>
                    <th>Semester</th>
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
              <label for="semester_id">Semester <span class="text-danger">*</span></label>
              <select name="semester_id" id="semester_id" class="form-control">
                <option value="">-- Pilih Semester --</option>
                @if ($semesters->isNotEmpty())
                  @foreach($semesters as $semester)
                    @if (activeSchoolYear()->semester == 1)
                      @if ($semester->number % 2 != 0)
                        <option value="{{ $semester->id }}">Semester {{ $semester->number }}</option>
                      @endif
                    @else
                      @if ($semester->number % 2 == 0)
                        <option value="{{ $semester->id }}">Semester {{ $semester->number }}</option>
                      @endif
                    @endif
                  @endforeach
                @else
                  <option value="" disabled>Data belum tersedia</option>
                @endif
              </select>
              <span class="text-danger">
                <strong id="semester_id-error"></strong>
              </span>
            </div>
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
            <div class="form-group">
              <label for="subject_id">Mata Kuliah <span class="text-danger">*</span></label>
              <select name="subject_id" id="subject_id" class="form-control">
                <option value="">-- Pilih Mata Kuliah --</option>
              </select>
              <span class="text-danger">
                <strong id="subject_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="class_order">Urutan Kelas <span class="text-danger">*</span></label>
              <input type="text" name="class_order" autocomplete="off" id="class_order" class="form-control"
                     placeholder="Contoh : A" maxlength="5">
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
          <h4 class="modal-title">Daftar Mahasiswa</h4>
        </div>
        <div class="modal-body">
          <button class="btn btn-danger btn-sm btn-delete-student pull-right"><i class="icon icon-trash-o"></i> Hapus Mahasiswa</button>
          <div class="clearfix"></div>
          <div class="table-responsive" style="margin-top: 20px">
            <table id="student_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">
                  <label class="custom-control custom-control-danger custom-checkbox">
                    <input class="custom-control-input checkbox_all_student" type="checkbox" id="checkbox_all_student" name="checkbox_all_student">
                    <span class="custom-control-indicator"></span>
                  </label>
                </th>
                <th width="20px">No</th>
                <th>NPM</th>
                <th>Nama Mahasiswa</th>
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
    $(document).ready(function () {
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
          {data: 'major.name'},
          {data: 'subject.name'},
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

      $('#semester_id, #subject_id, #major_id').select2({
        width: '100%',
      });

      // show modal add
      $('.btn-add').click(function () {
        $('.modal-title').text('Tambah Data Kelas');
        url = '{{ route('class.create') }}';
        type = 'post';
        resetForm();
      });

      // get data from database for edit data
      table.on('click', '.btn-edit', function (e) {
        e.preventDefault();
        $('#modal_class').modal('show');
        $('.modal-title').text('Edit Data Kelas');
        url = '{{ route('class.update') }}';
        type = 'put';
        const id = $(this).attr('id');
        $.ajax({
          headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          url: '{{ route('class.edit') }}',
          type: 'get',
          data: {id: id},
          dataType: 'json',
          success: function (resp) {
            if (resp.status === 200) {
              const data = resp.data;
              let html = '';

              html += `<option value="">-- Pilih Mata Kuliah --</option>`;
              $.each(resp.subjects, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              })
              $('#subject_id').html(html);

              $('#id').val(data.id);
              $('#class_order').val(data.class_order);
              $('#semester_id').select2('destroy').val(data.semester_id).select2({width: '100%'});
              $('#subject_id').select2('destroy').val(data.subject_id).select2({width: '100%'});
              $('#major_id').select2('destroy').val(data.major_id).select2({width: '100%'});
            } else {
              notification(resp.status, resp.message);
            }
          },
          error: function (xhr, status, error) {
            alert(status + ' : ' + error);
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

      $('#major_id, #semester_id').change(function () {
        getSubjects();
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
      $('#semester_id').select2('destroy').val('').select2({width: '100%'});
      $('#subject_id').html('<option value="">-- Pilih {{ subjectName() }} --</option>');
      $('#subject_id').select2('destroy').val('').select2({width: '100%'});
      $('#major_id').select2('destroy').val('').select2({width: '100%'});
      $('#class_order').val('');
    }

    const getSubjects = function () {
      const semester = $('#semester_id').val();
      const major = $('#major_id').val();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "{{ route('class.subject') }}",
        type: "post",
        data: {
          semester_id: semester,
          major_id: major
        },
        dataType: "json",
        success: function (data) {
          if (data.status === 200) {
            let html = '';
            html += `<option value="">-- Pilih Mata Kuliah --</option>`;
            $.each(data.data, function (i, v) {
              html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
            })
            $('#subject_id').html(html);
          } else {
            notification(data.status, data.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + " : " + error);
        }
      });
    }
  </script>
@endpush
