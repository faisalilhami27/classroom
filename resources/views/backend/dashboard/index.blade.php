@extends('backend.layouts.app')
@section('title', 'Dashboard')
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      <div class="row gutter-xs">
        <div class="col-xs-6 col-md-3">
          <div class="card bg-primary no-border">
            <div class="card-values">
              <div class="p-x">
                <small>Jumlah Kelas</small>
                <h3 class="card-title fw-l">{{ $data->classes }}</h3>
              </div>
            </div>
            <div class="card-chart">
              <canvas data-chart="line" data-animation="false"
                      data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]'
                      data-values='[{"backgroundColor": "transparent", "borderColor": "#ffffff", "data": [25250, 23370, 25568, 28961, 26762, 30072, 25135]}]'
                      data-scales='{"yAxes": [{ "ticks": {"max": 31072}}]}'
                      data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]'
                      height="35"></canvas>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
          <div class="card bg-danger no-border">
            <div class="card-values">
              <div class="p-x">
                <small>Jumlah Siswa</small>
                <h3 class="card-title fw-l">{{ $data->students }}</h3>
              </div>
            </div>
            <div class="card-chart">
              <canvas data-chart="line" data-animation="false"
                      data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]'
                      data-values='[{"backgroundColor": "transparent", "borderColor": "#ffffff", "data": [8796, 11317, 8678, 9452, 8453, 11853, 9945]}]'
                      data-scales='{"yAxes": [{ "ticks": {"max": 12742}}]}'
                      data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]'
                      height="35"></canvas>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
          <div class="card bg-info no-border">
            <div class="card-values">
              <div class="p-x">
                <small>Jumlah Karyawan</small>
                <h3 class="card-title fw-l">{{ $data->employees }}</h3>
              </div>
            </div>
            <div class="card-chart">
              <canvas data-chart="line" data-animation="false"
                      data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]'
                      data-values='[{"backgroundColor": "transparent", "borderColor": "#ffffff", "data": [116196, 145160, 124419, 147004, 134740, 120846, 137225]}]'
                      data-scales='{"yAxes": [{ "ticks": {"max": 158029}}]}'
                      data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]'
                      height="35"></canvas>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-md-3">
          <div class="card bg-success no-border">
            <div class="card-values">
              <div class="p-x">
                <small>Tahun Ajar Aktif</small>
                <h3 class="card-title fw-l">{{ $data->school_year }}</h3>
              </div>
            </div>
            <div class="card-chart">
              <canvas data-chart="line" data-animation="false"
                      data-labels='["Jun 21", "Jun 20", "Jun 19", "Jun 18", "Jun 17", "Jun 16", "Jun 15"]'
                      data-values='[{"backgroundColor": "transparent", "borderColor": "#ffffff", "data": [13590442, 12362934, 13639564, 13055677, 12915203, 11009940, 11542408]}]'
                      data-scales='{"yAxes": [{ "ticks": {"max": 14662531}}]}'
                      data-hide='["legend", "points", "scalesX", "scalesY", "tooltips"]'
                      height="35"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
    @if(is_null(configuration()))
      toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-full-width",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "10000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    };
    toastr.info("Silahkan isi data sekolah di menu pengaturan lalu di konfigurasi aplikasi.");
    @endif
  </script>
@endpush
