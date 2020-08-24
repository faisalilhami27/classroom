@extends('backend.layouts.app')
@section('title', $title)
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/clockpicker.css') }}">
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
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="exam_table" class="table table-striped table-hover dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Nama Ujian</th>
                    <th>Jenis Ujian</th>
                    <th>{{ subjectName() }}</th>
                    <th>{{ level() }}</th>
                    <th>Kelas</th>
                    <th>Durasi</th>
                    <th>Jumlah Soal</th>
                    <th width="200px">Aksi</th>
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

  <div id="modal_manage_exam" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_manage_exam" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Nama Ujian <span class="text-danger">*</span></label>
                  <input type="text" name="name" autocomplete="off" id="name" class="form-control"
                         placeholder="Nama ujian">
                  <span class="text-danger">
                    <strong id="name-error"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <label for="type_exam">Jenis Ujian <span class="text-danger">*</span></label>
                  <select name="type_exam" id="type_exam" class="form-control">
                    <option value="">-- Pilih Jenis Ujian --</option>
                    <option value="1">Ulangan Harian/Kuis</option>
                    <option value="2">Ujian Tengah Semester</option>
                    <option value="3">Ujian Akhir Semester</option>
                    @if (optional(configuration())->type_school != 1)
                      <option value="4">Try Out</option>
                    @endif
                  </select>
                  <span class="text-danger">
                    <strong id="type_exam-error"></strong>
                  </span>
                </div>
                @if (optional(configuration())->type_school == 1)
                  <div class="form-group">
                    <label for="major_id">Jurusan <span class="text-danger">*</span></label>
                    <select name="major_id" id="major_id" class="form-control">
                      <option value="">-- Pilih Jurusan --</option>
                      @forelse($majors as $major)
                        <option value="{{ $major->id }}">{{ $major->name }}</option>
                      @empty
                        <option disabled>Data belum tersedia</option>
                      @endforelse
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
                      @forelse($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}">{{ $gradeLevel->name }}</option>
                      @empty
                        <option disabled>Data belum tersedia</option>
                      @endforelse
                    </select>
                    <span class="text-danger">
                      <strong id="grade_level_id-error"></strong>
                    </span>
                  </div>
                  <div class="form-group">
                    <label for="subject_id">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="subject_id" id="subject_id" class="form-control">
                      <option value="">-- Pilih Mata Pelajaran --</option>
                      @forelse($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                      @empty
                        <option disabled>Data belum tersedia</option>
                      @endforelse
                    </select>
                    <span class="text-danger">
                      <strong id="subject_id-error"></strong>
                    </span>
                  </div>
                @endif
                <div class="form-group class_id">
                  <label for="class_id">Kelas <span class="text-danger">*</span></label>
                  <select name="class_id" id="class_id" class="form-control">
                    <option value="">-- Pilih Kelas --</option>
                  </select>
                  <span class="text-danger">
                    <strong id="class_id-error"></strong>
                  </span>
                </div>
                <div class="form-group" id="data_1">
                  <div class="row">
                    <div class="col-sm-6 col-md-6">
                      <label class="font-normal" for="start_date">Tanggal Mulai Ujian <span class="text-danger">*</span></label>
                      <div class="input-group date">
                        <span class="input-group-addon bg-primary"><i class="icon icon-calendar"></i></span>
                        <input style="background-color: #233345" type="text" class="form-control input-disabled"
                               id="start_date" placeholder="yyyy-mm-dd" name="start_date" readonly>
                      </div>
                      <span class="text-danger">
                        <strong id="start_date-error"></strong>
                      </span>
                    </div>
                    <div class="col-sm-6 col-md-6">
                      <label class="font-normal" for="start_time">Waktu Mulai Ujian <span
                          class="text-danger">*</span></label>
                      <div class="input-group clock_picker" data-placement="top" data-align="top" data-autoclose="true">
                        <span class="input-group-addon bg-primary"><i class="icon icon-clock-o"></i></span>
                        <input style="background-color: #233345" type="text" id="start_time" name="start_time"
                               class="form-control input-disabled" placeholder="00:00" readonly/>
                      </div>
                      <span class="text-danger">
                        <strong id="start_time-error"></strong>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-group" id="data_2">
                  <div class="row">
                    <div class="col-sm-6 col-md-6">
                      <label class="font-normal" for="end_date">Tanggal Selesai Ujian <span class="text-danger">*</span></label>
                      <div class="input-group date">
                        <span class="input-group-addon bg-primary"><i class="icon icon-calendar"></i></span>
                        <input style="background-color: #233345" type="text" class="form-control input-disabled"
                               id="end_date" placeholder="yyyy-mm-dd" name="end_date" readonly/>
                      </div>
                      <span class="text-danger">
                        <strong id="end_date-error"></strong>
                      </span>
                    </div>
                    <div class="col-sm-6 col-md-6">
                      <label class="font-normal" for="end_time">Waktu Selesai Ujian <span
                          class="text-danger">*</span></label>
                      <div class="input-group clock_picker" data-placement="top" data-align="top" data-autoclose="true">
                        <span class="input-group-addon bg-primary"><i class="icon icon-clock-o"></i></span>
                        <input style="background-color: #233345" type="text" id="end_time" name="end_time"
                               class="form-control input-disabled" placeholder="00:00" readonly/>
                      </div>
                      <span class="text-danger">
                        <strong id="end_time-error"></strong>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="amount_question">Jumlah Soal <span class="text-danger">*</span></label>
                  <input type="number" min="1" max="" name="amount_question" id="amount_question" class="form-control"
                         placeholder="Jumlah soal ujian">
                  <span class="text-danger">
                    <strong id="amount_question-error"></strong>
                  </span>
                  <b class="pull-right text-danger show_text_amount" style="display: none">Jumlah soal ada <span
                      id="amount">0</span></b>
                </div>
                <div class="form-group">
                  <label for="duration">Durasi Ujian (Menit) <span class="text-danger">*</span></label>
                  <input type="number" min="1" autocomplete="off" name="duration" id="duration" class="form-control"
                         placeholder="Durasi ujian">
                  <span class="text-danger">
                    <strong id="duration-error"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <label for="time_violation">Waktu Pelanggaran Ujian (Detik) <span class="text-danger">*</span></label>
                  <input type="number" min="1" autocomplete="off" name="time_violation" id="time_violation"
                         class="form-control" placeholder="Waktu pelanggran ujian">
                  <span class="text-danger">
                    <strong id="time_violation-error"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <label for="show_value">Apakah akan ditampilkan nilai ujian kepada siswa ? <span
                      class="text-danger">*</span>
                    <br>
                    <label class="custom-control custom-control-primary custom-radio">
                      <input class="custom-control-input" name="show_value" type="radio" id="show_value1" value="1">
                      <span style="margin-left: 20px; font-weight: bold">Ya</span>
                      <span class="custom-control-indicator"></span>
                    </label>
                    <label class="custom-control custom-control-danger custom-radio" id="show_value2"
                           style="margin-left: 20px">
                      <input class="custom-control-input" name="show_value" type="radio" value="2">
                      <span style="margin-left: 20px; font-weight: bold">Tidak</span>
                      <span class="custom-control-indicator"></span>
                    </label>
                  </label><br>
                  <span class="text-danger">
                    <strong id="show_value-error"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <label for="exam_rules_id">Jenis Perarutan <span class="text-danger">*</span></label>
                  <select name="exam_rules_id" id="exam_rules_id" class="form-control" onchange="showText(0)">
                    <option value="">-- Pilih Jenis Peraturan --</option>
                    @forelse($examRules as $examRule)
                      <option value="{{ $examRule->id }}">{{ $examRule->name }}</option>
                    @empty
                      <option disabled>Data belum tersedia</option>
                    @endforelse
                  </select>
                  <span class="text-danger">
                    <strong id="exam_rules_id-error"></strong>
                  </span>
                </div>
                <div class="form-group">
                  <label for="">Teks Peraturan</label>
                  <div class="media-body">
                    <div class="well text_rules" style="background-color: #233345; border: 1px solid #293B51"></div>
                  </div>
                  <div class="pull-right button_read_more"></div>
                </div>
              </div>
            </div>
            <p style="margin-left: 15px">Note : *) Harus diisi</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            <button class="btn btn-primary btn-submit" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modal_select_question" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Pilih Soal</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="exam_id" id="exam_id">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="school_year_id">Tahun Ajar</label>
                <select name="school_year_id" id="school_year_id" class="form-control">
                  <option value="">-- Pilih Tahun Ajar --</option>
                  @forelse ($schoolYears as $schoolYear)
                    <option value="{{ $schoolYear->id }}">{{ $schoolYear->early_year }}
                      /{{ $schoolYear->end_year }} {{ ($schoolYear->semester == 1) ? 'Ganjil' : 'Genap' }}</option>
                  @empty
                    <option disabled>Data belum tersedia</option>
                  @endforelse
                </select>
              </div>
            </div>
            <div class="col-md-12" style="margin-top: 20px">
              <div class="pull-right">
                <button style="margin-right: 10px" class="btn btn-info btn-sm btn-lock-question"><i
                    class="icon icon-unlock"></i> Buka Soal
                </button>
                <button class="btn btn-danger btn-sm btn-delete-question"><i class="icon icon-trash-o"></i> Hapus Soal
                </button>
              </div>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="question_table" class="table table-striped table-hover dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px" class="lock_unlock">
                      <center class="text-info" style="font-size: 17px"><i class="icon icon-lock"></i></center>
                    </th>
                    <th width="20px">No</th>
                    <th width="300px">Pertanyaan</th>
                    <th width="300px">Jawaban</th>
                    <th>File Tambahan</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <p>Note : Jika akan menghapus soal silahkan buka kunci terlebih dahulu </p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_show_question" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Daftar Soal</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="select_question">Pilih Soal</label>
            <select name="select_question" id="select_question" class="form-control">
              <option value="">-- Pilih Soal --</option>
              <option value="1">Semua</option>
              <option value="2">Soal Tertentu</option>
            </select>
          </div>
          <div class="table-responsive question" style="; display: none; margin-top: 10px">
            <table id="show_question_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">
                  <label class="custom-control custom-control-primary custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="check_all_question"
                           id="check_all_question">
                    <span class="custom-control-indicator"></span>
                  </label>
                </th>
                <th width="20px">No</th>
                <th width="300px">Pertanyaan</th>
                <th width="300px">Jawaban</th>
                <th>File Tambahan</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <p>Note : Simpan soal per halaman</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary btn-submit-question" type="submit">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_detail" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Detail Ujian</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-top: 10px">
            <table class="table table-striped" width="100%">
              <tbody>
              <tr>
                <td>Nama Ujian</td>
                <td>:</td>
                <td class="exam_name"></td>
              </tr>
              <tr>
                <td>Jenis Ujian</td>
                <td>:</td>
                <td class="type_exam_detail"></td>
              </tr>
              <tr>
                <td>{{ subjectName() }}</td>
                <td>:</td>
                <td class="subject_name"></td>
              </tr>
              <tr>
                <td>{{ level() }}</td>
                <td>:</td>
                <td class="level"></td>
              </tr>
              <tr>
                <td>Kelas</td>
                <td>:</td>
                <td class="class"></td>
              </tr>
              <tr>
                <td>Ketersediaan Ujian</td>
                <td>:</td>
                <td class="time"></td>
              </tr>
              <tr>
                <td>Jumlah Soal</td>
                <td>:</td>
                <td class="amount_question_detail"></td>
              </tr>
              <tr>
                <td>Durasi Ujian</td>
                <td>:</td>
                <td class="duration_detail"></td>
              </tr>
              <tr>
                <td>Waktu Pelanggaran</td>
                <td>:</td>
                <td class="time_violation_detail"></td>
              </tr>
              <tr>
                <td>Tampilkan nilai</td>
                <td>:</td>
                <td class="show_value_detail"></td>
              </tr>
              <tr>
                <td>Jenis Peraturan</td>
                <td>:</td>
                <td class="type_rules"></td>
              </tr>
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

  <div id="modal_assign" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Assign Siswa</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="exam_id_assign" id="exam_id_assign">
          <div class="row">
            <div class="col-md-12" style="margin-top: 20px">
              <div class="pull-right button-assign" style="display: none">
                <button class="btn btn-info btn-sm btn-add-student" style="margin-right: 10px"><i
                    class="icon icon-plus"></i> Tambah Siswa
                </button>
                <button class="btn btn-danger btn-sm btn-delete-student"><i class="icon icon-trash-o"></i> Hapus Siswa
                </button>
              </div>
              <button class="btn btn-primary pull-right btn-sm btn-generate" style="margin-right: 12px"><i
                  class="icon icon-random"></i> Generate Soal Acak
              </button>
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="assign_student_table" class="table table-striped table-hover dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px" class="text-center">
                      <label class="custom-control custom-control-danger custom-checkbox">
                        <input class="custom-control-input" type="checkbox" name="check_all_assign_student" id="check_all_assign_student">
                        <span class="custom-control-indicator"></span>
                      </label>
                    </th>
                    <th width="20px">No</th>
                    <th width="300px">{{ identityNumber() }}</th>
                    <th width="300px">Nama Lengkap</th>
                    <th>Status</th>
                    <th>Nilai</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <ol style="margin-left: -30px">
            <li>Untuk menghapus siswa silahkan centang terlebih dahulu checkbox di tabel</li>
            <li>Untuk menghapus siswa silahkan hapus per halaman</li>
            <li>Setelah ujian diaktifkan maka tidak bisa menambah atau menghapus siswa</li>
          </ol>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_show_student" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Daftar Siswa</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="select_student">Pilih Siswa</label>
            <select name="select_student" id="select_student" class="form-control">
              <option value="">-- Pilih Siswa --</option>
              <option value="1">Semua Siswa</option>
              <option value="2">Siswa Tertentu</option>
            </select>
          </div>
          <div class="table-responsive student" style="margin-top: 10px; display: none">
            <table id="show_student_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">
                  <label class="custom-control custom-control-primary custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="check_all_student" id="check_all_student">
                    <span class="custom-control-indicator"></span>
                  </label>
                </th>
                <th width="20px">No</th>
                <th width="300px">{{ identityNumber() }}</th>
                <th width="300px">Nama Lengkap</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <p>Note : Simpan data per halaman</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary btn-submit-student" type="submit">Tambah</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_student" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Daftar {{ studentName() }}</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-top: 10px">
            <table id="student_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">No</th>
                <th>{{ sortIdentityNumber() }}</th>
                <th>Nama {{ studentName() }}</th>
                <th>Pelanggaran</th>
                <th>Aksi</th>
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
  <div id="modal_score_student" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Nilai Ujian</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-top: 10px">
            <table id="score_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">No</th>
                <th>KKM</th>
                <th>Nilai</th>
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
  <div id="modal_violation_student" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Pelanggran Siswa</h4>
        </div>
        <div class="modal-body">
          <div class="show-violation"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script src="{{ asset('js/clockpicker.js') }}"></script>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    let table, url, type, textRules, tableTemporary, tableQuestion, tableStudent, lock = 0;

    const styles = {
      duration: function (row, type, data) {
        return data.duration + ' Menit';
      }
    }

    // load data in datatable
    table = $('#exam_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      destroy: true,
      order: [],

      ajax: {
        "url": '{{ route('manage.exam.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'name'},
        {data: 'type_exam'},
        {data: 'subject.name'},
        {data: 'level'},
        {data: 'class'},
        {data: 'duration', render: styles.duration},
        {data: 'amount_question'},
        {data: 'action', sClass: 'text-right'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2],
          orderable: false
        },
      ],
    });

    $('.btn-lock-question').click(function () {
      if (lock === 0) {
        $('.lock_unlock').html(`<center><label class="custom-control custom-control-danger custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="delete_all_question" id="delete_all_question">
                                  <span class="custom-control-indicator"></span>
                                 </label></center>`)
        lock = 1;
        $(this).removeClass('btn-info')
          .addClass('btn-danger')
          .html('<i class="icon icon-lock"></i> Kunci Soal');
      } else if (lock === 1) {
        $('.lock_unlock').html(`<center class="text-info" style="font-size: 17px"><i class="icon icon-lock"></i></center>`);
        lock = 2;
        $(this).removeClass('btn-danger')
          .addClass('btn-info')
          .html('<i class="icon icon-unlock"></i> Buka Soal');
      } else if (lock === 2) {
        $('.lock_unlock').html(`<center><label class="custom-control custom-control-danger custom-checkbox">
                                  <input class="custom-control-input" type="checkbox" name="delete_all_question" id="delete_all_question">
                                  <span class="custom-control-indicator"></span>
                                 </label></center>`)
        lock = 1;
        $(this).removeClass('btn-info')
          .addClass('btn-danger')
          .html('<i class="icon icon-lock"></i> Kunci Soal');
      }
      tableTemporary.ajax.reload();
    });

    $('input[name="check_all_question"]').change(function () {
      if ($(this).is(':checked')) {
        $('input[name="checkbox_question[]"]').prop('checked', true);
      } else {
        $('input[name="checkbox_question[]"]').prop('checked', false);
      }
    });

    $('#select_question').change(function () {
      const value = this.value;
      if (value === "1") {
        $('.question').hide();
        $('input[name="checkbox_question[]"]').prop('checked', false);
      } else {
        $('.question').show();
      }
    });

    $('#semester_id, #major_id').change(function (e) {
      e.preventDefault();
      const level = $('#semester_id').val();
      const major = $('#major_id').val();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('manage.exam.get') }}',
        type: 'get',
        data: {
          level: level,
          major_id: major
        },
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            let html = '';
            html += `<option value="">-- Pilih {{ subjectName() }} --</option>`;
            $.each(resp.subjects, function (i, v) {
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
    });

    $('#school_year_id').change(function () {
      if (this.value !== "") {
        $('#modal_show_question').modal('show');
        $('input[name="check_all_question"]').prop('checked', false);
        $('#select_question').val('');
        $('.question').hide();
        // show datatable question bank
        tableQuestion = $('#show_question_table').DataTable({
          processing: true,
          serverSide: true,
          responsive: true,
          destroy: true,
          order: [],

          ajax: {
            "url": '{{ route('manage.exam.question.json') }}',
            "type": 'post',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            "data": function (d) {
              d.school_year_id = $('#school_year_id').val()
              d.exam_id = $('#exam_id').val()
            }
          },

          columns: [
            {data: 'checkbox', orderable: false, searchable: false},
            {data: 'DT_RowIndex', searchable: false, orderable: false},
            {data: 'question', render: function (row, type, data) {
                const questionLength = data.question_name;
                const countLength = questionLength.length;
                const parser = new DOMParser;
                const dom = parser.parseFromString(questionLength,'text/html');
                const decodedString = dom.body.textContent;

                const dom2 = parser.parseFromString(decodedString,'text/html');
                const decodedString2 = dom2.body.textContent;
                if (countLength > 100) {
                  return "<div class='tooltip-demo' data-toggle='tooltip' data-html='true' data-placement='right' title='"+decodedString2+"'>Soal terlalu panjang, dekatkan cursor ke teks ini untuk melihat detail soal</div>";
                }else {
                  return decodedString2;
                }
              }},
            {data: 'answer'},
            {data: 'document'},
          ],

          columnDefs: [
            {
              targets: [0, -1, -2],
              orderable: false
            },
          ],
        });
      }
    });

    $('#subject_id').change(function (e) {
      e.preventDefault();
      const subject = this.value;
      const semester = $('#semester_id').val();
      const gradeLevel = $('#grade_level_id').val();
      $('#amount_question').val('');
      if (subject == "") {
        $('.show_text_amount').hide();
      } else {
        $('.show_text_amount').show();
      }
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('manage.exam.get.amount') }}',
        type: 'get',
        data: {
          subject_id: subject,
          semester_id: semester,
          grade_level_id: gradeLevel
        },
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            $('#amount').text(resp.data);
            if (resp.data === 0) {
              $('#amount_question').attr({
                "max": 0,
                "min": 0
              });
            } else {
              $('#amount_question').attr({
                "max": resp.data,
                "min": 1
              });
            }

            let html = '';
            html += `<option value="">-- Pilih Kelas --</option>`;
            $.each(resp.classes, function (i, v) {
              html += `<option value="${v.id}">${v.class_name}</option>`;
            })
            $('#class_id').html(html);
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    });

    $('.clock_picker').clockpicker()
    let date = new Date();
    date.setDate(date.getDate());
    $('#data_1 .input-group.date, #data_2 .input-group.date').datepicker({
      format: 'yyyy-mm-dd',
      startDate: date,
      todayBtn: "linked",
      keyboardNavigation: false,
      forceParse: false,
      calendarWeeks: true,
      todayHighlight: true,
      autoclose: true
    });

    $('#semester_id, #class_id, #subject_id, #major_id, #grade_level_id, #school_year_id, #level_filter, #subject_filter, #exam_rules_id, #type_exam').select2({width: '100%'});

    // show modal add data
    const addData = function () {
      $('#modal_manage_exam').modal('show');
      $('.show_text_amount').show();
      $('.modal-title').text('Tambah Ujian');
      url = '{{ route('manage.exam.create') }}';
      type = 'post';
      resetForm();
    }

    // show modal detail data
    const showDetail = function (id) {
      $('#modal_detail').modal('show');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('manage.exam.detail') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('.exam_name').text(data.exam_name);
            $('.level').text(data.level);
            $('.class').text(data.student_class);
            $('.type_exam_detail').text(data.type_exam_detail);
            $('.time').text(data.time);
            $('.amount_question_detail').text(data.amount_question_detail);
            $('.subject_name').text(data.subject_name);
            $('.duration_detail').text(data.duration_detail);
            $('.time_violation_detail').text(data.time_violation_detail);
            $('.show_value_detail').text(data.show_value_detail);
            $('.type_rules').text(data.type_rules);
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    }

    // open modal select question
    const selectQuestion = function (id) {
      $('#modal_select_question').modal('show');
      $('#exam_id').val(id);
      $('#check_all_question').prop('checked', false);
      $('#school_year_id').select2('destroy').val('').select2({width: '100%'});
      getQuestion(id);
      tableTemporary.on('change', '#delete_all_question', function () {
        if ($(this).is(':checked')) {
          $('input[name="delete_question[]"]').prop('checked', true);
        } else {
          $('input[name="delete_question[]"]').prop('checked', false);
        }
      });
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_manage_exam').modal('show');
      $('.show_text_amount').show();
      $('.modal-title').text('Edit Ujian');
      url = '{{ route('manage.exam.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('manage.exam.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            if (data.semester_id != null) {
              let html = '';
              html += `<option value="">-- Pilih {{ subjectName() }} --</option>`;
              $.each(resp.subjects, function (i, v) {
                html += `<option value="${v.id}">${v.code} - ${v.name}</option>`;
              })
              $('#subject_id').html(html);
            }

            let html2 = '';
            html2 += `<option value="">-- Pilih Kelas --</option>`;
            $.each(resp.classes, function (i, v) {
              html2 += `<option value="${v.id}">${v.class_name}</option>`;
            })
            $('#class_id').html(html2);

            $('#amount').text(resp.count);
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#duration').val(data.duration);
            $('#time_violation').val(data.time_violation);
            $('#start_date').val(moment(data.start_date).format('YYYY-MM-DD'));
            $('#start_time').val(moment(data.start_date).format('HH:mm'));
            $('#end_date').val(moment(data.end_date).format('YYYY-MM-DD'));
            $('#end_time').val(moment(data.end_date).format('HH:mm'));
            $('#amount_question').val(data.amount_question);
            $('#semester_id').select2('destroy').val(data.semester_id).select2({width: '100%'});
            $('#class_id').select2('destroy').val(data.exam_class.class_id).select2({width: '100%'});
            $('#grade_level_id').select2('destroy').val(data.grade_level_id).select2({width: '100%'});
            $('#subject_id').select2('destroy').val(data.subject_id).select2({width: '100%'});
            $('#major_id').select2('destroy').val(data.major_id).select2({width: '100%'});
            $('#exam_rules_id').select2('destroy').val(data.exam_rules_id).select2({width: '100%'});
            $('#type_exam').select2('destroy').val(data.type_exam).select2({width: '100%'});

            if (data.show_value == 1) {
              $('#show_value1').prop('checked', true);
            } else {
              $('#show_value2').prop('checked', true);
            }

            const parser = new DOMParser;
            const dom = parser.parseFromString('<!doctype html><body>' + resp.text, 'text/html');
            const text = dom.body.textContent;
            textRules = text;
            if (textRules.length > 200) {
              $('.text_rules').html(text.substr(0, 200) + " .....");
              $('.button_read_more').html('<a class="btn btn-xs btn-white" onClick="showText(1)"><i class="fa fa-arrow-down"></i> Baca Selengkapnya </a>');
            } else {
              $('.text_rules').html(text);
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

    // activate exam
    const activateExam = function (id) {
      $.confirm({
        content: 'Ketika sudah diaktifkan anda tidak bisa merubah ada kembali',
        title: 'Apakah yakin akan mengaktifkan ujian ?',
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
            text: '<i class="icon icon-power-off"></i> Aktifkan',
            btnClass: 'btn-success',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('manage.exam.activate.exam') }}",
                type: "post",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  if (data.status === 200) {
                    setTimeout(function () {
                      location.reload();
                    }, 1000)
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
                url: "{{ route('manage.exam.delete') }}",
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

    const showStudent = function (id) {
      $('#modal_student').modal('show');
      tableStudent = $('#student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('manage.exam.json.student') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.exam_id = id
          }
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'student.student_identity_number'},
          {data: 'student.name'},
          {data: 'violation'},
          {data: 'action', sClass: 'text-center'},
        ],

        columnDefs: [
          {
            targets: [0, -1],
            orderable: false
          },
        ],
      });
    }

    const showViolation = function (id) {
      $('#modal_violation_student').modal('show');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('manage.exam.violation') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (data) {
          let html = '';
          if (data.data.length == 0) {
            html += `<center><h3>Tidak ada pelanggaran</h3></center>`;
          } else {
            html += `<ol>`;
            $.each(data.data, function (i, v) {
              html += `<li>${v.violation_name}</li>`
            });
            html += `</ol>`;
          }
          $('.show-violation').html(html);
        },
        error: function (resp) {
          alert(resp.responseJSON.message)
        }
      });
    }

    const showScore = function (studentId, examId) {
      $('#modal_score_student').modal('show');
      $('#score_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('manage.exam.json.student.score') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.student_id = studentId
            d.exam_id = examId
          }
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'minimal'},
          {data: 'score'}
        ],

        columnDefs: [
          {
            targets: [0],
            orderable: false
          },
        ],
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
        data: $('#form_manage_exam').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          if (data.status === 200) {
            $('#modal_manage_exam').modal('hide');
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

    // submit data temporary question
    $('.btn-submit-question').click(function (e) {
      e.preventDefault();
      const arrayQuestion = [];
      const exam = $('#exam_id').val();
      const select = $('#select_question').val();
      const schoolYear = $('#school_year_id').val();
      $(".checkbox_question:checked").each(function () {
        arrayQuestion.push(this.value);
      });

      if (arrayQuestion.length === 0 && select === "2") {
        notification(500, 'Soal harus ada yang dipilih');
      } else {
        const countQuestion = (select === "1") ? "semua" : arrayQuestion.length;
        $.confirm({
          content: `Akan mengambil ${countQuestion} soal`,
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
              text: '<i class="icon icon-save"></i> Simpan',
              btnClass: 'btn-info',
              action: function () {
                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                  },
                  url: "{{ route('manage.exam.create.question') }}",
                  type: "post",
                  data: {
                    exam_id: exam,
                    select: select,
                    school_year_id: schoolYear,
                    question_list: arrayQuestion
                  },
                  dataType: "json",
                  success: function (data) {
                    notification(data.status, data.message);
                    if (data.status === 200) {
                      if (select === '1') {
                        $("#modal_show_question").modal('hide');
                        $("#school_year_id").select2('destroy').val('').select2({width: '100%'});
                      }
                      tableTemporary.ajax.reload();
                      table.ajax.reload();
                      tableQuestion.ajax.reload();
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
      }
    });

    // delete data temporary question
    $('.btn-delete-question').click(function (e) {
      e.preventDefault();
      const arrayQuestion = [];
      $(".delete_question:checked").each(function () {
        arrayQuestion.push(this.value);
      });

      if (arrayQuestion.length === 0) {
        notification(500, 'Soal harus ada yang dipilih');
      } else {
        $.confirm({
          content: `Akan menghapus ${arrayQuestion.length} soal yang ada`,
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
                  url: "{{ route('manage.exam.delete.id') }}",
                  type: "delete",
                  data: {
                    question_list: arrayQuestion
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
      }
    });

    // show text rules
    const showText = function (status) {
      const id = $('#exam_rules_id').val();
      if (id != "") {
        if (status === 0) {
          $.ajax({
            url: '{{ route('manage.exam.rules') }}',
            type: 'get',
            data: {id: id},
            dataType: 'json',
            success: function (resp) {
              const parser = new DOMParser;
              const dom = parser.parseFromString('<!doctype html><body>' + resp.text, 'text/html');
              const text = dom.body.textContent;
              if (resp.status === 200) {
                textRules = text;
                if (textRules.length > 200) {
                  $('.text_rules').html(text.substr(0, 200) + " .....");
                  $('.button_read_more').html('<a class="btn btn-xs btn-white" onClick="showText(1)"><i class="icon icon-arrow-down"></i> Baca Selengkapnya </a>');
                } else {
                  $('.text_rules').html(text);
                }
              } else {
                notification(resp.status, resp.message);
              }
            },
            error: function (xhr, status, error) {
              alert(status + ' : ' + error);
            }
          });
        } else if (status === 1) {
          $('.text_rules').html(textRules);
          $('.button_read_more').html('<a class="btn btn-xs btn-white" onClick="showText(2)"><i class="icon icon-arrow-up"></i> Persingkat </a>');
        } else if (status === 2) {
          $('.text_rules').html(textRules.substr(0, 200) + " .....");
          $('.button_read_more').html('<a class="btn btn-xs btn-white" onClick="showText(1)"><i class="icon icon-arrow-down"></i> Baca Selengkapnya </a>');
        }
      } else {
        $('.text_rules').html('');
        $('.button_read_more').html('');
      }
    }

    // method for handle before send data
    const loadingBeforeSend = function () {
      $(".btn-submit, .btn-submit-question, .btn-submit-question").attr('disabled', 'disabled');
      $(".btn-submit, .btn-submit-question, .btn-submit-question").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-submit, .btn-submit-question, .btn-submit-question").removeAttr('disabled');
      $(".btn-submit, .btn-submit-question, .btn-submit-question").html('Submit');
    }

    // reset form
    const resetForm = function () {
      $('#id').val('');
      $('#name').val('');
      $('#type_exam').val('');
      $('#duration').val('');
      $('#time_violation').val('');
      $('#start_date').val('');
      $('#start_time').val('');
      $('#amount_question').val('');
      $('.show_text_amount').hide();
      $('input[name="show_value"]').prop('checked', false);
      $('input[name="check"]').prop('checked', false);
      $('.show_text_amount').text();
      $('#major_id, #class_id, #semester_id, #subject_id, #grade_level_id, #exam_rules_id, #type_exam').select2('destroy').val('').select2({width: '100%'});
    }

    $('body').tooltip({selector: '[data-toggle="tooltip"]', animated: 'fade'});

    // assign student
    let tableAssign, tableAssignStudent, examId = 0;
    const styles1 = {
      duration: function (row, type, data) {
        return data.duration + ' Menit';
      }
    }

    table.on('click', '.btn-assign', function () {
      $('#modal_assign').modal('show');
      const id = $(this).attr('id');
      $('#exam_id_assign').val(id);
      getStudent(id);
      $.ajax({
        url: '{{ route('manage.exam.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            if (data.status == 1) {
              $('.button-assign').hide();
            } else {
              $('.button-assign').show();
            }
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    });

    $('#select_student').change(function () {
      const value = this.value;
      if (value === "1" || value === "") {
        $('.student').hide();
        $('input[name="checkbox_student[]"]').prop('checked', false);
      } else {
        $('.student').show();
      }
    });

    $('.btn-add-student').click(function () {
      $('#modal_show_student').modal('show');
      $('#check_all_student').prop('checked', false);
      $('#select_student').val('');
      $('.student').hide();
      tableStudent = $('#show_student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('assign.json.search') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.exam_id = $('#exam_id_assign').val()
          }
        },

        columns: [
          {data: 'checkbox'},
          {data: 'DT_RowIndex', searchable: false, orderable: false},
          {data: 'student_identity_number'},
          {data: 'name'},
        ],

        columnDefs: [
          {
            targets: [0, -1, -2],
            orderable: false
          },
        ],
      });
    });

    $('input[name="check_all_student"]').change(function () {
      if ($(this).is(':checked')) {
        $('input[name="checkbox_student[]"]').prop('checked', true);
      } else {
        $('input[name="checkbox_student[]"]').prop('checked', false);
      }
    });

    $('input[name="check_all_assign_student"]').change(function () {
      if ($(this).is(':checked')) {
        $('input[name="checkbox_assign_student[]"]').prop('checked', true);
      } else {
        $('input[name="checkbox_assign_student[]"]').prop('checked', false);
      }
    });

    $('.btn-generate').click(function (e) {
      e.preventDefault();
      const exam = $('#exam_id_assign').val();
      $.confirm({
        content: `Data yang sudah digenerate tidak bisa diubah lagi`,
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
            text: '<i class="icon icon-random"></i> Generate',
            btnClass: 'btn-info',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('assign.generate') }}",
                type: "post",
                data: {
                  exam_id: exam
                },
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  if (data.status === 200) {
                    setTimeout(function () {
                      location.reload();
                    }, 2000);
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

    $('.btn-submit-student').click(function (e) {
      e.preventDefault();
      const arrayStudent = [];
      const exam = $('#exam_id_assign').val();
      const select = $('#select_student').val();
      $(".checkbox_student:checked").each(function () {
        arrayStudent.push(this.value);
      });

      if (arrayStudent.length === 0 && select === "2") {
        notification(500, 'Siswa harus ada yang dipilih');
      } else {
        const countStudent = (select === "1") ? "semua" : arrayStudent.length;
        $.confirm({
          content: `Akan memilih ${countStudent} siswa`,
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
              text: '<i class="icon icon-save"></i> Simpan',
              btnClass: 'btn-info',
              action: function () {
                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                  },
                  url: "{{ route('assign.create') }}",
                  type: "post",
                  data: {
                    exam_id: exam,
                    student_id: arrayStudent,
                    type: select
                  },
                  dataType: "json",
                  success: function (data) {
                    notification(data.status, data.message);
                    if (data.status === 200) {
                      $("#modal_show_student").modal('hide');
                      tableAssignStudent.ajax.reload();
                      tableStudent.ajax.reload();
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
      }
    });

    $('.btn-delete-student').click(function (e) {
      e.preventDefault();
      const arrayAssignStudent = [];
      $(".checkbox_assign_student:checked").each(function () {
        arrayAssignStudent.push(this.value);
      });

      if (arrayAssignStudent.length === 0) {
        notification(500, 'Siswa harus ada yang dipilih');
      } else {
        $.confirm({
          content: `Akan akan menghapus ${arrayAssignStudent.length} siswa`,
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
              text: '<i class="icon icon-save"></i> Simpan',
              btnClass: 'btn-info',
              action: function () {
                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                  },
                  url: "{{ route('assign.delete') }}",
                  type: "delete",
                  data: {
                    id: arrayAssignStudent
                  },
                  dataType: "json",
                  success: function (data) {
                    notification(data.status, data.message);
                    if (data.status === 200) {
                      tableAssignStudent.ajax.reload();
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
      }
    });

    function getStudent(id) {
      tableAssignStudent = $('#assign_student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('assign.json.student') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.exam_id = id
          }
        },

        columns: [
          {data: 'checkbox'},
          {data: 'DT_RowIndex', searchable: false, orderable: false},
          {data: 'student.student_identity_number'},
          {data: 'student.name'},
          {data: 'status'},
          {data: 'score'},
        ],

        columnDefs: [
          {
            targets: [0, -1, -2],
            orderable: false
          },
        ],
      });
    }

    function getQuestion(id) {
      tableTemporary = $('#question_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('manage.exam.saved.json') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.lock_unlock = lock,
            d.exam_id = id
          }
        },

        columns: [
          {data: 'checkbox'},
          {data: 'DT_RowIndex', searchable: false, orderable: false},
          {data: 'question', render: function (row, type, data) {
              const questionLength = data.question_bank.question_name;
              const countLength = questionLength.length;
              const parser = new DOMParser;
              const dom = parser.parseFromString(questionLength,'text/html');
              const decodedString = dom.body.textContent;

              const dom2 = parser.parseFromString(decodedString,'text/html');
              const decodedString2 = dom2.body.textContent;
              if (countLength > 100) {
                return "<div class='tooltip-demo' data-toggle='tooltip' data-html='true' data-placement='right' title='"+decodedString2+"'>Soal terlalu panjang, dekatkan cursor ke teks ini untuk melihat detail soal</div>";
              }else {
                return decodedString2;
              }
            }},
          {data: 'answer'},
          {data: 'document',},
        ],

        columnDefs: [
          {
            targets: [0, -1, -2],
            orderable: false
          },
        ],
      });
    }
  </script>
@endpush
