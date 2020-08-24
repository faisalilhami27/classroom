@extends('backend.layouts.app')
@section('title', $title)
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
                <table id="exam_table" class="table table-striped table-hover dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Nama Ujian</th>
                    <th>Jenis Ujian</th>
                    <th>{{ subjectName() }}</th>
                    <th>Kelas</th>
                    <th>{{ level() }}</th>
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
  <div id="modal_student" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title">Progress Ujian</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-hover table-striped">
                <tbody>
                  <tr>
                    <td>Total semua siswa</td>
                    <td>:</td>
                    <td class="total"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa mengikuti ujian</td>
                    <td>:</td>
                    <td class="student_exam"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa belum mengikuti ujian</td>
                    <td>:</td>
                    <td class="not_yet"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa tidak mengikuti ujian</td>
                    <td>:</td>
                    <td class="student_not_exam"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa lulus</td>
                    <td>:</td>
                    <td class="pass"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa pas kkm</td>
                    <td>:</td>
                    <td class="minimal"></td>
                  </tr>
                  <tr>
                    <td>Jumlah siswa tidak lulus</td>
                    <td>:</td>
                    <td class="not_pass"></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-6 chart">
              <canvas id="exam_chart"></canvas>
            </div>
          </div>
          <button type="button" class="btn btn-info btn-sm" onclick="scoreExport()"><i class="icon icon-file-excel-o"></i> Export Nilai Ujian</button>
          <div class="table-responsive" style="margin-top: 10px">
            <table id="student_table" class="table table-striped table-hover dataTable" width="100%">
              <thead>
              <tr>
                <th width="20px">No</th>
                <th>{{ sortIdentityNumber() }}</th>
                <th>Nama {{ studentName() }}</th>
                <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <p>Note : Nilai diambil dari nilai tertinggi jika mengikuti remedial</p>
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
@stop
@push('scripts')
  <script>
    let table, tableStudent;
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
        "url": '{{ route('progress.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'name'},
        {data: 'type_exam'},
        {data: 'subject.name'},
        {data: 'class'},
        {data: 'level'},
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

    const showStudent = function (id) {
      $('#modal_student').modal('show');
      $('#exam_chart').remove();
      $('#id').val(id);
      $('.chart').append('<canvas id="exam_chart"></canvas>');
      getChart(id);
      tableStudent = $('#student_table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [],

        ajax: {
          "url": '{{ route('progress.json.student') }}',
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

    const scoreExport = function () {
      const id = $('#id').val();
      window.open(`{{ URL('progress/export') }}/${id}`, '_blank');
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
          "url": '{{ route('progress.json.student.score') }}',
          "type": 'post',
          "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
          "data": function (d) {
            d.student_id = studentId
            d.exam_id = examId
          }
        },

        columns: [
          {data: 'DT_RowIndex', searchable: false},
          {data: 'remedial'},
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

    function getChart(id) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('progress.chart') }}',
        type: 'post',
        data: {
          exam_id: id
        },
        dataType: 'json',
        success: function (data) {
          $('.total').html(data.data.total);
          $('.student_exam').html(data.data.exam);
          $('.not_yet').html(data.data.not_yet);
          $('.student_not_exam').html(data.data.not_exam);
          $('.pass').html(data.data.pass);
          $('.minimal').html(data.data.minimal);
          $('.not_pass').html(data.data.not_pass);
          examChart(data.data);
        },
        error: function (xhr, error, status) {
          alert(status + " : " + error);
        }
      });
    }

    function examChart(data) {
      const examChart = $('#exam_chart');
      Chart.defaults.scale.ticks.beginAtZero = true;
      new Chart(examChart, {
        type: 'doughnut',
        data: {
          labels: [
            "Lulus",
            "KKM",
            "Tidak Lulus",
          ],
          datasets: [
            {
              data: [
                data.pass,
                data.minimal,
                data.not_pass
              ],
              backgroundColor: [
                "#00de00",
                "#ffa330",
                "#F44336"
              ],
              borderColor: [
                "#00de00",
                "#ffa330",
                "#F44336"
              ],
              borderWidth: 0.5
            }]
        },
        options: {
          cutoutPercentage: 40,
          animation: {
            animateScale: true
          }
        }
      });
    }
  </script>
@endpush
