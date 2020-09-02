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
                <button class="btn btn-success btn-sm btn-export" style="float: right; margin-right: 15px"><i class="icon icon-cloud-download"></i> Export Data
                </button>
                <button class="btn btn-primary btn-sm" style="float: right; margin-right: 15px" data-toggle="modal"
                        data-target="#modal_import"><i class="icon icon-cloud-upload"></i> Import Data
                </button>
              </div>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="question_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>{{ subjectName() }}</th>
                    <th>{{ level() }}</th>
                    <th>Pertanyaan</th>
                    <th>Jenis Soal</th>
                    <th>Set Pilihan Jawaban</th>
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

  <div id="modal_question" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_question" method="post" enctype="multipart/form-data">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            @if (configuration()->type_school == 1)
              <div class="form-group">
                <label for="major_id">Jurusan <span class="text-danger">*</span></label>
                <select name="major_id" id="major_id" class="form-control">
                  <option value="">-- Pilih Jurusan --</option>
                  @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="major_id-error"></strong>
                </span>
              </div>
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
                <label for="subject_id">Mata Pelajaran <span class="text-danger">*</span></label>
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
              <label for="school_year_id">Tahun Ajar <span class="text-danger">*</span></label>
              <select name="school_year_id" id="school_year_id" class="form-control">
                <option value="">-- Pilih Tahun Ajar --</option>
                @foreach($schoolYears as $schoolYear)
                  <option value="{{ $schoolYear->id }}">{{ $schoolYear->early_year }}/{{ $schoolYear->end_year }}
                    - {{ ($schoolYear->semester == 1) ? 'Ganjil' : 'Genap' }}</option>
                @endforeach
              </select>
              <span class="text-danger">
                  <strong id="school_year_id-error"></strong>
                </span>
            </div>
            <div class="form-group">
              <label for="type_question">Jenis Soal <span class="text-danger">*</span></label>
              <select name="type_question" id="type_question" class="form-control">
                <option value="1">Text</option>
                <option value="2">Audio</option>
                <option value="3">Video</option>
              </select>
              <span class="text-danger">
                  <strong id="type_question-error"></strong>
                </span>
            </div>
            <div class="form-group document" style="display: none">
              <label for="document">Upload <span id="document_text"></span> <span
                  class="text-danger file_upload">*</span></label>
              <div class="input-group input-file">
                <input class="form-control" disabled type="text" id="document" placeholder="No file chosen"
                       style="background-color: rgba(0,0,0, 0.1)">
                <span class="input-group-btn">
                    <label class="btn btn-primary file-upload-btn">
                      <input id="document_file" class="file-upload-input" type="file" name="document">
                      <span class="icon icon-paperclip icon-lg"></span>
                    </label>
                  </span>
              </div>
              <span class="text-danger">
                  <strong id="document-error"></strong>
                </span>
            </div>
            <div class="form-group">
              <label for="question_name">Pertanyaan <span class="text-danger">*</span></label>
              <textarea name="question_name" id="question_name" class="form-control"></textarea>
              <span class="text-danger">
                  <strong id="question_name-error"></strong>
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

  <div id="modal_answer_question" role="dialog" class="modal fade">
    <div class="modal-dialog" role="document">
      <div class="modal-content ">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
              class="sr-only">Close</span></button>
          <h4 class="modal-title-update">Pilih Jawaban Soal</h4>
        </div>
        <form id="form_answer_question">
          <input type="hidden" name="question_id" id="question_id">
          <div class="modal-body">
            <div class="form-group">
              <label for="question">Pertanyaan <span class="text-danger">*</span></label>
              <div class="additional_file text-center"></div>
              <br>
              <div class="well media-body" style="background-color: #233345">
                <div class="question"></div>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="radio" name="choice" id="key_1" value="1" autocomplete='off'
                         title="Pilih jika ini merupakan kunci jawaban."/>
                </span>
                <input type="text" name="answer1" id="answer1" class="form-control" placeholder="Masukkan Jawaban 1 *"
                       autocomplete='off'/>
                <input type="hidden" name="answer_id1" id="answer_id1" class="form-control" autocomplete='off'/>
              </div>
              <span class="text-danger">
                <strong id="answer1-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="radio" name="choice" id="key_2" title="Pilih jika ini merupakan kunci jawaban." value="2"
                         autocomplete='off'/>
                </span>
                <input type="text" name="answer2" id="answer2" class="form-control" placeholder="Masukkan Jawaban 2 *"
                       autocomplete='off'/>
                <input type="hidden" name="answer_id2" id="answer_id2" class="form-control" autocomplete='off'/>
              </div>
              <span class="text-danger">
                <strong id="answer2-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="radio" name="choice" id="key_3" title="Pilih jika ini merupakan kunci jawaban." value="3"
                         autocomplete='off'/>
                </span>
                <input type="text" name="answer3" id="answer3" class="form-control" placeholder="Masukkan Jawaban 3 *"
                       autocomplete='off'/>
                <input type="hidden" name="answer_id3" id="answer_id3" class="form-control" autocomplete='off'/>
              </div>
              <span class="text-danger">
                <strong id="answer3-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="radio" name="choice" id="key_4" title="Pilih jika ini merupakan kunci jawaban." value="4"
                         autocomplete='off'/>
                </span>
                <input type="text" name="answer4" id="answer4" class="form-control" placeholder="Masukkan Jawaban 4 *"
                       autocomplete='off'/>
                <input type="hidden" name="answer_id4" id="answer_id4" class="form-control" autocomplete='off'/>
              </div>
              <span class="text-danger">
                <strong id="answer4-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <input type="radio" name="choice" id="key_5" title="Pilih jika ini merupakan kunci jawaban." value="5"
                         autocomplete='off'/>
                </span>
                <input type="text" name="answer5" id="answer5" class="form-control" placeholder="Masukkan Jawaban 5 *"
                       autocomplete='off'/>
                <input type="hidden" name="answer_id5" id="answer_id5" class="form-control" autocomplete='off'/>
              </div>
              <span class="text-danger">
                <strong id="answer5-error"></strong>
              </span>
            </div>
            <br>
            <p class="text-danger">*) Harus diisi.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn_submit_answer">Submit</button>
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
              <a href="{{ asset('excel/form_pengisian_data_soal.xlsx') }}" class="btn btn-info btn-sm"><i
                  class="fa fa-cloud-download-alt"></i> Download Format Import Data</a>
            </div>
            <div class="clearfix"></div>
            @if (configuration()->type_school == 1)
              <div class="form-group">
                <label for="major_id_import">Jurusan <span class="text-danger">*</span></label>
                <select name="major_id_import" id="major_id_import" class="form-control">
                  <option value="">-- Pilih Jurusan --</option>
                  @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="major_id_import-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="semester_id_import">Semester <span class="text-danger">*</span></label>
                <select name="semester_id_import" id="semester_id_import" class="form-control">
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
                  <strong id="semester_id_import-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="subject_id_import">Mata Kuliah <span class="text-danger">*</span></label>
                <select name="subject_id_import" id="subject_id_import" class="form-control">
                  <option value="">-- Pilih Mata Kuliah --</option>
                </select>
                <span class="text-danger">
                  <strong id="subject_id_import-error"></strong>
                </span>
              </div>
            @else
              <div class="form-group">
                <label for="grade_level_id_import">Tingkat Kelas <span class="text-danger">*</span></label>
                <select name="grade_level_id_import" id="grade_level_id_import" class="form-control">
                  <option value="">-- Pilih Tingkat Kelas --</option>
                  @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->id }}">{{ $gradeLevel->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="grade_level_id_import-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="subject_id_import">Mata Pelajaran <span class="text-danger">*</span></label>
                <select name="subject_id_import" id="subject_id_import" class="form-control">
                  <option value="">-- Pilih Mata Pelajaran --</option>
                  @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="subject_id_import-error"></strong>
                </span>
              </div>
            @endif
            <div class="form-group">
              <label for="school_year_id_import">Tahun Ajar <span class="text-danger">*</span></label>
              <select name="school_year_id_import" id="school_year_id_import" class="form-control">
                <option value="">-- Pilih Tahun Ajar --</option>
                @foreach($schoolYears as $schoolYear)
                  <option value="{{ $schoolYear->id }}">{{ $schoolYear->early_year }}/{{ $schoolYear->end_year }}
                    - {{ ($schoolYear->semester == 1) ? 'Ganjil' : 'Genap' }}</option>
                @endforeach
              </select>
              <span class="text-danger">
                  <strong id="school_year_id_import-error"></strong>
                </span>
            </div>
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
  <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
  <script type="text/javascript"
          src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-MML-AM_CHTML"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
  <script>
    let table, url, type, maxText = 100;
    let content = document.getElementById("question_name");
    CKEDITOR.replace(content, {
      filebrowserUploadUrl: '{{ route('question.upload', ['_token' => csrf_token() ]) }}',
      filebrowserUploadMethod: 'form',
      language: 'en-gb',
      extraPlugins: 'mathjax',
      toolbar: [
        {
          name: 'document',
          items: ['NewPage', 'Preview', '-', 'Templates']
        }, ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        {
          name: 'basicstyles',
          items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
        },
        {
          name: 'paragraph',
          items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']
        },
        {name: 'insert', items: ['HorizontalRule', 'Smiley', 'Table', 'SpecialChar', 'PageBreak', 'Image', 'Mathjax']},
        "/",
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
      ]
    });
    CKEDITOR.config.allowedContent = true;

    $.fn.modal.Constructor.prototype.enforceFocus = function () {
      $(document)
        .off('focusin.bs.modal') // guard against infinite focus loop
        .on('focusin.bs.modal', $.proxy(function (e) {
          if (
            this.$element[0] !== e.target && !this.$element.has(e.target).length
            // CKEditor compatibility fix start.
            && !$(e.target).closest('.cke_dialog, .cke').length
            // CKEditor compatibility fix end.
          ) {
            this.$element.trigger('focus');
          }
        }, this));
    };

    // load data in datatable
    table = $('#question_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('question.json') }}',
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
        {
          data: 'question_name', render: function (row, type, data) {
            let length = (data.question_name).length;
            let parser = new DOMParser;
            let dom = parser.parseFromString('<!doctype html><body>' + data.question_name, 'text/html');
            let decodedString = dom.body.textContent;

            let dom2 = parser.parseFromString('<!doctype html><body>' + decodedString, 'text/html');
            let decodedString2 = dom2.body.textContent;

            if (length >= 40) {
              decodedString2.substr(0, 40);
              return 'Teks terlalu panjang. <br>Cek detail klik icon <i class="icon icon-list"></i> disebelah kanan';
            } else {
              return decodedString2;
            }
          }
        },
        {data: 'type_question'},
        {data: 'select_answer_choice'},
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

    $('#major_id, #semester_id, #subject_id, #grade_level_id, #school_year_id, #major_id_import, #semester_id_import, #school_year_id_import, #subject_id_import, #grade_level_id_import, #level_filter, #subject_filter').select2({width: '100%'});

    $('#type_question').change(function () {
      const value = this.value;
      if (value == 2) {
        $('#document_text').text('Audio (Format : mp3, ogg, wav)');
        $('.document').slideDown(1000);
      } else if (value == 3) {
        $('#document_text').text('Video (Format : mp4, mkv, m4a)');
        $('.document').slideDown(1000);
      } else {
        $('.document').slideUp(1000);
      }
    });

    $('#semester_id, #major_id').change(function (e) {
      e.preventDefault();
      const semester = $('#semester_id').val();
      const major = $('#major_id').val();
      getSubject(semester, major)
    });

    $('#semester_id_import, #major_id_import').change(function (e) {
      e.preventDefault();
      const semester = $('#semester_id_import').val();
      const major = $('#major_id_import').val();
      getSubject(semester, major)
    });

    const getSubject = function (semester, major) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('question.get.subject') }}',
        type: 'get',
        data: {
          semester_id: semester,
          major_id: major
        },
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            let html = '';
            html += `<option value="">-- Pilih Mata Kuliah --</option>`;
            $.each(resp.data, function (i, v) {
              html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
            })
            $('#subject_id, #subject_id_import').html(html);
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    }

    // show modal add data
    const addData = function () {
      $('#modal_question').modal('show');
      $('.modal-title').text('Tambah Data Soal');
      url = '{{ route('question.create') }}';
      $('.file_upload').text('*');
      type = 'post';
      resetForm();
    }

    // show modal set Answer
    const setAnswer = function (id) {
      $('#modal_answer_question').modal('show');
      for (let i = 0; i < 5; i++) {
        $('#answer' + (i + 1)).val("");
        $('#answer_id' + (i + 1)).val("");
        $("#key_" + (i + 1)).prop('checked', false);
      }
      $.ajax({
        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        url: "{{ route('question.check.answer') }}",
        type: "GET",
        data: {id: id},
        dataType: "JSON",
        success: function (resp) {
          const data = resp.data;
          if (data.document == null) {
            $('.additional_file').html("");
          } else {
            let fileFormat = data.extension;
            if (fileFormat == "mp3" || fileFormat == "ogg" || fileFormat == "wav") {
              $('.additional_file').html(`<audio controls id="add_file"><source src="{!! asset('storage') !!}/${data.document}" type="audio/mpeg"></audio>`);
            } else if (fileFormat == "mp4" || fileFormat == "mkv" || fileFormat == "m4a") {
              $('.additional_file').html(`<video id="add_file" style="width: 100%; height: auto; z-index: 9999;" controls><source src="{{ asset('storage') }}/${data.document}" type="video/mp4"></video>`);
            } else {
              $('.additional_file').html(`<span class="badge badge-danger">File tidak tersedia, kemungkinan rusak atau tidak selesai upload</span><br>`);
            }
          }

          /* condition for knowing whether answer choices has exist or not */
          let parser = new DOMParser;
          let dom = parser.parseFromString('<!doctype html><body>' + resp.question, 'text/html');
          let decodedString = dom.body.textContent;

          let dom2 = parser.parseFromString('<!doctype html><body>' + decodedString, 'text/html');
          let decodedString2 = dom2.body.textContent;
          console.log(decodedString2);
          if (resp.answer != null) {
            for (let i = 0; i < resp.answer.length; i++) {
              $('#answer' + (i + 1)).val(resp.answer[i].answer_name);
              $('#answer_id' + (i + 1)).val(resp.answer[i].id);
              if (resp.answer[i].key == "1") {
                $("#key_" + (i + 1)).prop('checked', true);
              } else {
                $("#key_" + (i + 1)).prop('checked', false);
              }
            }
            $('.question').html(decodedString2);
            $('#question_id').val(resp.data.id);
          } else {
            $('.question').html(decodedString2);
            $('#question_id').val(resp.data.id);
          }
        },
        error: function (xhr, status, error) {
          alert(status + " : " + error);
        }
      });
    }

    $('#modal_answer_question').on('hidden.bs.modal', function () {
      const file = document.getElementById("add_file");
      if (file) {
        file.pause();
      }
    });

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_question').modal('show');
      $('.modal-title').text('Edit Data Soal');
      $('.file_upload').text('');
      url = '{{ route('question.update') }}';
      type = 'post';
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('question.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            if (data.semester_id != null) {
              let html = '';
              html += `<option value="">-- Pilih Mata Kuliah --</option>`;
              $.each(resp.subject, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              })
              $('#subject_id').html(html);
            }

            if (data.document != null) {
              $('.document').show();
              if (data.type_question == 2) {
                $('#document_text').text('Audio (Format : mp3, ogg, wav)');
              } else {
                $('#document_text').text('Video (Format : mp4, mkv, m4a)');
              }
            } else {
              $('.document').hide();
            }

            $('#id').val(data.id);
            $('#type_question').val(data.type_question);
            $('#grade_level_id').select2('destroy').val(data.grade_level_id).select2({width: '100%'});
            $('#semester_id').select2('destroy').val(data.semester_id).select2({width: '100%'});
            $('#school_year_id').select2('destroy').val(data.school_year_id).select2({width: '100%'});
            $('#major_id').select2('destroy').val(data.major_id).select2({width: '100%'});
            $('#subject_id').select2('destroy').val(data.subject_id).select2({width: '100%'});
            const parser = new DOMParser;
            const dom = parser.parseFromString('<!doctype html><body>' + resp.question, 'text/html');
            const decodedString = dom.body.textContent;

            const dom2 = parser.parseFromString('<!doctype html><body>' + decodedString, 'text/html');
            const decodedString2 = dom2.body.textContent;

            CKEDITOR.instances['question_name'].setData(decodedString2);
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
                url: "{{ route('question.delete') }}",
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
    $('#document_file').change(function () {
      const val = $(this).val().toLowerCase();
      const typeQuestion = $('#type_question').val();
      let regex, message;

      if (typeQuestion == 2) {
        regex = new RegExp("(.*?)\.(mp3|ogg|wav)$");
        message = 'Format yang diterima mp3, ogg, wav';
      } else if (typeQuestion == 3) {
        regex = new RegExp("(.*?)\.(mp4|mkv|m4a)$");
        message = 'Format yang diterima mp4, mkv, m4a';
      }

      if (!(regex.test(val))) {
        $(this).val('');
        alert(message);
      }
    });

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      for (let instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
      }
      const form = $('#form_question')[0];
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
            $('#modal_question').modal('hide');
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

    // submit answer
    $('.btn_submit_answer').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('question.store.answer') }}',
        type: 'post',
        data: $('#form_answer_question').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          if (data.status === 200) {
            resetForm();
            $('#modal_answer_question').modal('hide');
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

    // import data
    $('.btn-import').click(function () {
      const form = $('#form_import')[0];
      const formData = new FormData(form);
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('question.import') }}',
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

    // export data
    $('.btn-export').click(function () {
      const level = $("#level_filter").val();
      const subject = $("#subject_filter").val();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('question.link.export') }}',
        type: 'get',
        data: {
          level_filter: level,
          subject_filter: subject
        },
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeExport();
        },
        success: function (data) {
          $(location).attr('href', "{{ URL('question/export') }}" + '/' + data.level + '/' + data.subject);
          loadingAfterExport();
        },
        error: function (resp) {
          loadingAfterExport();
          alert(resp.responseJSON.message);
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
      $(".btn-submit, .btn_submit_answer").attr('disabled', 'disabled');
      $(".btn-submit, .btn_submit_answer").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-submit, .btn_submit_answer").removeAttr('disabled');
      $(".btn-submit, .btn_submit_answer").html('Submit');
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

    // method for handle before export data
    const loadingBeforeExport = function () {
      $(".btn-export").attr('disabled', 'disabled');
      $(".btn-export").html('<i class="fa fa-spinner fa-spin"></i> Loading....');
    }

    // method for handle after export data
    const loadingAfterExport = function () {
      $(".btn-export").removeAttr('disabled');
      $(".btn-export").html('<i class="icon icon-cloud-download"></i> Export Data');
    }

    // reset form
    const resetForm = function () {
      CKEDITOR.instances['question_name'].setData('');
      $('#id').val('');
      $('#document').val('');
      $('#detail_material').val('');
      $('#type_question').val(1);
      $('#major_id, #semester_id, #subject_id, #grade_level_id, #school_year_id').select2('destroy').val("").select2({width: '100%'});
    }
  </script>
@endpush
