@extends('backend.layouts.app')
@section('title', $title)
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/clockpicker.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap-duallistbox.min.css') }}">
@endpush
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      <div class="row gutter-xs">
        <div class="col-xs-12">
          <div class="card">
            <div class="card-header">
              <strong>{{ $title }}</strong>
            </div>
            <div class="card-body">
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="rules_table" class="table table-striped table-hover dataTable" width="100%">
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

  <div id="modal_remedial" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_remedial" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="select_question">Pilih Soal <span class="text-danger">*</span></label>
              <select name="select_question" id="select_question" class="form-control">
                <option value="">-- Pilih Soal --</option>
                <option value="1">Gunakan Soal Sebelumnya</option>
                <option value="2">Random Soal Baru</option>
              </select>
              <span class="text-danger">
                <strong id="select_question-error"></strong>
              </span>
            </div>
            <div class="form-group" id="data_1">
              <div class="row">
                <div class="col-sm-6 col-md-6">
                  <label class="font-normal" for="start_date">Tanggal Mulai Remedial <span class="text-danger">*</span></label>
                  <div class="input-group date">
                    <span class="input-group-addon bg-primary"><i class="icon icon-calendar"></i></span>
                    <input style="background-color: #233345" type="text" class="form-control input-disabled" id="start_date" placeholder="yyyy-mm-dd" name="start_date" readonly>
                  </div>
                  <span class="text-danger">
                    <strong id="start_date-error"></strong>
                  </span>
                </div>
                <div class="col-sm-6 col-md-6">
                  <label class="font-normal" for="start_time">Waktu Mulai Remedial <span class="text-danger">*</span></label>
                  <div class="input-group clock_picker" data-placement="top" data-align="top" data-autoclose="true">
                    <span class="input-group-addon bg-primary"><i class="icon icon-clock-o"></i></span>
                    <input style="background-color: #233345" type="text" id="start_time" name="start_time" class="form-control input-disabled" placeholder="00:00" readonly/>
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
                  <label class="font-normal" for="end_date">Tanggal Selesai Remedial <span class="text-danger">*</span></label>
                  <div class="input-group date">
                    <span class="input-group-addon bg-primary"><i class="icon icon-calendar"></i></span>
                    <input style="background-color: #233345" type="text" class="form-control input-disabled" id="end_date" placeholder="yyyy-mm-dd" name="end_date" readonly/>
                  </div>
                  <span class="text-danger">
                    <strong id="end_date-error"></strong>
                  </span>
                </div>
                <div class="col-sm-6 col-md-6">
                  <label class="font-normal" for="end_time">Waktu Selesai Remedial <span class="text-danger">*</span></label>
                  <div class="input-group clock_picker" data-placement="top" data-align="top" data-autoclose="true">
                    <span class="input-group-addon bg-primary"><i class="icon icon-clock-o"></i></span>
                    <input style="background-color: #233345" type="text" id="end_time" name="end_time" class="form-control input-disabled" placeholder="00:00" readonly/>
                  </div>
                  <span class="text-danger">
                    <strong id="end_time-error"></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="select_student_category">Pilih Kategori Siswa <span class="text-danger">*</span></label>
              <select name="select_student_category" id="select_student_category" class="form-control">
                <option value="">-- Pilih Kategori Siswa --</option>
                <option value="1">Kurang dari KKM</option>
                <option value="2">Pas KKM</option>
                <option value="3">Di bawah dan pas KKM</option>
              </select>
              <span class="text-danger">
                <strong id="select_student_category-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="student_id">Pilih Siswa <span class="text-danger">*</span></label>
              <select name="student_id[]" id="student_id" multiple class="form-control"></select>
              <span class="text-danger">
                <strong id="student_id-error"></strong>
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
  <div id="modal_student" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">{{ studentName() }} Remedial</h4>
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
          <h4 class="modal-title">Nilai Remedial</h4>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-top: 10px">
            <table id="score_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">No</th>
                <th>Remedial</th>
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
  <script src="{{ asset('backend/js/jquery.bootstrap-duallistbox.min.js') }}"></script>
  <script src="{{ asset('js/clockpicker.js') }}"></script>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
    let table, url, type, select, tableStudent;
    const styles = {
      duration: function (row, type, data) {
        return data.duration + ' Menit';
      }
    }

    // load data in datatable
    table = $('#rules_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      destroy: true,
      order: [],

      ajax: {
        "url": '{{ route('remedial.json') }}',
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
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2],
          orderable: false
        },
      ],
    });

    select = $('select[name="student_id[]"]').bootstrapDualListbox({
      selectorMinimalHeight: 160
    });

    $(function() {
      let customSettings = $('select[name="student_id[]"]').bootstrapDualListbox('getContainer');
      customSettings.find('.moveall i').removeClass().addClass('icon icon-arrow-right').next().remove();
      customSettings.find('.removeall i').removeClass().addClass('icon icon-arrow-left').next().remove();
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

    // show modal add data
    const addData = function (id) {
      $('#modal_remedial').modal('show');
      $('#id').val(id);
      $('.modal-title').text('Tambah Data Remedial');
      resetForm();
    }

    $('#select_student_category').change(function () {
      const id = $('#id').val();
      const category = this.value;
      select.bootstrapDualListbox('refresh', true);
      if (category !== "") {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          url: '{{ route('remedial.student') }}',
          type: 'get',
          data: {
            exam_id: id,
            select_student_category: category
          },
          dataType: 'json',
          success: function (data) {
            if (data.status === 200) {
              let html = '';
              data.data.map(item => {
                html += `<option value="${item.id}">${item.student.student_identity_number} - ${item.student.name}</option>`;
              });

              $('#student_id').html(html);
              select.bootstrapDualListbox('refresh', true);
            } else {
              $('#student_id').html('');
              select.bootstrapDualListbox('refresh', true);
              notification(data.status, data.message);
            }
          },
          error: function (resp) {
            alert(resp.responseJSON.message)
          }
        });
      } else {
        $('#student_id').html('');
        select.bootstrapDualListbox('refresh', true);
      }
    });

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('remedial.create') }}',
        type: 'post',
        data: $('#form_remedial').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          if (data.status === 200) {
            $('#modal_remedial').modal('hide');
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

    const showStudent = function (id) {
      $('#modal_student').modal('show');
      tableStudent = $('#student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('remedial.json.student') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.exam_id = id
          }
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'assign_student.student.student_identity_number'},
          {data: 'assign_student.student.name'},
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

    const showViolation = function (id, remedialId) {
      $('#modal_violation_student').modal('show');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('remedial.violation') }}',
        type: 'get',
        data: {
          id: id,
          remedial_id: remedialId
        },
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
          "url": '{{ route('remedial.json.student.score') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.student_id = studentId
            d.exam_id = examId
          }
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'exam_to'},
          {data: 'minimal'},
          {data: 'score'}
        ],

        columnDefs: [
          {
            targets: [0, -1, -2],
            orderable: false
          },
        ],
      });
    }

    // method for handle before send data
    const loadingBeforeSend = function () {
      $(".btn-submit, .btn-submit-student").attr('disabled', 'disabled');
      $(".btn-submit, .btn-submit-student").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-submit, .btn-submit-student").removeAttr('disabled');
      $(".btn-submit, .btn-submit-student").html('Submit');
    }

    // reset form
    const resetForm = function () {
      $('#start_date').val('');
      $('#start_time').val('');
      $('#end_time').val('');
      $('#end_date').val('');
      $('#select_student_category').val('');
      $('#select_question').val('');
      $('#student_id').html('');
      select.bootstrapDualListbox('refresh', true);
    }
  </script>
@endpush
