@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      @if (checkPermission()->create)
        <button class="btn btn-info btn-sm btn-tambah" type="button" style="margin-bottom: 10px">
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
                <table id="subject_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    @if (optional(configuration())->type_school == 1)
                      <th>Mata Kuliah</th>
                      <th>Semester</th>
                    @else
                      <th>Mata Pelajaran</th>
                      <th>Tingkat Kelas</th>
                    @endif
                    <th>KKM</th>
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

  <div id="modal_minimal_criteria" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_minimal_criteria" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="minimal_criteria">KKM <span class="text-danger">*</span></label>
              <input type="number" name="minimal_criteria" min="1" max="100" autocomplete="off" id="minimal_criteria" class="form-control" placeholder="Contoh : 80" maxlength="5">
              <span class="text-danger">
                <strong id="minimal_criteria-error"></strong>
              </span>
            </div>
            @if (optional(configuration())->type_school == 1)
              <div class="form-group level">
                <label for="semester_id">Semester <span class="text-danger">*</span></label>
                <select name="semester_id" id="semester_id" class="form-control">
                  <option value="">-- Pilih Semester --</option>
                  @if ($semesters->isNotEmpty())
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
            @else
              <div class="form-group level">
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
              <div class="form-group all_grade_level">
                <label class="custom-control custom-control-primary custom-checkbox" style="margin-top: -20px">
                  <input class="custom-control-input" type="checkbox" name="all_grade_level" id="all_grade_level" value="0">
                  <span class="custom-control-indicator"></span>
                  <span style="margin-left: 20px">Untuk semua {{ level() }} ?</span>
                </label>
              </div>
            @endif
            <div class="form-group subject_id" @if(optional(configuration())->type_school != 1) style="margin-top: -10px" @endif>
              <label for="subject_id">{{ subjectName() }} <span class="text-danger">*</span></label>
              <select name="subject_id" id="subject_id" class="form-control">
                <option value="">-- Pilih Mata Pelajaran --</option>
              </select>
              <span class="text-danger">
                <strong id="subject_id-error"></strong>
              </span>
            </div>
            <div class="form-group all_subject">
              <label class="custom-control custom-control-primary custom-checkbox" style="margin-top: -20px">
                <input class="custom-control-input" type="checkbox" name="all_subject" id="all_subject" value="0">
                <span class="custom-control-indicator"></span>
                <span style="margin-left: 20px">Untuk semua {{ subjectName() }} ?</span>
              </label>
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
    $(document).ready(function () {
      let table, url, type;

      // load data in datatable
      table = $('#subject_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [],

        ajax: {
          "url": '{{ route('minimal.criteria.json') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'subject.name'},
          {data: 'level'},
          {data: 'minimal_criteria'},
          {data: 'action', sClass: 'text-center'},
        ],

        columnDefs: [
          {
            targets: [0, -1],
            orderable: false
          },
        ],
      });

      $(function () {
        getSubject();
      })

      $('#semester_id, #grade_level_id, #subject_id').select2({width: '100%'});

      $('#all_subject').change(function () {
        if ($(this).is(':checked')) {
          $(this).val("1");
          $('.subject_id').slideUp(1000);
        } else {
          $(this).val("0");
          $('.subject_id').slideDown(1000);
        }
      });

      $('#all_grade_level').change(function () {
        if ($(this).is(':checked')) {
          $(this).val("1");
          $('.level').slideUp(1000);
        } else {
          $(this).val("0");
          $('.level').slideDown(1000);
        }
      });

      $('#semester_id').change(function () {
        const id = this.value;
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url: '{{ route('minimal.criteria.subject') }}',
          type: 'post',
          data: {semester_id: id},
          dataType: 'json',
          success: function (resp) {
            if (resp.status === 200) {
              const data = resp.data;
              let html = '';
              let subjectName = '{{ subjectName() }}';
              html += `<option value="">-- Pilih ${subjectName}</option>`;
              $.each(data, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              });

              $('#subject_id').html(html);
            } else {
              notification(resp.status, resp.message);
            }
          },
          error: function (xhr, status, error) {
            alert(status + ' : ' + error);
          }
        });
      });

      // show modal add data
      $('.btn-tambah').click(function () {
        $('#modal_minimal_criteria').modal('show');
        $('.modal-title').text('Tambah Data KKM');
        url = '{{ route('minimal.criteria.create') }}';
        type = 'post';
        resetForm();
      });

      // get data from database for edit data
      table.on('click', '.btn-edit', function () {
        const id = $(this).attr('id');
        $('#modal_minimal_criteria').modal('show');
        $('.modal-title').text('Edit Data KKM');
        $('.level').hide();
        $('.subject_id').hide();
        $('.all_subject').hide();
        $('.all_grade_level').hide();
        url = '{{ route('minimal.criteria.update') }}';
        type = 'put';
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url: '{{ route('minimal.criteria.edit') }}',
          type: 'get',
          data: {id: id},
          dataType: 'json',
          success: function (resp) {
            if (resp.status === 200) {
              const data = resp.data;
              $('#id').val(data.id);
              $('#minimal_criteria').val(data.minimal_criteria);
            } else {
              notification(resp.status, resp.message);
            }
          },
          error: function (xhr, status, error) {
            alert(status + ' : ' + error);
          }
        });
      })

      // delete data
      table.on('click', '.btn-delete', function () {
        const id = $(this).attr('id');
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
                  url: "{{ route('minimal.criteria.delete') }}",
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
      })

      // submit data
      $('.btn-submit').click(function (e) {
        e.preventDefault();
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url: url,
          type: type,
          data: $('#form_minimal_criteria').serialize(),
          dataType: 'json',
          beforeSend: function () {
            loadingBeforeSend();
          },
          success: function (data) {
            notification(data.status, data.message);
            loadingAfterSend();
            if (data.status === 200) {
              resetForm();
              $('#modal_minimal_criteria').modal('hide');
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

      // reset form
      function resetForm() {
        $('#id').val('');
        $('#semester_id, #subject_id, #grade_level_id').select2('destroy').val("").select2({width: '100%'});
        $('#minimal_criteria').val('');
        $('.subject_id').show();
        $('.level').show();
        $('.all_subject').show();
        $('.all_grade_level').show();
        $('#all_subject, #all_grade_level').prop('checked', false);
      }

      // method for handle before send data
      function loadingBeforeSend() {
        $(".btn-submit").attr('disabled', 'disabled');
        $(".btn-submit").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
      }

      // method for handle after send data
      function loadingAfterSend() {
        $(".btn-submit").removeAttr('disabled');
        $(".btn-submit").html('Submit');
      }

      // get subject for type school not university
      @if(optional(configuration())->type_school != 1)
        function getSubject() {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url: '{{ route('minimal.criteria.subject') }}',
          type: 'post',
          dataType: 'json',
          success: function (resp) {
            if (resp.status === 200) {
              const data = resp.data;
              let html = '';
              let subjectName = '{{ subjectName() }}';
              html += `<option value="">-- Pilih ${subjectName}</option>`;
              $.each(data, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              });

              $('#subject_id').html(html);
            } else {
              notification(resp.status, resp.message);
            }
          },
          error: function (xhr, status, error) {
            alert(status + ' : ' + error);
          }
        });
      }
      @endif
    });
  </script>
@endpush
