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
              <h5>Filter :</h5>
              <div class="row">
                <div class="col-sm-12 col-md-3">
                  <div class="form-group">
                    <select class="form-control" name="level_filter" id="level_filter">
                      <option value="all" checked>Semua {{ level() }}</option>
                      @if (optional(configuration())->type_school == 1)
                        @forelse ($semesters as $semester)
                          <option value="{{ $semester->id }}">Semester {{ $semester->number }}</option>
                        @empty
                          <option value="" disabled>Data belum tersedia</option>
                        @endforelse
                      @else
                        @forelse ($gradeLevels as $gradeLevel)
                          <option value="{{ $gradeLevel->id }}">{{ $gradeLevel->name }}</option>
                        @empty
                          <option value="" disabled>Data belum tersedia</option>
                        @endforelse
                      @endif
                    </select>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <select class="form-control" name="subject_filter" id="subject_filter">
                      <option value="all" checked>Semua {{ subjectName() }}</option>
                      @forelse ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                      @empty
                        <option value="" disabled>Data belum tersedia</option>
                      @endforelse
                    </select>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="material_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>{{ subjectName() }}</th>
                    <th>{{ level() }}</th>
                    <th>Sumber Materi</th>
                    <th>Judul Materi</th>
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

  <div id="modal_material" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_material" method="post" enctype="multipart/form-data">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="title">Judul Materi <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" autocomplete="off" id="title" placeholder="Judul materi maksimal 80 karakter">
              <p style="float: right"><span id="count_text">80</span>/80</p>
              <span class="text-danger">
                <strong id="title-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="school_year_id">Tahun Ajar <span class="text-danger">*</span></label>
              <select name="school_year_id" id="school_year_id" class="form-control">
                <option value="">-- Pilih Tahun Ajar --</option>
                @foreach($schoolYears as $schoolYear)
                  @if ($schoolYear->semester % 2 == 1)
                    <option value="{{ $schoolYear->id }}">{{ $schoolYear->early_year }}/{{ $schoolYear->end_year }} Ganjil</option>
                  @else
                    <option value="{{ $schoolYear->id }}">{{ $schoolYear->early_year }}/{{ $schoolYear->end_year }} Genap</option>
                  @endif
                @endforeach
              </select>
              <span class="text-danger">
                  <strong id="school_year_id-error"></strong>
                </span>
            </div>
            @if (configuration()->type_school == 1)
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
                <label for="subject_id">Mata Kuliah <span class="text-danger">*</span></label>
                <select name="subject_id" id="subject_id" class="form-control">
                  <option value="">-- Pilih Mata Kuliah --</option>
                </select>
                <span class="text-danger">
                  <strong id="subject_id-error"></strong>
                </span>
              </div>
            @else
              <div class="form-group">
                <label for="grade_level_id">Tingkat Kelas <span class="text-danger">*</span></label>
                <select name="grade_level_id" id="grade_level_id" class="form-control">
                  <option value="">-- Pilih Tingkat Kelas --</option>
                  @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->id }}">{{ $gradeLevel->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="grade_level_id-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="name">Mata Pelajaran <span class="text-danger">*</span></label>
                <select name="subject_id" id="subject_id" class="form-control">
                  <option value="">-- Pilih Mata Pelajaran --</option>
                  @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="subject_id-error"></strong>
                </span>
              </div>
            @endif
              <div class="form-group">
                <label for="position">Urutan Materi <span class="text-danger">*</span></label>
                <input type="number" min="1" name="position" id="position" class="form-control" placeholder="Urutan materi">
                <span class="text-danger">
                  <strong id="position-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="video_link">Link Video (Copy link Pada Menu Share Youtube)</label>
                <input type="text" name="video_link" id="video_link" class="form-control" placeholder="Contoh : https://youtu.be/P_IAKMQ-h2k">
                <span class="text-danger">
                  <strong id="video_link-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="form-control-5">Materi (pdf, ppt, pptx, doc, docx)</label>
                <div class="input-group input-file">
                  <input class="form-control" disabled type="text" id="module-file" placeholder="No file chosen" style="background-color: rgba(0,0,0, 0.1)">
                  <span class="input-group-btn">
                    <label class="btn btn-primary file-upload-btn">
                      <input id="module" class="file-upload-input" type="file" name="module">
                      <span class="icon icon-paperclip icon-lg"></span>
                    </label>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label for="form-control-5">Source Code (zip, rar)</label>
                <div class="input-group input-file">
                  <input class="form-control" disabled type="text" id="archive-file" placeholder="No file chosen" style="background-color: rgba(0,0,0, 0.1)">
                  <span class="input-group-btn">
                    <label class="btn btn-primary file-upload-btn">
                      <input id="archive" class="file-upload-input" type="file" name="archive">
                      <span class="icon icon-paperclip icon-lg"></span>
                    </label>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <label for="detail_material">Penjelasan Materi <span class="text-danger">*</span></label>
                <textarea name="detail_material" id="detail_material" class="form-control" rows="4" placeholder="Penjelasan Materi"></textarea>
                <span class="text-danger">
                  <strong id="detail_material-error"></strong>
                </span>
              </div>
            <p>Note: *) harus diisi.</p>
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
  <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
  <script>
    let table, url, type, maxText = 80;

    // load data in datatable
    table = $('#material_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('material.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        "data": function (d) {
          d.level = $('#level_filter').val(),
          d.subject = $('#subject_filter').val()
        }
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'subject.name'},
        {data: 'level'},
        {data: 'source'},
        {data: 'title'},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2, -3],
          orderable: false
        },
      ],
    });

    $("#level_filter, #subject_filter").on('change', function () {
      table.ajax.reload(null, false);
    });

    $('#semester_id, #subject_id, #grade_level_id, #level_filter, #subject_filter, #school_year_id').select2({width: '100%'});

    $('#title').keyup(function() {
      const len = this.value.length;
      if (len >= maxText) {
        this.value = this.value.substring(0, maxText);
      }

      const countText = maxText - len;
      if (countText < 0) {
        $('#count_text').text(0);
      } else {
        $('#count_text').text(countText);
      }
    });

    $('#semester_id').change(function (e) {
      e.preventDefault();
      const semester = this.value;
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('material.get.subject') }}',
        type: 'get',
        data: {semester_id: semester},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            let html = '';
            html += `<option value="">-- Pilih Mata Kuliah --</option>`;
            $.each(resp.data, function (i, v) {
              html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
            })
            $('#subject_id').html(html);
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    })

    // show modal add data
    const addData = function () {
      $('#modal_material').modal('show');
      $('.modal-title').text('Tambah Data Materi');
      url = '{{ route('material.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_material').modal('show');
      $('.modal-title').text('Edit Data Materi');
      url = '{{ route('material.update') }}';
      type = 'post';
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('material.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            const titleLength = maxText - data.title.length;
            if (data.semester_id != null) {
              let html = '';
              html += `<option value="">-- Pilih Mata Kuliah --</option>`;
              $.each(resp.subject, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              })
              $('#subject_id').html(html);
            }
            $('#count_text').text(titleLength);
            $('#id').val(data.id);
            $('#grade_level_id').select2('destroy').val(data.grade_level_id).select2({width: '100%'});
            $('#semester_id').select2('destroy').val(data.semester_id).select2({width: '100%'});
            $('#school_year_id').select2('destroy').val(data.school_year_id).select2({width: '100%'});
            $('#subject_id').select2('destroy').val(data.subject_id).select2({width: '100%'});
            $('#detail_material').val(data.content);
            $('#position').val(data.position);
            $('#title').val(data.title);
            $('#video_link').val(data.video_link);
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
                url: "{{ route('material.delete') }}",
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
    $('#archive').change(function () {
      const val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(zip|rar)$");

      if (!(regex.test(val))) {
        $(this).val('');
        alert('Format yang diterima zip dan rar');
      }
    });

    // check whether the extension file is suitable or not
    $('#module').change(function () {
       const val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(pdf|doc|docx|ppt|pptx)$");

      if (!(regex.test(val))) {
        $(this).val('');
        alert('Format yang diterima pdf, doc, docx, ppt dan pptx');
      }
    });

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      const form = $('#form_material')[0];
      const formData = new FormData(form);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          if (data.status === 200) {
            resetForm();
            $('#modal_material').modal('hide');
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

    $(function () {
      // We can attach the `fileselect` event to all file inputs on the page
      $(document).on('change', ':file', function () {
        const input = $(this),
          numFiles = input.get(0).files ? input.get(0).files.length : 1,
          label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
      });

      // We can watch for our custom `fileselect` event like this
      $(document).ready(function () {
        $(':file').on('fileselect', function (event, numFiles, label) {
          const input = $(this).parents('.input-file').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

          if (input.length) {
            input.val(log);
          } else {
            if (log) alert(log);
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
      $('#video_link').val('');
      $('#title').val('');
      $('#module-file').val('');
      $('#archive-file').val('');
      $('#detail_material').val('');
      $('#position').val('');
      $('#semester_id, #subject_id, #grade_level_id, #school_year_id').select2('destroy').val("").select2({width: '100%'});
    }
  </script>
@endpush
